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

class DashboardController extends Controller
{
    public function index()
    {
        // Cache dashboard data for 5 minutes to optimize performance
        $dashboardData = Cache::remember('dashboard_stats', 300, function () {
            // ── Risk score counts by risk_level column ─────────────────────
            $criticalRisk = RiskScore::where('risk_level', 'Critical')->count();
            $highRisk     = RiskScore::where('risk_level', 'High')->count();
            $mediumRisk   = RiskScore::where('risk_level', 'Medium')->count();
            $lowRisk      = RiskScore::where('risk_level', 'Low')->count();

            // Fallback to score-based ranges if risk_level column isn't populated yet
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
                'totalWeatherRecords'  => WeatherData::count(),
                'totalWatchlists'      => Watchlist::count(),
                // Named vars used in blade
                'criticalRiskCountries' => $criticalRisk,
                'highRiskCountries'     => $highRisk,
                'mediumRiskCountries'   => $mediumRisk,
                'lowRiskCountries'      => $lowRisk,
                // For backward compat (some views use highRiskCountries as "high+crit")
                'highestRiskCountry'   => RiskScore::with('country')->orderBy('total_score', 'desc')->first(),
                'lowestRiskCountry'    => RiskScore::with('country')->orderBy('total_score', 'asc')->first(),
                'averageRisk'          => RiskScore::avg('total_score'),
                'highestTemperature'   => WeatherData::orderBy('temperature', 'desc')->first(),
                'lowestTemperature'    => WeatherData::orderBy('temperature', 'asc')->first(),
                'strongestWind'        => WeatherData::orderBy('wind_speed', 'desc')->first(),
                'highestHumidity'      => WeatherData::orderBy('humidity', 'desc')->first(),
                'highestGDP'           => EconomicData::with('country')->orderBy('gdp', 'desc')->first(),
                'highestInflation'     => EconomicData::with('country')->orderBy('inflation', 'desc')->first(),
                'highestExport'        => EconomicData::with('country')->orderBy('exports', 'desc')->first(),
                'highestImport'        => EconomicData::with('country')->orderBy('imports', 'desc')->first(),
                'totalCurrency'        => CurrencyData::count(),
                'latestExchangeRate'   => ($latestRate = CurrencyData::orderByDesc('last_updated')->first()) 
                                            ? 'USD/' . $latestRate->currency_code . ': ' . number_format($latestRate->exchange_rate, 2) 
                                            : '—',
                'currencyUpdateTime'   => ($latestRate && $latestRate->last_updated) 
                                            ? \Carbon\Carbon::parse($latestRate->last_updated)->diffForHumans() 
                                            : '—',
                'strongestCurrency'    => ($strongest = CurrencyData::orderBy('exchange_rate', 'asc')->first()) 
                                            ? $strongest->currency_code . ' (' . number_format($strongest->exchange_rate, 2) . ')' 
                                            : '—',
                'weakestCurrency'      => ($weakest = CurrencyData::orderBy('exchange_rate', 'desc')->first()) 
                                            ? $weakest->currency_code . ' (' . number_format($weakest->exchange_rate, 2) . ')' 
                                            : '—',
                'latestNews'           => News::with('country')->latest('published_at')->take(10)->get(),
                'recentSyncLogs'       => SyncLog::with('country')->latest('failed_at')->take(5)->get(),
            ];
        });

        return view('dashboard', $dashboardData);
    }

    public function global()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('dashboard.global', compact('countries'));
    }
}