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

class SyncPortsJob implements ShouldQueue
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
            Log::info("SyncPortsJob started: Batch {$this->batchNumber}/{$this->totalBatches}");
            
            // Update stage to ports
            $progress = SyncProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->stage = 'ports';
                $progress->save();
            }
            
            $exitCode = Artisan::call('sync:ports');

            if ($exitCode !== 0) {
                Log::warning("SyncPortsJob failed: Batch {$this->batchNumber} exited with code {$exitCode}");
            }

            if ($progress) {
                $progress->updateProgress(
                    $progress->processed_countries + 25,
                    $this->batchNumber
                );
                
                // Check if this is the last batch of ports stage - dispatch next stage
                if ($this->batchNumber === $this->totalBatches) {
                    $progress->stage = 'news';
                    $progress->save();
                    
                    // Dispatch News jobs
                    for ($i = 1; $i <= $this->totalBatches; $i++) {
                        SyncNewsJob::dispatch($this->batchId, $i, $this->totalBatches);
                    }
                    Log::info("Dispatched News jobs for batch {$this->batchId}");
                }
            }

            Log::info("SyncPortsJob completed: Batch {$this->batchNumber}/{$this->totalBatches}");
        } catch (\Exception $e) {
            Log::error("SyncPortsJob error: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncPortsJob failed permanently: " . $exception->getMessage());
        
        // Mark progress as failed
        $progress = SyncProgress::where('batch_id', $this->batchId)->first();
        if ($progress) {
            $progress->markAsFailed('Ports sync failed: ' . $exception->getMessage());
        }
    }
}
