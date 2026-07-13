<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProgress extends Model
{
    protected $fillable = [
        'service',
        'processed',
        'total',
        'percentage',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
