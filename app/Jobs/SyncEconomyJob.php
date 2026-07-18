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

class SyncEconomyJob implements ShouldQueue
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
            Log::info("SyncEconomyJob started: Batch {$this->batchNumber}/{$this->totalBatches}");
            
            // Update stage to economy
            $progress = SyncProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->stage = 'economy';
                $progress->save();
            }
            
            $exitCode = Artisan::call('sync:economy', [
                '--batch' => $this->batchNumber,
                '--total-batches' => $this->totalBatches,
            ]);

            if ($exitCode !== 0) {
                Log::warning("SyncEconomyJob failed: Batch {$this->batchNumber} exited with code {$exitCode}");
            }

            if ($progress) {
                $progress->updateProgress(
                    $progress->processed_countries + 25,
                    $this->batchNumber
                );
                
                // Check if this is the last batch of economy stage - dispatch next stage
                if ($this->batchNumber === $this->totalBatches) {
                    $progress->stage = 'currency';
                    $progress->save();
                    
                    // Dispatch Currency jobs
                    for ($i = 1; $i <= $this->totalBatches; $i++) {
                        SyncCurrencyJob::dispatch($this->batchId, $i, $this->totalBatches);
                    }
                    Log::info("Dispatched Currency jobs for batch {$this->batchId}");
                }
            }

            Log::info("SyncEconomyJob completed: Batch {$this->batchNumber}/{$this->totalBatches}");
        } catch (\Exception $e) {
            Log::error("SyncEconomyJob error: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncEconomyJob failed permanently: " . $exception->getMessage());
        
        // Mark progress as failed
        $progress = SyncProgress::where('batch_id', $this->batchId)->first();
        if ($progress) {
            $progress->markAsFailed('Economy sync failed: ' . $exception->getMessage());
        }
    }
}
