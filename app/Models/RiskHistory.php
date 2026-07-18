<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'total_score',
        'risk_level',
        'calculated_at',
    ];

    protected $casts = [
        'total_score' => 'integer',
        'calculated_at' => 'datetime',
    ];

    /**
     * Relationship with country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope to get history for a specific country
     */
    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope to get recent history
     */
    public function scopeRecent($query, $limit = 30)
    {
        return $query->orderBy('calculated_at', 'desc')->limit($limit);
    }
}
