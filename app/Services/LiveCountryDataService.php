<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CurrencyData;
use Illuminate\Support\Facades\Log;

/**
 * Mengambil data weather/economy/currency real-time dari service API yang sudah ada,
 * untuk menggantikan tampilan data DB tanpa mengubah skema atau pipeline sync.
 * Mengembalikan null bila API gagal, sehingga caller jatuh ke data DB sebagai fallback.
 */
class LiveCountryDataService
{
    public function __construct(
        protected OpenMeteoService $weatherService,
        protected WorldBankService $worldBankService,
        protected ExchangeRateService $exchangeRateService,
    ) {
    }

    public function getWeather(Country $country): ?array
    {
        if (!$country->latitude || !$country->longitude) {
            return null;
        }

        return $this->weatherService->getWeather((float) $country->latitude, (float) $country->longitude);
    }

    public function getEconomy(Country $country): ?array
    {
        try {
            $gdp = $this->worldBankService->getIndicator($country->country_code, 'NY.GDP.MKTP.CD');

            if (!$gdp || $gdp['value'] === null) {
                return null;
            }

            $growth = $this->worldBankService->getIndicator($country->country_code, 'NY.GDP.MKTP.KD.ZG');
            $inflation = $this->worldBankService->getIndicator($country->country_code, 'FP.CPI.TOTL.ZG');
            $exports = $this->worldBankService->getIndicator($country->country_code, 'NE.EXP.GNFS.CD');
            $imports = $this->worldBankService->getIndicator($country->country_code, 'NE.IMP.GNFS.CD');
            $population = $this->worldBankService->getIndicator($country->country_code, 'SP.POP.TOTL');

            $exportsVal = $exports['value'] ?? 0;
            $importsVal = $imports['value'] ?? 0;

            return [
                'gdp' => $gdp['value'],
                'gdp_growth' => $growth['value'] ?? null,
                'inflation' => $inflation['value'] ?? null,
                'exports' => $exportsVal,
                'imports' => $importsVal,
                'trade_balance' => $exportsVal - $importsVal,
                'population' => $population['value'] ?? $country->population,
                'data_year' => $gdp['year'],
            ];
        } catch (\Throwable $e) {
            Log::error("LiveCountryDataService: getEconomy failed for {$country->country_code}: " . $e->getMessage());
            return null;
        }
    }

    public function getCurrency(Country $country, ?CurrencyData $existingDbRow): ?array
    {
        $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
        $currencyCode = $currencyCodes[0] ?? null;

        if (!$currencyCode) {
            return null;
        }

        $rate = $this->exchangeRateService->getRate($currencyCode, 'USD');

        if ($rate === null) {
            return null;
        }

        $changePercentage = 0.0;
        if ($existingDbRow && $existingDbRow->exchange_rate) {
            $previous = (float) $existingDbRow->exchange_rate;
            if ($previous > 0) {
                $changePercentage = (($rate - $previous) / $previous) * 100;
            }
        }

        return [
            'currency_code' => $currencyCode,
            'currency_name' => $existingDbRow->currency_name ?? $currencyCode,
            'base_currency' => 'USD',
            'exchange_rate' => $rate,
            'change_percentage' => $changePercentage,
            'last_updated' => now(),
        ];
    }
}
