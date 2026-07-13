<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\CurrencyData;
use App\Models\ImportProgress;
use App\Services\ExchangeRateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCurrencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'currency',
            'processed' => 0,
            'total' => Country::count(),
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle(ExchangeRateService $service)
    {
        try {
            $this->progress->update(['status' => 'processing']);

            $successCount = 0;
            $errorCount = 0;
            $skippedCount = 0;
            $processedCount = 0;

            // Process countries in chunks of 20
            Country::chunk(20, function ($countries) use ($service, &$successCount, &$errorCount, &$skippedCount, &$processedCount) {
                foreach ($countries as $country) {
                    try {
                        $processedCount++;

                        $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
                        $currencyCode = $currencyCodes[0] ?? null;

                        if (!$currencyCode) {
                            $skippedCount++;
                            Log::warning("ImportCurrencyJob: Skipped country {$country->id} - no currency code");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        $rate = $service->getRate($currencyCode, 'USD');

                        if ($rate === null) {
                            $errorCount++;
                            Log::error("ImportCurrencyJob: Failed to get rate for currency {$currencyCode}");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        $existing = CurrencyData::where('country_id', $country->id)
                            ->where('currency_code', $currencyCode)
                            ->first();

                        $oldRate = $existing ? (float) $existing->exchange_rate : 0.0;
                        $changePercentage = 0.0;
                        if ($oldRate > 0) {
                            $changePercentage = (($rate - $oldRate) / $oldRate) * 100;
                        }

                        CurrencyData::updateOrCreate(
                            [
                                'country_id' => $country->id,
                                'currency_code' => $currencyCode,
                            ],
                            [
                                'currency_name' => $currencyCode,
                                'base_currency' => 'USD',
                                'exchange_rate' => $rate,
                                'change_percentage' => $changePercentage,
                                'last_updated' => now(),
                            ]
                        );

                        // Calculate risk score for this country
                        app(\App\Services\RiskScoreEngine::class)->calculate($country);

                        $successCount++;
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportCurrencyJob: Error processing country {$country->id}: " . $e->getMessage(), [
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
            Log::error("ImportCurrencyJob error: " . $e->getMessage(), [
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
