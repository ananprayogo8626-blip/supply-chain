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

            // Process countries in chunks of 20
            Country::chunk(20, function ($countries) use ($worldBank, &$successCount, &$errorCount, &$skippedCount, &$processedCount) {
                foreach ($countries as $country) {
                    try {
                        $processedCount++;

                        $gdp = $worldBank->getIndicator($country->country_code, 'NY.GDP.MKTP.CD');
                        $inflation = $worldBank->getIndicator($country->country_code, 'FP.CPI.TOTL.ZG');
                        $exports = $worldBank->getIndicator($country->country_code, 'NE.EXP.GNFS.CD');
                        $imports = $worldBank->getIndicator($country->country_code, 'NE.IMP.GNFS.CD');
                        $population = $worldBank->getIndicator($country->country_code, 'SP.POP.TOTL');

                        if (!$gdp && !$inflation && !$exports && !$imports && !$population) {
                            $skippedCount++;
                            Log::warning("ImportEconomyJob: Skipped country {$country->id} - no data available");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        EconomicData::updateOrCreate(
                            [
                                'country_id' => $country->id,
                            ],
                            [
                                'gdp' => $gdp['value'] ?? 0,
                                'inflation' => $inflation['value'] ?? 0,
                                'exports' => $exports['value'] ?? 0,
                                'imports' => $imports['value'] ?? 0,
                                'population' => $population['value'] ?? 0,
                                'data_year' => $gdp['year'] ?? date('Y'),
                            ]
                        );

                        // Calculate risk score for this country
                        app(\App\Services\RiskScoreEngine::class)->calculate($country);

                        $successCount++;
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportEconomyJob: Error processing country {$country->id}: " . $e->getMessage(), [
                            'exception' => $e
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
