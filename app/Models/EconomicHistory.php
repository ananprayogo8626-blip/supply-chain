<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicHistory extends Model
{
    use HasFactory;

    protected $table = 'economic_histories';

    protected $fillable = [
        'country_id',
        'gdp',
        'gdp_growth',
        'inflation',
        'exports',
        'imports',
        'population',
        'data_year',
        'recorded_at',
    ];

    protected $casts = [
        'gdp' => 'float',
        'gdp_growth' => 'float',
        'inflation' => 'float',
        'exports' => 'float',
        'imports' => 'float',
        'population' => 'integer',
        'data_year' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
