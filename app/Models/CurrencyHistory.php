<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyHistory extends Model
{
    protected $table = 'currency_histories';

    protected $fillable = [
        'country_id',
        'currency_code',
        'exchange_rate',
        'change_percentage',
        'recorded_at',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'change_percentage' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
