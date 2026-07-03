<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    protected $table = 'weather_data';

    protected $fillable = [
        'country_id',
        'temperature',
        'wind_speed',
        'rainfall',
        'humidity',
        'weather_condition',
        'storm_risk',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}