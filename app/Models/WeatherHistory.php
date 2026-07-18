<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherHistory extends Model
{
    protected $table = 'weather_histories';

    protected $fillable = [
        'country_id',
        'temperature',
        'wind_speed',
        'rainfall',
        'humidity',
        'cloud',
        'pressure',
        'weather_condition',
        'storm_risk',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'wind_speed' => 'float',
        'rainfall' => 'float',
        'humidity' => 'float',
        'cloud' => 'float',
        'pressure' => 'float',
        'storm_risk' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
