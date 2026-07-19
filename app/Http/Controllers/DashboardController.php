<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Watchlist;
use App\Models\News;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\CurrencyData;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        // Gather dashboard statistics without caching
        $dashboardData = (function () {
            // ── Risk score counts by risk_level column ─────────────────────
            $criticalRisk = RiskScore::where('risk_level', 'Critical')->count();
            $highRisk     = RiskScore::where('risk_level', 'High')->count();
            $mediumRisk   = RiskScore::where('risk_level', 'Medium')->count();
            $lowRisk      = RiskScore::where('risk_level', 'Low')->count();

            // Fallback to score‑based ranges if the risk_level column is empty
            if (($criticalRisk + $highRisk + $mediumRisk + $lowRisk) === 0) {
                $criticalRisk = RiskScore::where('total_score', '>=', 76)->count();
                $highRisk     = RiskScore::whereBetween('total_score', [51, 75])->count();
                $mediumRisk   = RiskScore::whereBetween('total_score', [26, 50])->count();
                $lowRisk      = RiskScore::where('total_score', '<=', 25)->count();
            }

            return [
                'totalCountries'       => Country::count(),
                'totalPorts'           => Port::count(),
                'totalNews'            => News::count(),
                'totalArticles'        => News::count(),
                'totalWeatherRecords'  => WeatherData::count(),
                'totalWatchlists'      => Watchlist::count(),
                'totalCurrency'        => CurrencyData::count(),
                'totalEconomy'         => EconomicData::count(),
                // Risk related
                'criticalRiskCountries' => $criticalRisk,
                'highRiskCountries'     => $highRisk,
                'mediumRiskCountries'   => $mediumRisk,
                'lowRiskCountries'      => $lowRisk,
                'criticalRiskCount'      => $criticalRisk,
                'highRiskCount'          => $highRisk,
                'mediumRiskCount'        => $mediumRisk,
                'lowRiskCount'           => $lowRisk,
                'todaysSyncCount'        => SyncLog::whereDate('created_at', now()->toDateString())->count(),
                'lastSyncTime'           => optional(SyncLog::orderBy('created_at','desc')->first())->created_at ? Carbon::parse(SyncLog::orderBy('created_at','desc')->first()->created_at)->diffForHumans() : '—',
                'highestRiskCountry'   => RiskScore::with('country')->orderBy('total_score', 'desc')->first(),
                'lowestRiskCountry'    => RiskScore::with('country')->orderBy('total_score', 'asc')->first(),
                'averageRisk'          => RiskScore::avg('total_score'),
                // Weather extremes
                'highestTemperature'   => WeatherData::orderBy('temperature', 'desc')->first(),
                'lowestTemperature'    => WeatherData::orderBy('temperature', 'asc')->first(),
                'strongestWind'        => WeatherData::orderBy('wind_speed', 'desc')->first(),
                'highestHumidity'      => WeatherData::orderBy('humidity', 'desc')->first(),
                // Economic leaders
                'highestGDP'           => EconomicData::with('country')->orderBy('gdp', 'desc')->first(),
                'highestInflation'     => EconomicData::with('country')->orderBy('inflation', 'desc')->first(),
                'highestExport'        => EconomicData::with('country')->orderBy('exports', 'desc')->first(),
                'highestImport'        => EconomicData::with('country')->orderBy('imports', 'desc')->first(),
                // Currency details
                'latestExchangeRate'   => ($latestRate = CurrencyData::orderByDesc('last_updated')->first()) 
                                            ? 'USD/' . $latestRate->currency_code . ': ' . number_format($latestRate->exchange_rate, 2) 
                                            : '—',
                'currencyUpdateTime'   => ($latestRate && $latestRate->last_updated) 
                                            ? \Carbon\Carbon::parse($latestRate->last_updated)->diffForHumans() 
                                            : '—',
                'latestCurrency'       => ($latestRate = CurrencyData::orderByDesc('last_updated')->first())
                                            ? 'USD/' . $latestRate->currency_code . ': ' . number_format($latestRate->exchange_rate, 2)
                                            : '—',
                'currencyUpdatedAt'    => ($latestRate && $latestRate->last_updated)
                                            ? \Carbon\Carbon::parse($latestRate->last_updated)->diffForHumans()
                                            : '—',
                'strongestCurrency'    => ($strongest = CurrencyData::orderBy('exchange_rate', 'asc')->first()) 
                                            ? $strongest->currency_code . ' (' . number_format($strongest->exchange_rate, 2) . ')' 
                                            : '—',
                'weakestCurrency'      => ($weakest = CurrencyData::orderBy('exchange_rate', 'desc')->first()) 
                                            ? $weakest->currency_code . ' (' . number_format($weakest->exchange_rate, 2) . ')' 
                                            : '—',
                // Dynamic data for JavaScript
                'latestNews'           => News::with('country')->latest('published_at')->take(10)->get(),
                'recentSyncLogs'       => SyncLog::with('country')->latest('failed_at')->take(5)->get(),
                // Additional sections
                'topRiskScores'        => RiskScore::orderBy('total_score', 'desc')->take(10)->get(),
                'newsSentimentRatio'   => optional(
                    (function () {
                        $total = News::count();
                        $positive = News::where('sentiment', 'positive')->count();
                        return $total > 0 ? round($positive / $total * 100, 2) . '%' : '—';
                    })()
                ),
                'activePorts'          => Port::where('status', 'active')->get(),
                'riskTrend'            => RiskScore::orderBy('created_at', 'desc')->take(30)->get(),
                'riskDistribution'    => [
                    'critical' => $criticalRisk,
                    'high'     => $highRisk,
                    'medium'   => $mediumRisk,
                    'low'      => $lowRisk,
                ],
            ];
        })();

        return view('dashboard', $dashboardData);
    }

    public function global()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('dashboard.global', compact('countries'));
    }
}