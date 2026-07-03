<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_name',
        'country_code',
        'capital',
        'region',
        'currency',
        'language',
        'flag',
        'population'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Weather
    public function weatherData()
    {
        return $this->hasOne(WeatherData::class);
    }

    // Economy
    public function economicData()
    {
        return $this->hasOne(EconomicData::class);
    }

    // Currency
    public function currencyData()
    {
        return $this->hasOne(CurrencyData::class);
    }

    // Ports
    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    // Risk Score
    public function riskScore()
    {
        return $this->hasOne(RiskScore::class);
    }

    // Watchlist
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    // Articles / News
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}