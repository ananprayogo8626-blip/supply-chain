<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\News;
use App\Models\Port;
use App\Models\RiskScore;
use App\Services\ExchangeRateService;
use App\Services\LiveCountryDataService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(
        protected LiveCountryDataService $liveDataService,
        protected ExchangeRateService $exchangeRateService,
    ) {
    }

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
     * GET /api/weather?country_id=
     * Data cuaca real-time (bukan dari database) untuk satu negara.
     */
    public function weather(Request $request)
    {
        if (!$request->has('country_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter country_id wajib diisi — data cuaca diambil real-time per negara, bukan daftar semua negara.',
            ], 422);
        }

        $country = Country::find($request->country_id);
        if (!$country) {
            return response()->json(['status' => 'error', 'message' => 'Country not found'], 404);
        }

        $weather = $this->liveDataService->getWeather($country);

        return response()->json([
            'status' => 'success',
            'data' => $weather ? array_merge($weather, ['country_id' => $country->id]) : null,
        ]);
    }

    /**
     * GET /api/economy?country_id=
     * Data ekonomi real-time (bukan dari database) untuk satu negara.
     */
    public function economy(Request $request)
    {
        if (!$request->has('country_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter country_id wajib diisi — data ekonomi diambil real-time per negara, bukan daftar semua negara.',
            ], 422);
        }

        $country = Country::find($request->country_id);
        if (!$country) {
            return response()->json(['status' => 'error', 'message' => 'Country not found'], 404);
        }

        $economy = $this->liveDataService->getEconomy($country);

        return response()->json([
            'status' => 'success',
            'data' => $economy ? array_merge($economy, ['country_id' => $country->id]) : null,
        ]);
    }

    /**
     * GET /api/currency[?country_id=]
     * Tanpa country_id: kurs real-time semua negara sekaligus (1 panggilan API, di-cache).
     * Dengan country_id: kurs real-time untuk satu negara.
     */
    public function currency(Request $request)
    {
        if ($request->has('country_id')) {
            $country = Country::find($request->country_id);
            if (!$country) {
                return response()->json(['status' => 'error', 'message' => 'Country not found'], 404);
            }

            $currency = $this->liveDataService->getCurrency($country);

            return response()->json([
                'status' => 'success',
                'data' => $currency ? array_merge($currency, ['country_id' => $country->id]) : null,
            ]);
        }

        $rates = $this->exchangeRateService->getRates('USD');
        $data = [];

        if ($rates && isset($rates['rates'])) {
            $countries = Country::whereNotNull('currency')->where('currency', '!=', '')->get();

            foreach ($countries as $country) {
                $code = strtoupper(trim(explode(',', $country->currency)[0] ?? ''));
                if ($code && isset($rates['rates'][$code])) {
                    $data[] = [
                        'country_id' => $country->id,
                        'country_name' => $country->country_name,
                        'currency_code' => $code,
                        'base_currency' => 'USD',
                        'exchange_rate' => (float) $rates['rates'][$code],
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
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
        $query = RiskScore::with(['country.news']);

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

            // Last API Sync (hanya domain yang masih punya pipeline sync: ports & news)
            $lastPortsUpdate = Port::max('updated_at');
            $lastNewsUpdate = News::max('updated_at');

            $lastSyncVal = collect([
                $lastPortsUpdate,
                $lastNewsUpdate
            ])->filter()->max();

            $lastSyncStr = $lastSyncVal ? \Carbon\Carbon::parse($lastSyncVal)->diffForHumans() : 'Never';

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

            // Historical Trend Data (Risk saja — satu-satunya domain dengan tabel histori resmi)
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

            // Top countries risk list dengan indikator real-time (dibatasi 10 negara)
            $topRisks = RiskScore::with(['country.news'])
                ->orderByDesc('total_score')
                ->take(10)
                ->get()
                ->map(function ($rs) {
                    $economy = $this->liveDataService->getEconomy($rs->country);
                    $gdpVal = $economy['gdp'] ?? null;
                    if ($gdpVal) {
                        if ($gdpVal >= 1e12) $gdpStr = '$' . number_format($gdpVal / 1e12, 1) . 'T';
                        elseif ($gdpVal >= 1e9) $gdpStr = '$' . number_format($gdpVal / 1e9, 1) . 'B';
                        else $gdpStr = '$' . number_format($gdpVal / 1e6, 1) . 'M';
                    } else {
                        $gdpStr = 'N/A';
                    }

                    $weather = $this->liveDataService->getWeather($rs->country);
                    $weatherStr = $weather ? $weather['temperature'] . '°C, ' . ($weather['weather_condition'] ?? '') : 'N/A';

                    $currency = $this->liveDataService->getCurrency($rs->country);
                    $currencyStr = $currency ? $currency['currency_code'] . ' (' . number_format($currency['exchange_rate'], 2) . ')' : 'N/A';

                    $latestNews = $rs->country->news->sortByDesc('published_at')->first();
                    $newsStr = $latestNews ? $latestNews->title : 'No recent news';

                    return [
                        'flag'         => $rs->country->flag ?? null,
                        'country_name' => $this->cleanUtf8($rs->country->country_name ?? 'Unknown'),
                        'score'        => $rs->total_score,
                        'level'        => $rs->risk_level,
                        'riskLevel'    => $rs->risk_level,
                        'lat'          => $rs->country->latitude ?? null,
                        'lng'          => $rs->country->longitude ?? null,
                        'weather'      => $this->cleanUtf8($weatherStr),
                        'gdp'          => $gdpStr,
                        'currency'     => $this->cleanUtf8($currencyStr),
                        'news'         => $this->cleanUtf8(\Illuminate\Support\Str::limit($newsStr, 60)),
                    ];
                })->values();

            // Top Safe Countries
            $topSafe = RiskScore::with(['country'])
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

            // Recent Watchlist
            $recentWatchlist = \App\Models\Watchlist::with(['country.riskScore'])->latest()->take(6)->get()->map(fn($w) => [
                'company_name' => $w->company_name,
                'flag' => $w->country->flag ?? null,
                'country_name' => $w->country->country_name ?? 'N/A',
                'priority' => $w->priority,
                'risk_level' => $w->country->riskScore->risk_level ?? 'Low'
            ])->values();

            // Live API Status checks (hanya domain yang masih punya sync log — weather/economy/currency
            // sekarang real-time murni, tanpa job sync yang bisa gagal/dicatat)
            $apiStatus = [];
            $stages = [
                'news' => 'GNews API',
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
                        'criticalRisk'     => $critical,
                        'highRisk'         => $high,
                        'mediumRisk'       => $medium,
                        'lowRisk'          => $low,
                        'lastSyncStr'      => $lastSyncStr,
                    ],
                    'riskProfile'          => $riskProfile,
                    'newsSentiment'        => $newsSentiment,
                    'topRisks'             => $topRisks->toArray(),
                    'riskTrend'            => $riskTrend->toArray(),
                    'topSafe'              => $topSafe->toArray(),
                    'latestNewsList'       => $latestNewsList->toArray(),
                    'activePorts'          => $activePorts->toArray(),
                    'recentWatchlist'      => $recentWatchlist->toArray(),
                    'criticalWarnings'     => $criticalWarnings->toArray(),
                    'alertCount'           => $alertCount,
                    'apiStatus'            => $apiStatus,
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
