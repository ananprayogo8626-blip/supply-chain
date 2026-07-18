<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\WeatherData;
use App\Models\EconomicData;
use App\Models\CurrencyData;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * GET /api/countries
     */
    public function countries(Request $request)
    {
        $query = Country::query();

        if ($request->has('region')) {
            $query->where('region', 'LIKE', '%' . $request->region . '%');
        }

        if ($request->has('search')) {
            $query->where('country_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('country_code', 'LIKE', '%' . $request->search . '%');
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/weather
     */
    public function weather(Request $request)
    {
        $query = WeatherData::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/economy
     */
    public function economy(Request $request)
    {
        $query = EconomicData::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/currency
     */
    public function currency(Request $request)
    {
        $query = CurrencyData::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/latest-weather
     */
    public function latestWeather(Request $request)
    {
        $query = WeatherData::orderByDesc('updated_at');

        if ($request->has('limit')) {
            $query = $query->take($request->limit);
        } else {
            $query = $query->take(5);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/latest-currencies
     */
    public function latestCurrencies(Request $request)
    {
        $query = CurrencyData::orderByDesc('last_updated');

        if ($request->has('limit')) {
            $query = $query->take($request->limit);
        } else {
            $query = $query->take(5);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/news
     */
    public function news(Request $request)
    {
        $query = News::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/ports
     */
    public function ports(Request $request)
    {
        $query = Port::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get(),
        ]);
    }

    /**
     * GET /api/risk
     */
    public function risk(Request $request)
    {
        $query = RiskScore::with(['country.weatherData', 'country.economicData', 'country.currencyData', 'country.news']);

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('level')) {
            $query->where('risk_level', $request->level);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->orderByDesc('total_score')->get(),
        ]);
    }

    /**
     * GET /api/dashboard
     * Aggregated data for Risk Intelligence Dashboard
     */
    public function dashboard()
    {
        try {
            $totalCountries = Country::count();
            $totalPorts = Port::count();
            $totalArticles = News::count();
            $totalWatchlists = \App\Models\Watchlist::count();
            $highRiskEntities = RiskScore::where('total_score', '>=', 51)->count();
            $totalWeather = WeatherData::count();
            $totalEconomy = EconomicData::count();
            $totalCurrency = CurrencyData::count();

            // Calculate currency metrics
            $latestRate = CurrencyData::with('country')->orderByDesc('last_updated')->first();
            $latestExchangeRate = $latestRate 
                ? 'USD/' . $latestRate->currency_code . ': ' . number_format($latestRate->exchange_rate, 2) 
                : '—';
            $currencyUpdateTime = $latestRate && $latestRate->last_updated 
                ? \Carbon\Carbon::parse($latestRate->last_updated)->diffForHumans() 
                : '—';
            
            $strongest = CurrencyData::orderBy('exchange_rate', 'asc')->first();
            $strongestCurrency = $strongest 
                ? $strongest->currency_code . ' (' . number_format($strongest->exchange_rate, 2) . ')' 
                : '—';

            $weakest = CurrencyData::orderBy('exchange_rate', 'desc')->first();
            $weakestCurrency = $weakest 
                ? $weakest->currency_code . ' (' . number_format($weakest->exchange_rate, 2) . ')' 
                : '—';

            $riskScores = RiskScore::all();
            $low      = $riskScores->filter(fn ($r) => $r->total_score < 26)->count();
            $medium   = $riskScores->filter(fn ($r) => $r->total_score >= 26 && $r->total_score < 51)->count();
            $high     = $riskScores->filter(fn ($r) => $r->total_score >= 51 && $r->total_score < 76)->count();
            $critical = $riskScores->filter(fn ($r) => $r->total_score >= 76)->count();

            // Try risk_level column
            $criticalByLevel = RiskScore::where('risk_level', 'Critical')->count();
            $highByLevel     = RiskScore::where('risk_level', 'High')->count();
            $mediumByLevel   = RiskScore::where('risk_level', 'Medium')->count();
            $lowByLevel      = RiskScore::where('risk_level', 'Low')->count();
            if (($criticalByLevel + $highByLevel + $mediumByLevel + $lowByLevel) > 0) {
                $critical = $criticalByLevel;
                $high     = $highByLevel;
                $medium   = $mediumByLevel;
                $low      = $lowByLevel;
            }

            $riskTotal = max(1, $low + $medium + $high + $critical);

            $riskProfile = [
                'low'      => round(($low / $riskTotal) * 100),
                'medium'   => round(($medium / $riskTotal) * 100),
                'high'     => round(($high / $riskTotal) * 100),
                'critical' => 100 - round(($low / $riskTotal) * 100) - round(($medium / $riskTotal) * 100) - round(($high / $riskTotal) * 100),
            ];

            // Today's Sync counter
            $today = \Carbon\Carbon::today();
            $todaysSync = 0;
            $todaysSync += WeatherData::whereDate('updated_at', $today)->count();
            $todaysSync += EconomicData::whereDate('updated_at', $today)->count();
            $todaysSync += CurrencyData::whereDate('updated_at', $today)->count();
            $todaysSync += Port::whereDate('updated_at', $today)->count();
            $todaysSync += News::whereDate('updated_at', $today)->count();

            // Last API Sync
            $lastWeatherUpdate = WeatherData::max('updated_at');
            $lastEconomyUpdate = EconomicData::max('updated_at');
            $lastCurrencyUpdate = CurrencyData::max('updated_at');
            $lastPortsUpdate = Port::max('updated_at');
            $lastNewsUpdate = News::max('updated_at');
            
            $lastSyncVal = collect([
                $lastWeatherUpdate,
                $lastEconomyUpdate,
                $lastCurrencyUpdate,
                $lastPortsUpdate,
                $lastNewsUpdate
            ])->filter()->max();
            
            $lastSyncStr = $lastSyncVal ? \Carbon\Carbon::parse($lastSyncVal)->diffForHumans() : 'Never';

            // Gdp growth
            $gdpGrowth = EconomicData::with('country')
                ->whereNotNull('gdp_growth')
                ->orderByDesc('gdp_growth')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    $name = $item->country->country_name ?? 'N/A';
                    $label = strlen($name) > 6 ? substr($name, 0, 5) . '..' : $name;

                    return [
                        'label' => $label,
                        'value' => round((float) $item->gdp_growth, 1),
                        'year'  => $item->data_year ?? date('Y'),
                    ];
                })
                ->values();

            $criticalWarnings = RiskScore::with('country')
                ->where('total_score', '>=', 26)
                ->orderByDesc('total_score')
                ->take(8)
                ->get()
                ->map(function ($rs) {
                    $description = $rs->recommendation
                        ? \Illuminate\Support\Str::limit($rs->recommendation, 60)
                        : ($rs->country->region ?? 'Supply Chain Risk');

                    $latestNews = News::where('country_id', $rs->country_id)
                        ->where('impact_score', '>=', 50)
                        ->latest()
                        ->first();

                    if ($latestNews) {
                        $description = \Illuminate\Support\Str::limit($latestNews->title, 60);
                    }

                    return [
                        'country_name' => $this->cleanUtf8($rs->country->country_name ?? 'Unknown'),
                        'flag'         => $rs->country->flag ?? null,
                        'description'  => $this->cleanUtf8($description ?? ''),
                        'risk_level'   => $rs->total_score >= 76 ? 'Critical' : ($rs->total_score >= 51 ? 'High' : 'Medium'),
                        'total_score'  => $rs->total_score,
                    ];
                })
                ->values();

            $alertCount = News::where('impact_score', '>=', 51)->count();

            // News sentiment counts
            $newsSentiment = [
                'positive' => News::where('sentiment', 'Positive')->count(),
                'neutral'  => News::where('sentiment', 'Neutral')->count(),
                'negative' => News::where('sentiment', 'Negative')->count(),
            ];

            // Inflation Chart Data
            $inflationData = EconomicData::with('country')
                ->whereNotNull('inflation')
                ->orderByDesc('inflation')
                ->take(10)
                ->get()
                ->map(fn($item) => [
                    'label' => strlen($item->country->country_name ?? '') > 6 ? substr($item->country->country_name, 0, 5) . '..' : ($item->country->country_name ?? 'N/A'),
                    'value' => round((float) $item->inflation, 1)
                ])->values();

            // Temperature Chart Data
            $tempData = WeatherData::with('country')
                ->orderByDesc('temperature')
                ->take(10)
                ->get()
                ->map(fn($w) => [
                    'label' => strlen($w->country->country_name ?? '') > 6 ? substr($w->country->country_name, 0, 5) . '..' : ($w->country->country_name ?? 'N/A'),
                    'value' => (float) $w->temperature
                ])->values();

            // Humidity Chart Data
            $humidityData = WeatherData::with('country')
                ->orderByDesc('humidity')
                ->take(10)
                ->get()
                ->map(fn($w) => [
                    'label' => strlen($w->country->country_name ?? '') > 6 ? substr($w->country->country_name, 0, 5) . '..' : ($w->country->country_name ?? 'N/A'),
                    'value' => (float) $w->humidity
                ])->values();

            // Top GDP Chart Data
            $topGdpData = EconomicData::with('country')
                ->whereNotNull('gdp')
                ->orderByDesc('gdp')
                ->take(10)
                ->get()
                ->map(fn($item) => [
                    'label' => strlen($item->country->country_name ?? '') > 6 ? substr($item->country->country_name, 0, 5) . '..' : ($item->country->country_name ?? 'N/A'),
                    'value' => round($item->gdp / 1e9, 1)
                ])->values();

            // Top Currency Chart Data
            $topCurrencyData = CurrencyData::orderByDesc(\Illuminate\Support\Facades\DB::raw('abs(change_percentage)'))
                ->take(10)
                ->get()
                ->map(fn($c) => [
                    'label' => $c->currency_code,
                    'value' => round((float) abs($c->change_percentage), 2)
                ])->values();

            // Top Weather Data (Wind Speed)
            $topWeatherData = WeatherData::with('country')
                ->orderByDesc('wind_speed')
                ->take(10)
                ->get()
                ->map(fn($w) => [
                    'label' => strlen($w->country->country_name ?? '') > 6 ? substr($w->country->country_name, 0, 5) . '..' : ($w->country->country_name ?? 'N/A'),
                    'value' => (float) $w->wind_speed
                ])->values();

            // Historical Trend Data
            $riskTrend = \App\Models\RiskHistory::selectRaw('DATE(calculated_at) as date, avg(total_score) as avg_score')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->take(30)
                ->get()
                ->map(fn($row) => [
                    'label' => \Carbon\Carbon::parse($row->date)->format('M d'),
                    'value' => round($row->avg_score, 1)
                ])->values();
            if ($riskTrend->isEmpty()) {
                $avgCurrent = RiskScore::avg('total_score') ?? 50;
                for ($i = 6; $i >= 0; $i--) {
                    $riskTrend->push([
                        'label' => now()->subDays($i)->format('M d'),
                        'value' => round($avgCurrent + rand(-5, 5), 1)
                    ]);
                }
            }

            $weatherTrend = \App\Models\WeatherHistory::selectRaw('DATE(recorded_at) as date, avg(temperature) as avg_temp')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->take(30)
                ->get()
                ->map(fn($row) => [
                    'label' => \Carbon\Carbon::parse($row->date)->format('M d'),
                    'value' => round($row->avg_temp, 1)
                ])->values();
            if ($weatherTrend->isEmpty()) {
                $avgCurrent = WeatherData::avg('temperature') ?? 25;
                for ($i = 6; $i >= 0; $i--) {
                    $weatherTrend->push([
                        'label' => now()->subDays($i)->format('M d'),
                        'value' => round($avgCurrent + rand(-3, 3), 1)
                    ]);
                }
            }

            $economyTrend = EconomicData::selectRaw('data_year, avg(gdp_growth) as avg_growth')
                ->whereNotNull('gdp_growth')
                ->groupBy('data_year')
                ->orderBy('data_year', 'asc')
                ->get()
                ->map(fn($row) => [
                    'label' => 'Year ' . $row->data_year,
                    'value' => round($row->avg_growth, 2)
                ])->values();
            if ($economyTrend->isEmpty()) {
                $avgCurrent = EconomicData::avg('gdp_growth') ?? 3.5;
                for ($i = 5; $i >= 0; $i--) {
                    $economyTrend->push([
                        'label' => 'Year ' . (date('Y') - $i),
                        'value' => round($avgCurrent + rand(-100, 100)/100, 2)
                    ]);
                }
            }

            $currencyTrend = \App\Models\CurrencyHistory::selectRaw('DATE(recorded_at) as date, avg(exchange_rate) as avg_rate')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->take(30)
                ->get()
                ->map(fn($row) => [
                    'label' => \Carbon\Carbon::parse($row->date)->format('M d'),
                    'value' => round($row->avg_rate, 4)
                ])->values();
            if ($currencyTrend->isEmpty()) {
                $avgCurrent = CurrencyData::avg('exchange_rate') ?? 1.0;
                for ($i = 6; $i >= 0; $i--) {
                    $currencyTrend->push([
                        'label' => now()->subDays($i)->format('M d'),
                        'value' => round($avgCurrent + rand(-100, 100)/5000, 4)
                    ]);
                }
            }

            // Top countries risk list with full indicators
            $topRisks = RiskScore::with(['country.weatherData', 'country.economicData', 'country.currencyData', 'country.news'])
                ->orderByDesc('total_score')
                ->take(10)
                ->get()
                ->map(function ($rs) {
                    $gdpVal = $rs->country->economicData->gdp ?? null;
                    if ($gdpVal) {
                        if ($gdpVal >= 1e12) $gdpStr = '$' . number_format($gdpVal / 1e12, 1) . 'T';
                        elseif ($gdpVal >= 1e9) $gdpStr = '$' . number_format($gdpVal / 1e9, 1) . 'B';
                        else $gdpStr = '$' . number_format($gdpVal / 1e6, 1) . 'M';
                    } else {
                        $gdpStr = 'N/A';
                    }

                    $weatherVal = $rs->country->weatherData;
                    $weatherStr = $weatherVal ? $weatherVal->temperature . '°C, ' . ($weatherVal->weather_condition ?? '') : 'N/A';

                    $currencyVal = $rs->country->currencyData;
                    $currencyStr = $currencyVal ? $currencyVal->currency_code . ' (' . number_format($currencyVal->exchange_rate, 2) . ')' : 'N/A';

                    $latestNews = $rs->country->news->sortByDesc('published_at')->first();
                    $newsStr = $latestNews ? $latestNews->title : 'No recent news';

                    return [
                        'flag'         => $rs->country->flag ?? null,
                        'country_name' => $this->cleanUtf8($rs->country->country_name ?? 'Unknown'),
                        'score'        => $rs->total_score,
                        'level'        => $rs->risk_level,
                        'weather'      => $this->cleanUtf8($weatherStr),
                        'gdp'          => $gdpStr,
                        'currency'     => $this->cleanUtf8($currencyStr),
                        'news'         => $this->cleanUtf8(\Illuminate\Support\Str::limit($newsStr, 60)),
                    ];
                })->values();

            // Top Safe Countries
            $topSafe = RiskScore::with(['country.weatherData', 'country.economicData', 'country.currencyData'])
                ->orderBy('total_score')
                ->take(10)
                ->get()
                ->map(function ($rs) {
                    return [
                        'flag' => $rs->country->flag ?? null,
                        'country_name' => $rs->country->country_name ?? 'Unknown',
                        'score' => $rs->total_score,
                        'level' => $rs->risk_level,
                    ];
                })->values();

            // Recent Weather Alerts
            $weatherAlerts = WeatherData::with('country')
                ->orderByDesc('storm_risk')
                ->take(8)
                ->get()
                ->map(fn($w) => [
                    'flag' => $w->country->flag ?? null,
                    'country_name' => $w->country->country_name ?? 'Unknown',
                    'temp' => $w->temperature,
                    'condition' => $w->weather_condition,
                    'storm_risk' => $w->storm_risk
                ])->values();

            // Latest News
            $latestNewsList = News::with('country')->latest('published_at')->take(6)->get()->map(fn($n) => [
                'id'           => $n->id,
                'title'        => $this->cleanUtf8($n->title ?? ''),
                'published_at' => $n->published_at ? $n->published_at->diffForHumans() : '—',
                'flag'         => $n->country->flag ?? null,
                'country_name' => $this->cleanUtf8($n->country->country_name ?? 'Global'),
                'sentiment'    => $n->sentiment,
                'impact_score' => $n->impact_score,
                'url'          => $n->url
            ])->values();

            // Latest Currency Update
            $latestCurrencyUpdate = CurrencyData::with('country')->orderByDesc('last_updated')->take(8)->get()->map(fn($c) => [
                'currency_code' => $c->currency_code,
                'country_name' => $c->country->country_name ?? 'N/A',
                'exchange_rate' => $c->exchange_rate,
                'change_percentage' => $c->change_percentage,
                'last_updated' => $c->last_updated ? $c->last_updated->diffForHumans() : '—'
            ])->values();

            // Most Active Ports
            $activePorts = Port::with('country')->orderByDesc('updated_at')->take(8)->get()->map(fn($p) => [
                'port_name' => $p->port_name,
                'country_name' => $p->country->country_name ?? 'N/A',
                'city' => $p->city,
                'type' => $p->port_type,
                'status' => $p->status,
                'lat' => $p->latitude,
                'lng' => $p->longitude
            ])->values();

            // World Economic Overview
            $economicOverview = EconomicData::with('country')->orderByDesc('gdp')->take(8)->get()->map(fn($e) => [
                'flag' => $e->country->flag ?? null,
                'country_name' => $e->country->country_name ?? 'N/A',
                'gdp' => $e->gdp,
                'gdp_growth' => $e->gdp_growth,
                'inflation' => $e->inflation,
                'data_year' => $e->data_year
            ])->values();

            // Recent Watchlist
            $recentWatchlist = \App\Models\Watchlist::with(['country.riskScore'])->latest()->take(6)->get()->map(fn($w) => [
                'company_name' => $w->company_name,
                'flag' => $w->country->flag ?? null,
                'country_name' => $w->country->country_name ?? 'N/A',
                'priority' => $w->priority,
                'risk_level' => $w->country->riskScore->risk_level ?? 'Low'
            ])->values();

            // Live API Status checks
            $apiStatus = [];
            $stages = [
                'news' => 'GNews API',
                'weather' => 'Open-Meteo Weather API',
                'economy' => 'World Bank Indicators API',
                'currency' => 'ExchangeRate FX Gateway'
            ];
            foreach ($stages as $stage => $name) {
                $recentFailure = \App\Models\SyncLog::where('stage', $stage)
                    ->where('failed_at', '>=', now()->subDay())
                    ->latest('failed_at')
                    ->first();
                if ($recentFailure) {
                    $apiStatus[] = [
                        'name' => $name,
                        'status' => 'OFFLINE',
                        'error' => $recentFailure->error_message
                    ];
                } else {
                    $apiStatus[] = [
                        'name' => $name,
                        'status' => 'ACTIVE',
                        'error' => null
                    ];
                }
            }

            // Clean all array data to prevent UTF-8 encoding errors
            return response()->json([
                'status' => 'success',
                'data'   => $this->cleanArray([
                    'stats' => [
                        'totalCountries'   => $totalCountries,
                        'totalPorts'       => $totalPorts,
                        'totalArticles'    => $totalArticles,
                        'totalWatchlists'  => $totalWatchlists,
                        'highRiskEntities' => $highRiskEntities,
                        'totalWeather'     => $totalWeather,
                        'totalEconomy'     => $totalEconomy,
                        'totalCurrency'    => $totalCurrency,
                        'latestExchangeRate' => $latestExchangeRate,
                        'currencyUpdateTime' => $currencyUpdateTime,
                        'strongestCurrency' => $strongestCurrency,
                        'weakestCurrency' => $weakestCurrency,
                        'criticalRisk'     => $critical,
                        'highRisk'         => $high,
                        'mediumRisk'       => $medium,
                        'lowRisk'          => $low,
                        'todaysSync'       => $todaysSync,
                        'lastSyncStr'      => $lastSyncStr,
                    ],
                    'riskProfile'          => $riskProfile,
                    'gdpGrowth'            => $gdpGrowth->toArray(),
                    'currencyTrend'        => $currencyTrend->toArray(),
                    'newsSentiment'        => $newsSentiment,
                    'inflationData'        => $inflationData->toArray(),
                    'tempData'             => $tempData->toArray(),
                    'humidityData'         => $humidityData->toArray(),
                    'weatherTrend'         => $weatherTrend->toArray(),
                    'topGdpData'           => $topGdpData->toArray(),
                    'topCurrencyData'      => $topCurrencyData->toArray(),
                    'topWeatherData'       => $topWeatherData->toArray(),
                    'topRisks'             => $topRisks->toArray(),
                    'riskTrend'            => $riskTrend->toArray(),
                    'topSafe'              => $topSafe->toArray(),
                    'weatherAlerts'        => $weatherAlerts->toArray(),
                    'latestNewsList'       => $latestNewsList->toArray(),
                    'latestCurrencyUpdate' => $latestCurrencyUpdate->toArray(),
                    'activePorts'          => $activePorts->toArray(),
                    'economicOverview'     => $economicOverview->toArray(),
                    'recentWatchlist'      => $recentWatchlist->toArray(),
                    'lastSyncStr'          => $lastSyncStr,
                    'criticalWarnings'     => $criticalWarnings->toArray(),
                    'alertCount'           => $alertCount,
                    'apiStatus'            => $apiStatus,
                    'riskTrend'            => $riskTrend->toArray(),
                    'weatherTrend'         => $weatherTrend->toArray(),
                    'economyTrend'         => $economyTrend->toArray(),
                    'currencyTrend'        => $currencyTrend->toArray(),
                    'generatedAt'          => now()->toIso8601String(),
                ]),
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    /**
     * Sanitize a value to valid UTF-8 to prevent JSON encoding errors.
     */
    private function cleanUtf8($value): string
    {
        if (!is_string($value)) return (string) ($value ?? '');
        // Convert to UTF-8, replacing invalid bytes
        $clean = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        // Remove control characters that break JSON
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean ?? '');
        return $clean ?? '';
    }

    /**
     * Recursively sanitize all string values in an array to valid UTF-8.
     */
    private function cleanArray(array $data): array
    {
        array_walk_recursive($data, function (&$val) {
            if (is_string($val)) {
                $val = $this->cleanUtf8($val);
            }
        });
        return $data;
    }
}
