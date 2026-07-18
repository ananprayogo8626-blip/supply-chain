<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyData extends Model
{
    use SoftDeletes;
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
        'exchange_rate' => 'float',
        'change_percentage' => 'float',
        'last_updated' => 'datetime',
    ];

    /**
     * Appended dynamic attributes
     */
    protected $appends = ['buy', 'sell', 'symbol'];

    /**
     * Accessor for currency symbol
     */
    public function getSymbolAttribute()
    {
        $code = strtoupper($this->currency_code);
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'IDR' => 'Rp',
            'CNY' => '¥', 'SGD' => 'S$', 'AUD' => 'A$', 'CAD' => 'C$', 'INR' => '₹',
            'MYR' => 'RM', 'PHP' => '₱', 'THB' => '฿', 'VND' => '₫', 'KRW' => '₩',
            'RUB' => '₽', 'BRL' => 'R$', 'TRY' => '₺', 'ZAR' => 'R', 'MXN' => '$',
            'AED' => 'د.إ', 'SAR' => 'ر.س', 'EGP' => 'E£', 'PKR' => '₨', 'BDT' => '৳',
            'HKD' => 'HK$', 'TWD' => 'NT$', 'NZD' => 'NZ$', 'CHF' => 'Fr', 'SEK' => 'kr',
            'NOK' => 'kr', 'DKK' => 'kr', 'PLN' => 'zł', 'HUF' => 'Ft', 'CZK' => 'Kč',
            'ILS' => '₪', 'CLP' => '$', 'COP' => '$', 'PEN' => 'S/.', 'ARS' => '$',
        ];
        return $symbols[$code] ?? $code;
    }

    /**
     * Accessor for dynamic buy rate
     */
    public function getBuyAttribute()
    {
        return (float) $this->exchange_rate * 0.99;
    }

    /**
     * Accessor for dynamic sell rate
     */
    public function getSellAttribute()
    {
        return (float) $this->exchange_rate * 1.01;
    }

    /**
     * Relasi ke Country
     * Satu data kurs dimiliki oleh satu negara
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}