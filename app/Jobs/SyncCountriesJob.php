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

class SyncCountriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchId;
    protected $batchNumber;
    protected $totalBatches;

    public $timeout = 180; // 3 minutes per batch
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $batchId, int $batchNumber, int $totalBatches)
    {
        $this->batchId = $batchId;
        $this->batchNumber = $batchNumber;
        $this->totalBatches = $totalBatches;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("SyncCountriesJob started: Batch {$this->batchNumber}/{$this->totalBatches}");
            
            // Update stage to countries
            $progress = SyncProgress::where('batch_id', $this->batchId)->first();
            if ($progress) {
                $progress->stage = 'countries';
                $progress->save();
            }
            
            $exitCode = Artisan::call('sync:countries', [
                '--batch' => $this->batchNumber,
                '--total-batches' => $this->totalBatches,
            ]);

            if ($exitCode !== 0) {
                Log::warning("SyncCountriesJob failed: Batch {$this->batchNumber} exited with code {$exitCode}");
            }

            // Update progress
            if ($progress) {
                $progress->updateProgress(
                    $progress->processed_countries + 25,
                    $this->batchNumber
                );
                
                // Check if this is the last batch of countries stage - dispatch next stage
                if ($this->batchNumber === $this->totalBatches) {
                    $progress->stage = 'weather';
                    $progress->save();
                    
                    // Dispatch Weather jobs
                    for ($i = 1; $i <= $this->totalBatches; $i++) {
                        SyncWeatherJob::dispatch($this->batchId, $i, $this->totalBatches);
                    }
                    Log::info("Dispatched Weather jobs for batch {$this->batchId}");
                }
            }

            Log::info("SyncCountriesJob completed: Batch {$this->batchNumber}/{$this->totalBatches}");
        } catch (\Exception $e) {
            Log::error("SyncCountriesJob error: " . $e->getMessage(), [
                'batch_id' => $this->batchId,
                'batch_number' => $this->batchNumber,
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncCountriesJob failed permanently: " . $exception->getMessage());
        
        // Mark progress as failed
        $progress = SyncProgress::where('batch_id', $this->batchId)->first();
        if ($progress) {
            $progress->markAsFailed('Countries sync failed: ' . $exception->getMessage());
        }
    }
}
