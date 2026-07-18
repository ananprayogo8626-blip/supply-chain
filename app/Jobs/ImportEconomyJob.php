<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\EconomicData;
use App\Models\ImportProgress;
use App\Services\WorldBankService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportEconomyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'economy',
            'processed' => 0,
            'total' => Country::count(),
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle(WorldBankService $worldBank)
    {
        try {
            $this->progress->update(['status' => 'processing']);

            $successCount = 0;
            $errorCount = 0;
            $skippedCount = 0;
            $processedCount = 0;

            Log::info("ImportEconomyJob: Starting economy import for {$this->progress->total} countries");

            // Process countries in chunks of 20
            Country::chunk(20, function ($countries) use ($worldBank, &$successCount, &$errorCount, &$skippedCount, &$processedCount) {
                foreach ($countries as $country) {
                    try {
                        $processedCount++;

                        // Use retry for individual country processing
                        $gdp = retry(2, function() use ($worldBank, $country) {
                            return $worldBank->getIndicator($country->country_code, 'NY.GDP.MKTP.CD');
                        }, 500);
                        
                        $inflation = retry(2, function() use ($worldBank, $country) {
                            return $worldBank->getIndicator($country->country_code, 'FP.CPI.TOTL.ZG');
                        }, 500);
                        
                        $exports = retry(2, function() use ($worldBank, $country) {
                            return $worldBank->getIndicator($country->country_code, 'NE.EXP.GNFS.CD');
                        }, 500);
                        
                        $imports = retry(2, function() use ($worldBank, $country) {
                            return $worldBank->getIndicator($country->country_code, 'NE.IMP.GNFS.CD');
                        }, 500);
                        
                        $population = retry(2, function() use ($worldBank, $country) {
                            return $worldBank->getIndicator($country->country_code, 'SP.POP.TOTL');
                        }, 500);

                        // Calculate GDP growth if we have GDP data
                        $gdpGrowth = null;
                        if ($gdp && isset($gdp['value'])) {
                            // Try to get previous year's GDP for growth calculation
                            $prevGdp = retry(2, function() use ($worldBank, $country) {
                                return $worldBank->getIndicator($country->country_code, 'NY.GDP.MKTP.CD');
                            }, 500);
                            
                            if ($prevGdp && isset($prevGdp['value']) && $prevGdp['value'] > 0) {
                                $gdpGrowth = (($gdp['value'] - $prevGdp['value']) / $prevGdp['value']) * 100;
                            }
                        }

                        // Check if we have any data at all
                        if (!$gdp && !$inflation && !$exports && !$imports && !$population) {
                            $skippedCount++;
                            Log::warning("ImportEconomyJob: Skipped country {$country->country_name} ({$country->country_code}) - no data available");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        // Use transaction to ensure data integrity
                        \Illuminate\Support\Facades\DB::transaction(function() use ($country, $gdp, $inflation, $exports, $imports, $population, $gdpGrowth) {
                            $existing = EconomicData::where('country_id', $country->id)->first();
                            if ($existing) {
                                \App\Models\EconomicHistory::create([
                                    'country_id' => $country->id,
                                    'gdp' => $existing->gdp,
                                    'gdp_growth' => $existing->gdp_growth,
                                    'inflation' => $existing->inflation,
                                    'exports' => $existing->exports,
                                    'imports' => $existing->imports,
                                    'population' => $existing->population,
                                    'data_year' => $existing->data_year,
                                    'recorded_at' => $existing->updated_at ?? now(),
                                ]);
                            }

                            EconomicData::updateOrCreate(
                                [
                                    'country_id' => $country->id,
                                ],
                                [
                                    'gdp' => $gdp['value'] ?? ($existing ? $existing->gdp : null),
                                    'gdp_growth' => $gdpGrowth ?? ($existing ? $existing->gdp_growth : null),
                                    'inflation' => $inflation['value'] ?? ($existing ? $existing->inflation : null),
                                    'exports' => $exports['value'] ?? ($existing ? $existing->exports : null),
                                    'imports' => $imports['value'] ?? ($existing ? $existing->imports : null),
                                    'population' => $population['value'] ?? ($existing ? $existing->population : null),
                                    'data_year' => $gdp['year'] ?? ($existing ? $existing->data_year : date('Y')),
                                ]
                            );

                            // Calculate risk score for this country
                            app(\App\Services\RiskScoreEngine::class)->calculate($country);
                        });

                        $successCount++;
                        Log::info("ImportEconomyJob: Successfully processed economy for {$country->country_name} ({$country->country_code}) - GDP: " . ($gdp['value'] ?? 'N/A') . ", Year: " . ($gdp['year'] ?? date('Y')));
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportEconomyJob: Error processing country {$country->country_name} ({$country->country_code}): " . $e->getMessage(), [
                            'exception' => $e,
                            'country_id' => $country->id
                        ]);
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                        continue;
                    }
                }
            });

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

            \App\Jobs\CalculateRiskScoresJob::dispatch();

            Log::info("ImportEconomyJob: Economy import completed. Success: {$successCount}, Errors: {$errorCount}, Skipped: {$skippedCount}");

        } catch (\Throwable $e) {
            Log::error("ImportEconomyJob error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->progress->update([
                'status' => 'failed',
                'finished_at' => now(),
            ]);
        }
    }

    protected function updateProgress($processed, $success, $error, $skipped)
    {
        $progress = ($processed / $this->progress->total) * 100;
        $this->progress->update([
            'processed' => $processed,
            'percentage' => round($progress, 2),
        ]);
    }
}
