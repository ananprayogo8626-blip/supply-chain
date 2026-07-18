<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\ImportProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateRiskScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'risk_scores',
            'processed' => 0,
            'total' => Country::count(),
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle()
    {
        try {
            Log::info("RiskScoreEngine: [Risk Started]");
            $this->progress->update(['status' => 'processing']);

            $successCount = 0;
            $errorCount = 0;
            $processedCount = 0;

            // Process countries in chunks of 25 to prevent timeout
            Country::chunk(25, function ($countries) use (&$successCount, &$errorCount, &$processedCount) {
                foreach ($countries as $country) {
                    try {
                        $processedCount++;

                        // Calculate risk score for this country
                        $score = app(\App\Services\RiskScoreEngine::class)->calculate($country);

                        if ($score) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }

                        $this->updateProgress($processedCount, $successCount, $errorCount);

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("CalculateRiskScoresJob: Error processing country {$country->id}: " . $e->getMessage(), [
                            'exception' => $e
                        ]);
                        $this->updateProgress($processedCount, $successCount, $errorCount);
                        continue;
                    }
                }
            });

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

            \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');

            Log::info("CalculateRiskScoresJob completed: {$successCount} countries processed, {$errorCount} errors");
            Log::info("RiskScoreEngine: [Risk Finished]");

        } catch (\Throwable $e) {
            Log::error("CalculateRiskScoresJob error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->progress->update([
                'status' => 'failed',
                'finished_at' => now(),
            ]);
        }
    }

    protected function updateProgress($processed, $success, $error)
    {
        $progress = ($processed / $this->progress->total) * 100;
        $this->progress->update([
            'processed' => $processed,
            'percentage' => round($progress, 2),
        ]);
    }
}
