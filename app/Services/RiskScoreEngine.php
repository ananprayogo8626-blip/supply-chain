<?php

namespace App\Services;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\CurrencyData;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;

class RiskScoreEngine
{
    /**
     * Weights for risk calculation
     * Must sum to 1.0 (100%)
     */
    protected $weights = [
        'weather' => 0.25,
        'economy' => 0.25,
        'news'    => 0.25,
        'currency'=> 0.15,
        'port'    => 0.10,
    ];

    /**
     * Calculate and save/update risk score for a country
     *
     * @param Country $country
     * @return RiskScore|null
     */
    public function calculate(Country $country): ?RiskScore
    {
        // 1. Weather Score (25%)
        $weatherData = WeatherData::where('country_id', $country->id)->first();
        $weatherScore = 20; // Healthy standard weather
        if ($weatherData) {
            $ws = 0;
            // Temp risk
            $temp = abs((float) $weatherData->temperature);
            if ($temp > 35 || $temp < 0) $ws += 25;
            elseif ($temp > 28 || $temp < 8) $ws += 10;
            
            // Wind speed
            $wind = (float) $weatherData->wind_speed;
            if ($wind > 12) $ws += 25;
            elseif ($wind > 6) $ws += 10;
            
            // Storm risk
            $ws += (int) $weatherData->storm_risk;
            
            // Rainfall/Cloud
            if ((float) $weatherData->rainfall > 5) $ws += 15;
            if ((float) $weatherData->cloud > 70) $ws += 10;
            
            $weatherScore = min(100, max(20, $ws));
        }

        // 2. Economy Score (25%)
        $economicData = EconomicData::where('country_id', $country->id)->first();
        $economyScore = 30; // Default
        if ($economicData) {
            $es = 15;
            // Inflation
            $inflation = (float) $economicData->inflation;
            if ($inflation > 15 || $inflation < -2) $es += 40;
            elseif ($inflation > 8 || $inflation < 0) $es += 25;
            elseif ($inflation > 4) $es += 15;
            
            // GDP Growth
            $growth = (float) $economicData->gdp_growth;
            if ($growth < -1) $es += 35;
            elseif ($growth < 1) $es += 20;
            elseif ($growth < 2.5) $es += 10;
            
            // Trade balance
            $imports = (float) $economicData->imports;
            $exports = (float) $economicData->exports;
            if ($imports > $exports * 1.3) $es += 15;
            
            $economyScore = min(100, max(15, $es));
        }

        // 3. News Score (25%)
        $newsList = News::where('country_id', $country->id)->latest()->take(10)->get();
        $newsScore = 30; // Default
        if ($newsList->count() > 0) {
            $totalImpact = 0;
            foreach ($newsList as $news) {
                $impact = (int) $news->impact_score;
                if ($news->sentiment === 'Negative') {
                    $impact = min(100, $impact + 35); // Stronger penalty
                } elseif ($news->sentiment === 'Positive') {
                    $impact = max(10, $impact - 15);
                }
                $totalImpact += $impact;
            }
            $newsScore = (int) ($totalImpact / $newsList->count());
        }

        // 4. Currency Score (15%)
        $currencyData = CurrencyData::where('country_id', $country->id)->first();
        $currencyScore = 20; // Default
        if ($currencyData) {
            $change = abs((float) $currencyData->change_percentage);
            if ($change > 8) $currencyScore = 90;
            elseif ($change > 4) $currencyScore = 70;
            elseif ($change > 1.5) $currencyScore = 40;
            else $currencyScore = 15;
        }

        // 5. Port Score (10%)
        $ports = Port::where('country_id', $country->id)->get();
        $portScore = 10; // Default
        if ($ports->count() > 0) {
            $pts = 10;
            $hasClosed = $ports->where('status', 'Closed')->count() > 0;
            $hasCongested = $ports->where('status', 'Congested')->count() > 0;
            if ($hasClosed) $pts += 50;
            if ($hasCongested) $pts += 30;
            
            $inactivePorts = $ports->where('status', 'Inactive')->count();
            if ($inactivePorts > 0) {
                $pts += ($inactivePorts / $ports->count()) * 20;
            }
            $portScore = min(100, $pts);
        }

        // Calculate weighted score
        $totalScore = (int) round(
            ($weatherScore * $this->weights['weather']) +
            ($economyScore * $this->weights['economy']) +
            ($newsScore * $this->weights['news']) +
            ($currencyScore * $this->weights['currency']) +
            ($portScore * $this->weights['port'])
        );

        // Classify Risk Level
        if ($totalScore >= 76) {
            $riskLevel = 'Critical';
            $recommendation = 'Critical risk level. Highest priority threat. Diversify suppliers, establish backup logistics, and hedge currency exposure immediately.';
        } elseif ($totalScore >= 51) {
            $riskLevel = 'High';
            $recommendation = 'High risk level. Major warning. Limit single-source dependencies and review buffer inventory.';
        } elseif ($totalScore >= 26) {
            $riskLevel = 'Medium';
            $recommendation = 'Moderate risk level. Monitor weather warnings, track currency fluctuations, and maintain regular contact with local agents.';
        } else {
            $riskLevel = 'Low';
            $recommendation = 'Stable supply chain environment. Standard operations can proceed. Normal monitoring schedule is sufficient.';
        }

        // Update or create risk score
        return RiskScore::updateOrCreate(
            ['country_id' => $country->id],
            [
                'weather_score'  => $weatherScore,
                'economic_score' => $economyScore,
                'currency_score' => $currencyScore,
                'news_score'     => $newsScore,
                'port_score'     => $portScore,
                'total_score'    => $totalScore,
                'risk_level'     => $riskLevel,
                'recommendation' => $recommendation,
                'calculated_at'  => now(),
            ]
        );
    }
}
