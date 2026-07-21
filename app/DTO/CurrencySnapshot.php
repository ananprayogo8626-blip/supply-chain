<?php

namespace App\DTO;

/**
 * Plain (non-Eloquent) value object standing in for the old CurrencyData model,
 * so Blade views written against $country->currencyData->{field} keep working
 * with live ExchangeRate API data instead of a persisted row. Replicates the
 * buy/sell/symbol accessors that used to live on the CurrencyData Eloquent model.
 */
class CurrencySnapshot
{
    private const SYMBOLS = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'IDR' => 'Rp',
        'CNY' => '¥', 'SGD' => 'S$', 'AUD' => 'A$', 'CAD' => 'C$', 'INR' => '₹',
        'MYR' => 'RM', 'PHP' => '₱', 'THB' => '฿', 'VND' => '₫', 'KRW' => '₩',
        'RUB' => '₽', 'BRL' => 'R$', 'TRY' => '₺', 'ZAR' => 'R', 'MXN' => '$',
        'AED' => 'د.إ', 'SAR' => 'ر.س', 'EGP' => 'E£', 'PKR' => '₨', 'BDT' => '৳',
        'HKD' => 'HK$', 'TWD' => 'NT$', 'NZD' => 'NZ$', 'CHF' => 'Fr', 'SEK' => 'kr',
        'NOK' => 'kr', 'DKK' => 'kr', 'PLN' => 'zł', 'HUF' => 'Ft', 'CZK' => 'Kč',
        'ILS' => '₪', 'CLP' => '$', 'COP' => '$', 'PEN' => 'S/.', 'ARS' => '$',
    ];

    public readonly float $buy;
    public readonly float $sell;
    public readonly string $symbol;

    public function __construct(
        public readonly ?string $currency_code = null,
        public readonly ?string $currency_name = null,
        public readonly ?string $base_currency = null,
        public readonly ?float $exchange_rate = null,
        public readonly ?float $change_percentage = null,
        public readonly mixed $last_updated = null,
    ) {
        $rate = $this->exchange_rate ?? 0.0;
        $this->buy = $rate * 0.99;
        $this->sell = $rate * 1.01;
        $code = strtoupper($this->currency_code ?? '');
        $this->symbol = self::SYMBOLS[$code] ?? $code;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            currency_code: $data['currency_code'] ?? null,
            currency_name: $data['currency_name'] ?? null,
            base_currency: $data['base_currency'] ?? null,
            exchange_rate: isset($data['exchange_rate']) ? (float) $data['exchange_rate'] : null,
            change_percentage: isset($data['change_percentage']) ? (float) $data['change_percentage'] : null,
            last_updated: $data['last_updated'] ?? now(),
        );
    }
}
