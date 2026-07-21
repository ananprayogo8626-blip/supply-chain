<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\CurrencyData;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;
use Illuminate\Support\Facades\Log;

class RiskScoreEngine
{
    public function __construct(protected LiveCountryDataService $liveDataService)
    {
    }

    /**
     * Calculate and save/update risk score for a country using 5 weighted risk factors.
     * sum of weights: Weather (25%), Economy (20%), Currency (15%), News (25%), Ports (15%).
     *
     * @param Country $country
     * @return RiskScore|null
     */
    public function calculate(Country $country): ?RiskScore
    {
        try {
            Log::info("RiskScoreEngine: [Country Processed] {$country->country_name}");

            $weatherVal  = $this->calculateWeatherScore($country);
            $economyVal  = $this->calculateEconomyScore($country);
            $currencyVal = $this->calculateCurrencyScore($country);
            $newsVal     = $this->calculateNewsScore($country);
            $portVal     = $this->calculatePortScore($country);

            // Compute total weighted score (Weighted Risk Model)
            $totalScore = (int) round(
                ($weatherVal  * 0.25) +
                ($economyVal  * 0.20) +
                ($currencyVal * 0.15) +
                ($newsVal     * 0.25) +
                ($portVal     * 0.15)
            );

            // Risk Level classification:
            // 0 - 25 LOW, 26 - 50 MEDIUM, 51 - 75 HIGH, 76 - 100 CRITICAL
            if ($totalScore >= 76) {
                $riskLevel = 'Critical';
                $recommendation = 'Critical supply chain threat. Immediately establish backup logistics and hedge currency/tariff exposures.';
            } elseif ($totalScore >= 51) {
                $riskLevel = 'High';
                $recommendation = 'High risk warnings. Establish buffer inventory and diversify supplier nodes.';
            } elseif ($totalScore >= 26) {
                $riskLevel = 'Medium';
                $recommendation = 'Medium risk. Monitor weather patterns and track currency fluctuations closely.';
            } else {
                $riskLevel = 'Low';
                $recommendation = 'Stable supply chain environment. Standard operations are sufficient.';
            }

            $dataToUpdate = [
                'weather_score'  => $weatherVal,
                'economic_score' => $economyVal,
                'currency_score' => $currencyVal,
                'news_score'     => $newsVal,
                'port_score'     => $portVal,
                'total_score'    => $totalScore,
                'risk_level'     => $riskLevel,
                'recommendation' => $recommendation,
                'calculated_at'  => now(),
            ];

            $existing = RiskScore::where('country_id', $country->id)->first();

            if ($existing) {
                $isDifferent = false;
                foreach (['weather_score', 'economic_score', 'currency_score', 'port_score', 'news_score', 'total_score', 'risk_level'] as $key) {
                    if ($existing->$key != $dataToUpdate[$key]) {
                        $isDifferent = true;
                        break;
                    }
                }

                if ($isDifferent) {
                    $this->saveRiskHistory($country, $totalScore, $riskLevel);
                    $existing->update($dataToUpdate);
                    Log::info("RiskScoreEngine: [Risk Updated] {$country->country_name} score: {$totalScore} ({$riskLevel})");
                }
                $riskScore = $existing;
            } else {
                $this->saveRiskHistory($country, $totalScore, $riskLevel);
                $riskScore = RiskScore::create(array_merge(['country_id' => $country->id], $dataToUpdate));
                Log::info("RiskScoreEngine: [Risk Updated] {$country->country_name} score: {$totalScore} ({$riskLevel})");
            }

            return $riskScore;

        } catch (\Throwable $e) {
            Log::error("RiskScoreEngine: [Risk Failed] for {$country->country_name}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Recalculate risk scores for all countries
     */
    public function calculateAll(): void
    {
        try {
            Log::info("RiskScoreEngine: [Risk Calculation Started]");
            
            $totalCountries = Country::count();
            $processedCount = 0;
            
            Country::chunk(20, function ($countries) use (&$processedCount) {
                foreach ($countries as $country) {
                    $this->calculate($country);
                    $processedCount++;
                }
            });
            
            Log::info("RiskScoreEngine: [Calculation Finished]");
            
        } catch (\Throwable $e) {
            Log::error("RiskScoreEngine: Batch calculation error: " . $e->getMessage());
        }
    }

    /**
     * 1. Calculate Weather Score (25% Weight)
     */
    protected function calculateWeatherScore(Country $country): int
    {
        $weather = $this->liveDataService->getWeather($country);

        if (!$weather) {
            $dbWeather = WeatherData::where('country_id', $country->id)->first();
            $weather = $dbWeather ? $dbWeather->only(['storm_risk', 'rainfall', 'wind_speed', 'temperature']) : null;
        }

        if (!$weather) {
            return 30; // default cache fallback
        }

        // Storm Risk contribution (40%)
        $stormScore = (int) ($weather['storm_risk'] ?? 0);

        // Heavy Rain contribution (20%)
        $rainfall = (float) ($weather['rainfall'] ?? 0);
        $rainScore = $rainfall > 10 ? 100 : ($rainfall > 5 ? 70 : ($rainfall > 2 ? 40 : 10));

        // Extreme Wind contribution (20%)
        $wind = (float) ($weather['wind_speed'] ?? 0);
        $windScore = $wind > 15 ? 100 : ($wind > 10 ? 70 : ($wind > 5 ? 40 : 10));

        // Temperature contribution (20%)
        $temp = (float) ($weather['temperature'] ?? 15);
        $tempScore = ($temp > 38 || $temp < -5) ? 100 : (($temp > 30 || $temp < 5) ? 60 : 10);

        return (int) round(
            ($stormScore * 0.40) +
            ($rainScore  * 0.20) +
            ($windScore  * 0.20) +
            ($tempScore  * 0.20)
        );
    }

    /**
     * 2. Calculate Economy Score (20% Weight)
     */
    protected function calculateEconomyScore(Country $country): int
    {
        $economy = $this->liveDataService->getEconomy($country);

        if (!$economy) {
            $dbEconomy = EconomicData::where('country_id', $country->id)->first();
            $economy = $dbEconomy ? $dbEconomy->only(['gdp', 'inflation', 'exports', 'imports']) : null;
        }

        if (!$economy) {
            return 30; // default cache fallback
        }

        // GDP Size contribution (20%)
        $gdp = (float) ($economy['gdp'] ?? 0);
        $gdpScore = $gdp < 1e10 ? 80 : ($gdp < 5e10 ? 50 : ($gdp < 2e11 ? 30 : 10));

        // Inflation deviation contribution (30%)
        $inflation = (float) ($economy['inflation'] ?? 0);
        $inflationScore = ($inflation > 15 || $inflation < -2) ? 100 : (($inflation > 8) ? 70 : (($inflation > 4) ? 40 : 10));

        // Export volume contribution (25%)
        $exports = (float) ($economy['exports'] ?? 0);
        $exportScore = $exports < 5e9 ? 80 : ($exports < 2e10 ? 50 : 15);

        // Import volume contribution (25%)
        $imports = (float) ($economy['imports'] ?? 0);
        $importScore = $imports < 5e9 ? 80 : ($imports < 2e10 ? 50 : 15);

        return (int) round(
            ($gdpScore       * 0.20) +
            ($inflationScore * 0.30) +
            ($exportScore    * 0.25) +
            ($importScore    * 0.25)
        );
    }

    /**
     * 3. Calculate Currency Score (15% Weight)
     */
    protected function calculateCurrencyScore(Country $country): int
    {
        $dbCurrency = CurrencyData::where('country_id', $country->id)->first();
        $currency = $this->liveDataService->getCurrency($country, $dbCurrency);

        if (!$currency) {
            $currency = $dbCurrency ? $dbCurrency->only(['exchange_rate', 'change_percentage']) : null;
        }

        if (!$currency) {
            return 30; // default cache fallback
        }

        // Exchange Rate level contribution (40%)
        $rate = (float) ($currency['exchange_rate'] ?? 1);
        $rateScore = $rate > 10000 ? 80 : ($rate > 1000 ? 60 : ($rate > 100 ? 40 : ($rate > 5 ? 20 : 10)));

        // Volatility contribution (60%)
        $volatility = abs((float) ($currency['change_percentage'] ?? 0));
        $volatilityScore = $volatility > 8 ? 100 : ($volatility > 4 ? 70 : ($volatility > 2 ? 40 : 10));

        return (int) round(
            ($rateScore       * 0.40) +
            ($volatilityScore * 0.60)
        );
    }

    /**
     * 4. Calculate News Score (25% Weight)
     */
    protected function calculateNewsScore(Country $country): int
    {
        $newsList = News::where('country_id', $country->id)
            ->latest('published_at')
            ->take(10)
            ->get();

        if ($newsList->isEmpty()) {
            return 30; // default cache fallback
        }

        $negative = 0;
        $neutral  = 0;
        $positive = 0;
        $total    = $newsList->count();

        foreach ($newsList as $item) {
            if ($item->sentiment === 'Negative') {
                $negative++;
            } elseif ($item->sentiment === 'Positive') {
                $positive++;
            } else {
                $neutral++;
            }
        }

        // Weighted Sentiment Analysis index
        $score = (int) round(
            (($negative / $total) * 100) +
            (($neutral  / $total) * 40) -
            (($positive / $total) * 30)
        );

        return min(100, max(0, $score));
    }

    /**
     * 5. Calculate Port Score (15% Weight)
     */
    protected function calculatePortScore(Country $country): int
    {
        $ports = Port::where('country_id', $country->id)->get();
        if ($ports->isEmpty()) {
            return 25; // default fallback for landlocked targets
        }

        $totalPortScore = 0;

        foreach ($ports as $port) {
            // Status contribution (40%)
            $statusScore = $port->status === 'Closed' ? 100 : ($port->status === 'Congested' ? 60 : 15);

            // Congestion contribution (30%)
            $congestionScore = $port->status === 'Congested' ? 100 : 10;

            // Delay contribution (30%)
            $delayScore = $port->status === 'Closed' ? 100 : ($port->status === 'Congested' ? 60 : 10);

            $totalPortScore += (int) round(
                ($statusScore     * 0.40) +
                ($congestionScore * 0.30) +
                ($delayScore      * 0.30)
            );
        }

        return (int) round($totalPortScore / $ports->count());
    }

    /**
     * Save risk score history for tracking trends
     */
    protected function saveRiskHistory(Country $country, int $totalScore, string $riskLevel): void
    {
        try {
            \App\Models\RiskHistory::create([
                'country_id' => $country->id,
                'total_score' => $totalScore,
                'risk_level' => $riskLevel,
                'calculated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("RiskScoreEngine: Error saving risk history for {$country->country_name}: " . $e->getMessage());
        }
    }
}
