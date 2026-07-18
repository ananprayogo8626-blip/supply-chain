<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeatherData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'weather_data';

    protected $fillable = [
        'country_id',
        'temperature',
        'wind_speed',
        'rainfall',
        'humidity',
        'cloud',
        'pressure',
        'weather_condition',
        'weather_code',
        'storm_risk',
    ];

    protected $casts = [
        'temperature' => 'float',
        'wind_speed' => 'float',
        'rainfall' => 'float',
        'humidity' => 'float',
        'cloud' => 'float',
        'pressure' => 'float',
        'storm_risk' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}