<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
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
     * Simpan data cuaca
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'temperature' => 'required|numeric',
            'wind_speed' => 'required|numeric',
            'rainfall' => 'required|numeric',
            'humidity' => 'required|numeric',
            'weather_condition' => 'required',
            'storm_risk' => 'required|integer|min:0|max:100',
        ]);

        WeatherData::create($request->all());

        return redirect()
            ->route('weather.index')
            ->with('success', 'Data cuaca berhasil ditambahkan.');
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
            'weather_condition' => 'required',
            'storm_risk' => 'required|integer|min:0|max:100',
        ]);

        $weather->update($request->all());

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