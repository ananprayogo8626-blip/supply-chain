<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\SyncProgress;
use App\Jobs\SyncCountriesJob;
use App\Jobs\SyncWeatherJob;
use App\Jobs\SyncEconomyJob;
use App\Jobs\SyncCurrencyJob;
use App\Jobs\SyncNewsJob;

class SyncController extends Controller
{
    /**
     * Synchronize ALL data from external APIs using Queue Jobs:
     * Countries, Weather, Economy, Currency, News, Ports, Risk Scores.
     *
     * Called via the "Sync All API Data" button on the dashboard.
     */
    public function syncAll(Request $request)
    {
        try {
            // Generate unique batch ID
            $batchId = 'sync_' . uniqid();
            
            // Calculate total batches (25 countries per batch)
            $totalCountries = \App\Models\Country::count();
            $totalBatches = max(1, ceil($totalCountries / 25));
            
            // Create progress tracker
            $progress = SyncProgress::create([
                'batch_id' => $batchId,
                'stage' => 'countries',
                'total_countries' => $totalCountries,
                'processed_countries' => 0,
                'current_batch' => 0,
                'total_batches' => $totalBatches,
                'progress_percentage' => 0,
                'status' => 'processing',
                'started_at' => now(),
            ]);
            
            // Dispatch jobs in stages
            $this->dispatchStagedJobs($batchId, $totalBatches);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Sync process started successfully',
                'batch_id' => $batchId,
                'total_batches' => $totalBatches * 5, // 5 stages
            ]);
        } catch (\Throwable $e) {
            Log::error("syncAll: Error starting sync: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to start sync: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Dispatch jobs in stages: Countries -> Weather -> Economy -> Currency -> News
     * Each stage is dispatched after the previous stage completes
     */
    private function dispatchStagedJobs(string $batchId, int $totalBatches): void
    {
        // Stage 1: Countries (dispatch immediately)
        for ($i = 1; $i <= $totalBatches; $i++) {
            SyncCountriesJob::dispatch($batchId, $i, $totalBatches);
        }
        
        // Stage 2-5 will be dispatched by the last batch of each stage
        // This ensures sequential processing
        Log::info("Dispatched Countries jobs for batch {$batchId}");
    }

    /**
     * AJAX Endpoint: Get sync progress
     */
    public function getProgress(Request $request, $batchId)
    {
        try {
            $progress = SyncProgress::where('batch_id', $batchId)->first();
            
            if (!$progress) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Progress not found',
                ], 404);
            }

            // Check for timeout
            if ($progress->status === 'processing' && $progress->hasTimedOut()) {
                $progress->markAsTimedOut();
            }
            
            return response()->json([
                'status' => $progress->status,
                'stage' => $progress->stage,
                'progress_percentage' => $progress->progress_percentage,
                'current_batch' => $progress->current_batch,
                'total_batches' => $progress->total_batches,
                'processed_countries' => $progress->processed_countries,
                'total_countries' => $progress->total_countries,
                'error_message' => $progress->error_message,
            ]);
        } catch (\Throwable $e) {
            Log::error("getProgress: Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get progress',
            ], 500);
        }
    }

    /**
     * AJAX Endpoint: Sync a single step (backward compatibility for existing UI)
     */
    public function syncStep(Request $request, $step)
    {
        try {
            $jobs = [
                'countries' => ['countries:sync', 'Countries Data'],
                'weather'   => ['weather:sync', 'Weather Data'],
                'economy'   => ['economy:sync', 'Economy Data'],
                'currency'  => ['currency:sync', 'Currency Data'],
                'ports'     => ['ports:import', 'Ports Data'],
                'news'      => ['news:sync', 'News Data'],
                'risk'      => ['risk:calculate', 'Risk Scores'],
            ];

            if (!isset($jobs[$step])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid synchronization step: ' . $step,
                ], 400);
            }

            $command = $jobs[$step][0];
            $label = $jobs[$step][1];

            $exitCode = Artisan::call($command);

            if ($exitCode !== 0) {
                Log::warning("syncStep: [{$command}] exited with code {$exitCode}");
                return response()->json([
                    'status'  => 'error',
                    'message' => "Synchronization failed at {$label} (exit code {$exitCode}).",
                ], 500);
            }

            return response()->json([
                'status'  => 'success',
                'message' => "Successfully synchronized {$label}.",
            ]);
        } catch (\Throwable $e) {
            Log::error("syncStep: Error during {$step} sync: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => "Error synchronizing {$step}: " . $e->getMessage(),
            ], 500);
        }
    }
}
