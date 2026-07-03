<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyData extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'currency_data';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'country_id',
        'currency_code',
        'currency_name',
        'base_currency',
        'exchange_rate',
        'change_percentage',
        'last_updated',
    ];

    /**
     * Casting data
     */
    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'change_percentage' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    /**
     * Relasi ke Country
     * Satu data kurs dimiliki oleh satu negara
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}