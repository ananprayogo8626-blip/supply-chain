<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;

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
    public function index()
    {
        $weather = WeatherData::with('country')->latest()->get();

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

        $weather = WeatherData::create($request->all());
        $country = Country::find($request->country_id);
        if ($country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($country);
        }

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil ditambahkan.');
    }

    /**
     * Sinkronisasi data cuaca dari Open-Meteo API
     */
    public function sync(Country $country)
    {
        try {
            // Use existing coordinates, fallback to geocoding if missing
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

            WeatherData::updateOrCreate(
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

            return redirect()
                ->route('weather.index')
                ->with('success', 'Data cuaca berhasil diambil dari Open-Meteo API.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("WeatherController@sync error for country {$country->id}: " . $e->getMessage(), [
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

            return redirect()
                ->route('weather.index')
                ->with('success', 'Weather import job dispatched. Processing in background.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("WeatherController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('weather.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan cuaca: ' . $e->getMessage());
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

        $weather->update($request->all());
        if ($weather->country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($weather->country);
        }

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil diperbarui.');
    }

    /**
     * Hapus data cuaca
     */
    public function destroy(WeatherData $weather)
    {
        $weather->delete();

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil dihapus.');
    }
}