<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProgress extends Model
{
    protected $fillable = [
        'batch_id',
        'stage',
        'total_countries',
        'processed_countries',
        'current_batch',
        'total_batches',
        'progress_percentage',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Calculate overall progress percentage based on stage and batch progress
     */
    public function calculateOverallProgress(): int
    {
        $stageWeights = [
            'countries' => 0,
            'weather' => 20,
            'economy' => 40,
            'currency' => 60,
            'news' => 80,
        ];

        $baseProgress = $stageWeights[$this->stage] ?? 0;
        $stageProgress = ($this->total_batches > 0) 
            ? ($this->current_batch / $this->total_batches) * 20 
            : 0;

        return min(100, (int)($baseProgress + $stageProgress));
    }

    /**
     * Update progress and save
     */
    public function updateProgress(int $processed, int $currentBatch): void
    {
        $this->processed_countries = $processed;
        $this->current_batch = $currentBatch;
        $this->progress_percentage = $this->calculateOverallProgress();
        $this->save();
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->progress_percentage = 100;
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $error): void
    {
        $this->status = 'failed';
        $this->error_message = $error;
        $this->save();
    }

    /**
     * Check if sync has timed out (more than 10 minutes)
     */
    public function hasTimedOut(): bool
    {
        if (!$this->started_at) {
            return false;
        }
        
        $timeoutMinutes = 10;
        return $this->started_at->diffInMinutes(now()) > $timeoutMinutes;
    }

    /**
     * Mark as timed out
     */
    public function markAsTimedOut(): void
    {
        $this->status = 'failed';
        $this->error_message = 'Synchronization timed out after 10 minutes';
        $this->completed_at = now();
        $this->save();
    }
}
