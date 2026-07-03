<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'risk_scores';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'country_id',
        'weather_score',
        'economic_score',
        'currency_score',
        'news_score',
        'port_score',
        'total_score',
        'risk_level',
        'recommendation',
        'calculated_at',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'weather_score' => 'integer',
        'economic_score' => 'integer',
        'currency_score' => 'integer',
        'news_score' => 'integer',
        'port_score' => 'integer',
        'total_score' => 'integer',
        'calculated_at' => 'datetime',
    ];

    /**
     * Relasi ke Country
     * Satu Risk Score dimiliki oleh satu Country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Accessor untuk warna badge berdasarkan level risiko
     */
    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'Low' => 'success',
            'Medium' => 'warning',
            'High' => 'danger',
            default => 'secondary',
        };
    }
}