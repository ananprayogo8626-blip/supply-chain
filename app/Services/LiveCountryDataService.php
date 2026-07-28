<?php

namespace App\Services;

use App\DTO\CurrencySnapshot;
use App\DTO\EconomicSnapshot;
use App\DTO\WeatherSnapshot;
use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Sumber tunggal data weather/economy/currency real-time (tanpa persistensi DB),
 * dipakai oleh semua controller yang perlu menampilkan data ini untuk satu atau
 * beberapa negara (Country show/dashboard, comparison, watchlist, risk score, map).
 */
class LiveCountryDataService
{
    public function __construct(
        protected OpenMeteoService $weatherService,
        protected WorldBankService $worldBankService,
        protected ExchangeRateService $exchangeRateService,
    ) {
    }

    /**
     * Timpa relasi weatherData/economicData/currencyData milik $country dengan
     * snapshot live API (DTO, bukan Eloquent). Kalau live fetch gagal, relasi
     * yang sebelumnya di-set (kalau ada) dibiarkan apa adanya.
     */
    public function attachLiveData(Country $country): void
    {
        if ($weather = $this->getWeather($country)) {
            $country->setRelation('weatherData', WeatherSnapshot::fromArray($weather));
        }

        if ($economy = $this->getEconomy($country)) {
            $country->setRelation('economicData', EconomicSnapshot::fromArray($economy));
        }

        if ($currency = $this->getCurrency($country)) {
            $country->setRelation('currencyData', CurrencySnapshot::fromArray($currency));
        }
    }

    public function getWeather(Country $country): ?array
    {
        if (!$country->latitude || !$country->longitude) {
            return null;
        }

        $cacheKey = "weather_live_{$country->id}";
        return Cache::remember($cacheKey, 1800, function() use ($country) {
            return $this->weatherService->getWeather((float) $country->latitude, (float) $country->longitude);
        });
    }

    public function getEconomy(Country $country): ?array
    {
        if (!$country->country_code) {
            return null;
        }

        $cacheKey = "economy_live_{$country->country_code}";
        return Cache::remember($cacheKey, 3600, function() use ($country) {
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
        });
    }

    /**
     * Deret waktu multi-tahun untuk GDP Trend / Inflation Trend chart, langsung dari
     * World Bank API (tidak perlu tabel histori sendiri).
     */
    public function getEconomyHistory(Country $country, string $indicator, int $years = 10): array
    {
        $toYear = (int) now()->year - 1; // data World Bank umumnya lag 1 tahun
        $fromYear = $toYear - $years;

        return $this->worldBankService->getIndicatorSeries($country->country_code, $indicator, $fromYear, $toYear);
    }

    public function getCurrency(Country $country): ?array
    {
        $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
        $currencyCode = strtoupper($currencyCodes[0] ?? '');

        // Currency mapping & fallback for special territory ISO codes
        if ($currencyCode === 'CKD') $currencyCode = 'NZD'; // Cook Islands Dollar pegged to NZD
        if ($currencyCode === 'CUC') $currencyCode = 'CUP'; // Cuban Convertible Peso fallback
        if (empty($currencyCode) || in_array($currencyCode, ['N/A', 'NONE', 'EMPTY'])) {
            $currencyCode = 'USD'; // Default fallback for territories without central bank ISO codes
        }

        $rate = $this->exchangeRateService->getRate($currencyCode, 'USD');

        // Fallback for non-traded currencies
        if ($rate === null) {
            $rate = 1.0;
        }

        $changePercentage = 0.0;
        $prevCacheKey = "currency_prev_rate_{$country->id}";
        $previous = Cache::get($prevCacheKey);

        if ($previous !== null && $previous > 0) {
            $changePercentage = (($rate - $previous) / $previous) * 100;
        }

        Cache::put($prevCacheKey, $rate, now()->addDays(7));

        return [
            'currency_code' => $currencyCode,
            'currency_name' => $currencyCode,
            'base_currency' => 'USD',
            'exchange_rate' => $rate,
            'change_percentage' => round($changePercentage, 2),
            'last_updated' => now(),
        ];
    }
}
