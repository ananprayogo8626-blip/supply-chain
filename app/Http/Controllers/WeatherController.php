<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\ActivityLog;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(OpenMeteoService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Menampilkan semua data cuaca
     */
    public function index(Request $request)
    {
        $query = WeatherData::with('country');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('country', function($q) use ($search) {
                $q->where('country_name', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%");
            });
        }

        // Trash filter
        if ($request->status === 'trash') {
            $query->onlyTrashed();
        }

        $weather = $query->latest()->paginate(15)->withQueryString();

        return view('weather.index', compact('weather'));
    }

    /**
     * Form tambah data cuaca
     */
    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('weather.create', compact('countries'));
    }

    /**
     * Simpan data cuaca manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'temperature' => 'required|numeric',
            'wind_speed' => 'required|numeric',
            'rainfall' => 'required|numeric',
            'humidity' => 'required|numeric',
            'cloud' => 'nullable|integer|min:0|max:100',
            'pressure' => 'nullable|numeric|min:0',
            'weather_condition' => 'required',
            'storm_risk' => 'required|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $weather = WeatherData::create($request->all());
            $country = Country::find($request->country_id);
            if ($country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($country);
            }
            DB::commit();

            ActivityLog::log('Create', "Created Weather data for Country ID: {$weather->country_id} (#{$weather->id})", $weather);

            return redirect()
                ->route('weather.index')
                ->with('success', 'Data cuaca berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("WeatherController@store error: " . $e->getMessage());
            return redirect()
                ->route('weather.index')
                ->with('error', 'Gagal menambahkan data cuaca: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi data cuaca dari Open-Meteo API
     */
    public function sync(Country $country)
    {
        try {
            $lat = $country->latitude;
            $lng = $country->longitude;

            if ($lat === null || $lng === null) {
                $geocode = app(\App\Services\GeocodingService::class)->getCoordinates($country->capital, $country->country_code);
                if ($geocode) {
                    $lat = $geocode['latitude'];
                    $lng = $geocode['longitude'];
                }
            }

            if ($lat === null || $lng === null) {
                return redirect()->route('weather.index')
                    ->with('error', 'Koordinat tidak tersedia untuk negara ini.');
            }

            $data = $this->weatherService->getWeather((float) $lat, (float) $lng);

            if (!$data) {
                return redirect()
                    ->route('weather.index')
                    ->with('error', 'Gagal mengambil data dari Open-Meteo API.');
            }

            $weather = WeatherData::updateOrCreate(
                [
                    'country_id' => $country->id,
                ],
                [
                    'temperature' => $data['temperature'],
                    'wind_speed' => $data['wind_speed'],
                    'rainfall' => $data['rainfall'],
                    'humidity' => $data['humidity'],
                    'cloud' => $data['cloud'] ?? null,
                    'pressure' => $data['pressure'] ?? null,
                    'weather_condition' => $data['weather_condition'],
                    'storm_risk' => $data['storm_risk'],
                ]
            );

            app(\App\Services\RiskScoreEngine::class)->calculate($country);

            ActivityLog::log('Sync', "Synced Weather data for Country: {$country->country_name} (#{$weather->id})", $weather);

            return redirect()
                ->route('weather.index')
                ->with('success', 'Data cuaca berhasil diambil dari Open-Meteo API.');
        } catch (\Throwable $e) {
            Log::error("WeatherController@sync error for country {$country->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('weather.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan cuaca: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import weather data for all countries
     */
    public function import()
    {
        try {
            \App\Jobs\ImportWeatherJob::dispatch();
            ActivityLog::log('Sync', 'Dispatched bulk Weather import job.');
            return response()->json([
                'status' => 'success',
                'message' => 'Weather import job dispatched. Processing in background.',
            ]);
        } catch (\Throwable $e) {
            Log::error("WeatherController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyinkronkan cuaca: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Form edit data cuaca
     */
    public function edit(WeatherData $weather)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('weather.edit', compact('weather', 'countries'));
    }

    /**
     * Show weather detail page
     */
    public function show(WeatherData $weather)
    {
        $weather->load('country');
        return view('weather.show', compact('weather'));
    }

    /**
     * Update data cuaca
     */
    public function update(Request $request, WeatherData $weather)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'temperature' => 'required|numeric',
            'wind_speed' => 'required|numeric',
            'rainfall' => 'required|numeric',
            'humidity' => 'required|numeric',
            'cloud' => 'nullable|integer|min:0|max:100',
            'pressure' => 'nullable|numeric|min:0',
            'weather_condition' => 'required',
            'storm_risk' => 'required|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $weather->update($request->all());
            if ($weather->country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($weather->country);
            }
            DB::commit();

            ActivityLog::log('Update', "Updated Weather data for Country ID: {$weather->country_id} (#{$weather->id})", $weather);

            return redirect()
                ->route('weather.index')
                ->with('success', 'Data cuaca berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("WeatherController@update error: " . $e->getMessage());
            return redirect()
                ->route('weather.index')
                ->with('error', 'Gagal memperbarui data cuaca: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data cuaca
     */
    public function destroy(WeatherData $weather)
    {
        $weather->delete();

        ActivityLog::log('Delete', "Soft-deleted Weather data ID: {$weather->id}", $weather);

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil dihapus.');
    }

    /**
     * Restore data cuaca
     */
    public function restore($id)
    {
        $weather = WeatherData::onlyTrashed()->findOrFail($id);
        $weather->restore();

        ActivityLog::log('Restore', "Restored Weather data ID: {$weather->id}", $weather);

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil dipulihkan.');
    }

    /**
     * Export weather to CSV
     */
    public function exportCsv()
    {
        $weather = WeatherData::with('country')->get();
        $headers = ['ID', 'Country Name', 'Temperature (°C)', 'Wind Speed (km/h)', 'Rainfall (mm)', 'Humidity (%)', 'Weather Condition', 'Storm Risk (%)'];

        return \App\Services\ExportImportHelper::exportCsv('weather_data', $headers, $weather, function($w) {
            return [
                $w->id,
                $w->country->country_name ?? '—',
                $w->temperature,
                $w->wind_speed,
                $w->rainfall,
                $w->humidity,
                $w->weather_condition,
                $w->storm_risk,
            ];
        });
    }

    /**
     * Export weather to PDF
     */
    public function exportPdf()
    {
        $weather = WeatherData::with('country')->get();
        $headers = ['ID', 'Country', 'Temp', 'Wind Speed', 'Rainfall', 'Humidity', 'Condition', 'Storm Risk'];
        $rows = [];
        foreach ($weather as $w) {
            $rows[] = [
                $w->id,
                $w->country->country_name ?? '—',
                $w->temperature . '°C',
                $w->wind_speed . ' km/h',
                $w->rainfall . ' mm',
                $w->humidity . '%',
                $w->weather_condition,
                $w->storm_risk . '%',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Weather Database', $headers, $rows);
    }

    /**
     * Import weather from CSV
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

                $countryCode = $row[1] ?? ''; // assuming country code is in column index 1
                $temperature = $row[2] ?? 0;
                $wind = $row[3] ?? 0;
                $rain = $row[4] ?? 0;
                $humidity = $row[5] ?? 0;
                $condition = $row[6] ?? 'Sunny';
                $storm = $row[7] ?? 0;

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $w = WeatherData::withTrashed()->updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'temperature' => $temperature,
                        'wind_speed' => $wind,
                        'rainfall' => $rain,
                        'humidity' => $humidity,
                        'weather_condition' => $condition,
                        'storm_risk' => $storm,
                    ]
                );

                if ($w->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $w->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} weather records, updated {$updated} records from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new weather records created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}