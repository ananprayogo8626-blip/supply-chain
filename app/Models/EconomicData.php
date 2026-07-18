<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'economic_data';

    protected $fillable = [
        'country_id',
        'gdp',
        'gdp_growth',
        'inflation',
        'exports',
        'imports',
        'trade_balance',
        'population',
        'data_year',
    ];

    protected $casts = [
        'gdp' => 'float',
        'gdp_growth' => 'float',
        'inflation' => 'float',
        'exports' => 'float',
        'imports' => 'float',
        'trade_balance' => 'float',
        'population' => 'integer',
        'data_year' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}