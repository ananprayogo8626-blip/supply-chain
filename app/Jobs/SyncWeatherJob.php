<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\SyncProgress;

class SyncWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchId;
    protected $batchNumber;
    protected $totalBatches;

    public $timeout = 180; // 3 minutes per batch
    public $tries = 3;

    public function __construct(string $batchId, int $batchNumber, int $totalBatches)
    {
        $this->batchId = $batchId;
        $this->batchNumber = $batchNumber;
        $this->totalBatches = $totalBatches;
    }

    public function handle(): void
    {
        try {
            Log::info("SyncWeatherJob started: Batch {$this->batchNumber}/{$this->totalBatches}");
            
            // Update stage to weather
            $progress = SyncProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->stage = 'weather';
                $progress->save();
            }
            
            $exitCode = Artisan::call('weather:sync', [
                '--batch' => $this->batchNumber,
                '--total-batches' => $this->totalBatches,
            ]);

            if ($exitCode !== 0) {
                Log::warning("SyncWeatherJob failed: Batch {$this->batchNumber} exited with code {$exitCode}");
            }

            if ($progress) {
                $progress->updateProgress(
                    $progress->processed_countries + 25,
                    $this->batchNumber
                );
                
                // Check if this is the last batch of weather stage - dispatch next stage
                if ($this->batchNumber === $this->totalBatches) {
                    $progress->stage = 'economy';
                    $progress->save();
                    
                    // Dispatch Economy jobs
                    for ($i = 1; $i <= $this->totalBatches; $i++) {
                        SyncEconomyJob::dispatch($this->batchId, $i, $this->totalBatches);
                    }
                    Log::info("Dispatched Economy jobs for batch {$this->batchId}");
                }
            }

            Log::info("SyncWeatherJob completed: Batch {$this->batchNumber}/{$this->totalBatches}");
        } catch (\Exception $e) {
            Log::error("SyncWeatherJob error: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncWeatherJob failed permanently: " . $exception->getMessage());
        
        // Mark progress as failed
        $progress = SyncProgress::where('batch_id', $this->batchId)->first();
        if ($progress) {
            $progress->markAsFailed('Weather sync failed: ' . $exception->getMessage());
        }
    }
}
