<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Port extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'ports';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'country_id',
        'port_name',
        'port_code',
        'unlocode',
        'city',
        'latitude',
        'longitude',
        'port_type',
        'status',
        'description',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Relasi ke Country
     * Satu pelabuhan dimiliki oleh satu negara
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}