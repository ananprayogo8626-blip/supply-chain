<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ExchangeRate API Service (v6 Official)
 * Documentation: https://www.exchangerate-api.com/docs/standard-requests
 */
class ExchangeRateService
{
    protected ?string $apiKey = null;
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.key') ?? env('EXCHANGERATE_API_KEY') ?? '';
    }

    /**
     * Fetch all conversion rates from base currency (default USD).
     * Normalizes the v6 'conversion_rates' key into 'rates' for compatibility.
     *
     * @param string $baseCurrency Default USD
     * @return array|null
     */
    public function getRates(string $baseCurrency = 'USD'): ?array
    {
        if (empty($this->apiKey)) {
            Log::error("ExchangeRateService: [API Error] API Key is not configured in services.exchangerate.key");
            return null;
        }

        $cacheKey = "exchangerate_{$baseCurrency}";
        $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = $this->fetchRates($baseCurrency);

        if ($result !== null) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $result, now()->addMinutes(30));
        }

        return $result;
    }

    protected function fetchRates(string $baseCurrency): ?array
    {
        try {
            Log::info("ExchangeRateService: [Sync Started] Fetching exchange rates for base currency {$baseCurrency}");

            $url = "{$this->baseUrl}/{$this->apiKey}/latest/{$baseCurrency}";
            
            $response = retry(3, function() use ($url) {
                return Http::timeout(20)->get($url);
            }, 1000);

            if (!$response->successful()) {
                Log::error("ExchangeRateService: [API Error] HTTP request failed with status: " . $response->status());
                Log::error("ExchangeRateService: [Sync Failed] Exchange rates fetch failed for base {$baseCurrency}");
                return null;
            }

            $data = $response->json();

            if (($data['result'] ?? '') !== 'success' || (!isset($data['conversion_rates']) && !isset($data['rates']))) {
                Log::error("ExchangeRateService: [API Error] Invalid API response structure: " . json_encode($data));
                Log::error("ExchangeRateService: [Sync Failed] Exchange rates fetch failed for base {$baseCurrency}");
                return null;
            }

            $rates = $data['conversion_rates'] ?? $data['rates'] ?? [];

            $result = [
                'base'       => $data['base_code'] ?? $baseCurrency,
                'rates'      => $rates,
                'updated_at' => now(),
            ];

            Log::info("ExchangeRateService: [Sync Finished] Successfully retrieved rates, " . count($rates) . " currencies available");

            return $result;

        } catch (\Throwable $e) {
            Log::error("ExchangeRateService: [API Error] Exception occurred: " . $e->getMessage(), [
                'exception' => $e
            ]);
            Log::error("ExchangeRateService: [Sync Failed] Exchange rates fetch failed for base {$baseCurrency}");
            return null;
        }
    }

    /**
     * Get specific currency exchange rate.
     *
     * @param string $currencyCode Target currency code (e.g. IDR, EUR)
     * @param string $baseCurrency Default USD
     * @return float|null
     */
    public function getRate(string $currencyCode, string $baseCurrency = 'USD'): ?float
    {
        $currencyCode = strtoupper(trim($currencyCode));
        if (!preg_match('/^[A-Z]{3}$/', $currencyCode)) {
            Log::warning("ExchangeRateService: Invalid currency code format: {$currencyCode}");
            return null;
        }

        $data = $this->getRates($baseCurrency);

        if (!$data || !isset($data['rates'][$currencyCode])) {
            Log::warning("ExchangeRateService: Currency code {$currencyCode} not found in rates for base {$baseCurrency}");
            return null;
        }

        return (float) $data['rates'][$currencyCode];
    }
}
