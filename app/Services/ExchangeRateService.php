<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * ExchangeRate API Service
 * Gratis tier: https://www.exchangerate-api.com
 * Tanpa API key: https://open.er-api.com/v6/latest/USD (open access)
 */
class ExchangeRateService
{
    protected $baseUrl = 'https://open.er-api.com/v6/latest';

    /**
     * Ambil semua kurs dari base currency (default USD)
     *
     * @param string $baseCurrency Default USD
     * @return array|null
     */
    public function getRates(string $baseCurrency = 'USD'): ?array
    {
        try {
            $response = retry(2, function() use ($baseCurrency) {
                return Http::timeout(15)->get("{$this->baseUrl}/{$baseCurrency}");
            }, 500);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (($data['result'] ?? '') !== 'success' || !isset($data['rates'])) {
                return null;
            }

            return [
                'base'       => $data['base_code'] ?? $baseCurrency,
                'rates'      => $data['rates'],
                'updated_at' => now(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Ambil kurs untuk 1 currency spesifik
     *
     * @param string $currencyCode Kode mata uang (misal: IDR, EUR)
     * @param string $baseCurrency Default USD
     * @return float|null
     */
    public function getRate(string $currencyCode, string $baseCurrency = 'USD'): ?float
    {
        $data = $this->getRates($baseCurrency);

        if (!$data || !isset($data['rates'][$currencyCode])) {
            return null;
        }

        return (float) $data['rates'][$currencyCode];
    }
}
