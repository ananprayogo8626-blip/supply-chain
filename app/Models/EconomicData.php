<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicData extends Model
{
    use HasFactory;

    protected $table = 'economic_data';

    protected $fillable = [
        'country_id',
        'gdp',
        'gdp_growth',
        'inflation',
        'exports',
        'imports',
        'population',
        'data_year',
    ];

    protected $casts = [
        'gdp' => 'float',
        'gdp_growth' => 'float',
        'inflation' => 'float',
        'exports' => 'float',
        'imports' => 'float',
        'population' => 'integer',
        'data_year' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}