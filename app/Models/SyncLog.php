<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'stage',
        'country_id',
        'country_code',
        'error_message',
        'exception_class',
        'failed_at',
    ];

    protected $casts = [
        'failed_at' => 'datetime',
    ];

    /**
     * Scope to get logs by batch ID
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope to get logs by stage
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Relationship to country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
