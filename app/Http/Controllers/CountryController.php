<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ActivityLog;
use App\Services\CountryService;
use App\Services\LiveCountryDataService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $countryService;
    protected $liveDataService;

    public function __construct(CountryService $countryService, LiveCountryDataService $liveDataService)
    {
        $this->countryService = $countryService;
        $this->liveDataService = $liveDataService;
    }

    /**
     * Menampilkan semua data negara
     */
    public function index(Request $request)
    {
        $query = Country::with(['riskScore']);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('country_name', 'LIKE', "%{$s}%")
                  ->orWhere('country_code', 'LIKE', "%{$s}%")
                  ->orWhere('capital', 'LIKE', "%{$s}%");
            });
        }

        // Region filter
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Filter by status (trash)
        if ($request->status === 'trash') {
            $query->onlyTrashed();
        }

        // Sorting
        $sort = $request->input('sort', 'country_name');
        match ($sort) {
            'country_name_desc' => $query->orderBy('country_name', 'desc'),
            'population_desc'   => $query->orderByDesc('population'),
            'population_asc'    => $query->orderBy('population'),
            'region'            => $query->orderBy('region')->orderBy('country_name'),
            default             => $query->orderBy('country_name'),
        };

        $perPage = (int) $request->input('per_page', 25);
        $countries = $query->paginate($perPage)->withQueryString();

        // Regions list
        $regions = Country::distinct()->whereNotNull('region')->pluck('region');

        return view('countries.index', compact('countries', 'regions'));
    }

    /**
     * Sinkronisasi data negara via REST Countries API
     */
    public function import(CountryService $service)
    {
        try {
            $service->syncCountries();
            ActivityLog::log('Sync', 'Synchronized countries with REST Countries API.');
            return redirect()
                ->route('countries.index')
                ->with('success', 'Sinkronisasi data negara berhasil.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('countries.index')
                ->with('error', 'Gagal sinkronisasi data negara: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form tambah negara
     */
    public function create()
    {
        return view('countries.create');
    }

    /**
     * Menyimpan data negara baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:countries,country_code',
            'capital'      => 'nullable|string|max:255',
            'region'       => 'nullable|string|max:255',
            'currency'     => 'nullable|string|max:255',
            'language'     => 'nullable|string|max:255',
            'population'   => 'nullable|integer',
        ]);

        $country = Country::create($request->all());

        ActivityLog::log('Create', "Created Country: {$country->country_name} (#{$country->id})", $country);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Negara berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail negara
     */
    public function show(Country $country)
    {
        $country->load(['riskScore', 'news' => fn($q) => $q->latest()->limit(5), 'ports']);
        $this->liveDataService->attachLiveData($country);

        $riskHistory = \App\Models\RiskHistory::where('country_id', $country->id)
            ->orderBy('calculated_at', 'asc')
            ->take(30)
            ->get();

        return view('countries.show', compact('country', 'riskHistory'));
    }

    /**
     * Get country data for dashboard (AJAX)
     */
    public function dashboardData(Country $country)
    {
        $country->load(['riskScore', 'news' => fn($q) => $q->latest()->limit(3), 'ports']);
        $this->liveDataService->attachLiveData($country);

        return response()->json([
            'country' => [
                'id' => $country->id,
                'country_name' => $country->country_name,
                'country_code' => $country->country_code,
                'capital' => $country->capital,
                'region' => $country->region,
                'subregion' => $country->subregion,
                'population' => $country->population,
                'area' => $country->area,
                'timezone' => $country->timezone,
                'currency' => $country->currency,
                'languages' => $country->language,
                'flag' => $country->flag,
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
            ],
            'risk_score' => $country->riskScore ? [
                'total_score' => $country->riskScore->total_score,
                'risk_level' => $country->riskScore->risk_level,
                'weather_score' => $country->riskScore->weather_score,
                'inflation_score' => $country->riskScore->economic_score,
                'exchange_rate_score' => $country->riskScore->currency_score,
                'news_sentiment_score' => $country->riskScore->news_score,
                'recommendation' => $country->riskScore->recommendation,
            ] : null,
            'weather' => $country->weatherData ? [
                'temperature' => $country->weatherData->temperature,
                'humidity' => $country->weatherData->humidity,
                'wind_speed' => $country->weatherData->wind_speed,
                'pressure' => $country->weatherData->pressure,
                'rainfall' => $country->weatherData->rainfall,
                'weather_condition' => $country->weatherData->weather_condition,
                'last_updated' => $country->weatherData->updated_at,
            ] : null,
            'economy' => $country->economicData ? [
                'gdp' => $country->economicData->gdp,
                'gdp_growth' => $country->economicData->gdp_growth,
                'inflation' => $country->economicData->inflation,
                'exports' => $country->economicData->exports,
                'imports' => $country->economicData->imports,
                'trade_balance' => $country->economicData->trade_balance,
                'year' => $country->economicData->data_year,
            ] : null,
            'currency' => $country->currencyData ? [
                'currency_code' => $country->currencyData->currency_code,
                'currency_name' => $country->currencyData->currency_name,
                'exchange_rate' => $country->currencyData->exchange_rate,
                'buy' => $country->currencyData->buy,
                'sell' => $country->currencyData->sell,
                'change_percentage' => $country->currencyData->change_percentage,
                'last_updated' => $country->currencyData->last_updated,
            ] : null,
            'news' => $country->news->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'summary' => $n->summary,
                'source' => $n->source,
                'image' => $n->image,
                'sentiment' => $n->sentiment,
                'published_at' => $n->published_at,
                'url' => $n->url,
            ]),
            'ports_count' => $country->ports->count(),
        ]);
    }

    /**
     * Menampilkan form edit negara
     */
    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    /**
     * Update data negara
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:countries,country_code,' . $country->id,
            'capital'      => 'required',
            'region'       => 'required',
            'currency'     => 'required',
            'language'     => 'required',
            'population'   => 'required|numeric',
            'flag'         => 'nullable',
        ]);

        $country->update($request->all());

        ActivityLog::log('Update', "Updated Country: {$country->country_name} (#{$country->id})", $country);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil diperbarui.');
    }

    /**
     * Hapus data negara
     */
    public function destroy(Country $country)
    {
        $country->delete();

        ActivityLog::log('Delete', "Soft-deleted Country: {$country->country_name} (#{$country->id})", $country);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil dihapus.');
    }

    /**
     * Restore data negara
     */
    public function restore($id)
    {
        $country = Country::onlyTrashed()->findOrFail($id);
        $country->restore();

        ActivityLog::log('Restore', "Restored Country: {$country->country_name} (#{$country->id})", $country);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil dipulihkan.');
    }

    /**
     * Export countries to CSV
     */
    public function exportCsv()
    {
        $countries = Country::all();
        $headers = ['ID', 'Country Name', 'Country Code', 'Capital', 'Region', 'Currency', 'Language', 'Population'];
        
        return \App\Services\ExportImportHelper::exportCsv('countries', $headers, $countries, function($country) {
            return [
                $country->id,
                $country->country_name,
                $country->country_code,
                $country->capital ?? '—',
                $country->region ?? '—',
                $country->currency ?? '—',
                $country->language ?? '—',
                $country->population ?? 0,
            ];
        });
    }

    /**
     * Export countries to PDF
     */
    public function exportPdf()
    {
        $countries = Country::all();
        $headers = ['ID', 'Name', 'Code', 'Capital', 'Region', 'Currency', 'Population'];
        $rows = [];
        foreach ($countries as $c) {
            $rows[] = [
                $c->id,
                $c->country_name,
                $c->country_code,
                $c->capital ?? '—',
                $c->region ?? '—',
                $c->currency ?? '—',
                $c->population ? number_format($c->population) : '0',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Countries List', $headers, $rows);
    }

    /**
     * Import countries from CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle);
            $imported = 0;
            $updated = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;

                $name = $row[1] ?? '';
                $code = $row[2] ?? '';
                $capital = $row[3] ?? null;
                $region = $row[4] ?? null;
                $currency = $row[5] ?? null;
                $language = $row[6] ?? null;
                $population = $row[7] ?? null;

                if (empty($code) || empty($name)) continue;

                $country = Country::withTrashed()->updateOrCreate(
                    ['country_code' => $code],
                    [
                        'country_name' => $name,
                        'capital' => $capital,
                        'region' => $region,
                        'currency' => $currency,
                        'language' => $language,
                        'population' => $population,
                    ]
                );

                if ($country->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $country->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} countries, updated {$updated} countries from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new countries created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}