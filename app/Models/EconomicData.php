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
        'inflation',
        'exports',
        'imports',
        'population',
        'data_year',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}