<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use App\Services\WorldBankService;
use Illuminate\Http\Request;

class EconomicController extends Controller
{
    protected $worldBank;

    public function __construct(WorldBankService $worldBank)
    {
        $this->worldBank = $worldBank;
    }

    public function index()
    {
        $economy = EconomicData::with('country')->latest()->get();

        return view('economy.index', compact('economy'));
    }

    public function create()
    {
        $countries = Country::orderBy('country_name')->get();

        return view('economy.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'gdp' => 'required|numeric',
            'inflation' => 'required|numeric',
            'exports' => 'required|numeric',
            'imports' => 'required|numeric',
            'population' => 'required|numeric',
            'data_year' => 'required',
        ]);

        $economy = EconomicData::create($request->all());
        $country = Country::find($request->country_id);
        if ($country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($country);
        }

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil ditambahkan.');
    }

    /**
     * Sinkronisasi dari World Bank API
     */
    public function sync(Country $country)
    {
        try {
            $gdp = $this->worldBank->getIndicator($country->country_code, 'NY.GDP.MKTP.CD');
            $inflation = $this->worldBank->getIndicator($country->country_code, 'FP.CPI.TOTL.ZG');
            $exports = $this->worldBank->getIndicator($country->country_code, 'NE.EXP.GNFS.CD');
            $imports = $this->worldBank->getIndicator($country->country_code, 'NE.IMP.GNFS.CD');
            $population = $this->worldBank->getIndicator($country->country_code, 'SP.POP.TOTL');

            EconomicData::updateOrCreate(
                [
                    'country_id' => $country->id,
                ],
                [
                    'gdp' => $gdp['value'] ?? 0,
                    'inflation' => $inflation['value'] ?? 0,
                    'exports' => $exports['value'] ?? 0,
                    'imports' => $imports['value'] ?? 0,
                    'population' => $population['value'] ?? 0,
                    'data_year' => $gdp['year'] ?? date('Y'),
                ]
            );

            app(\App\Services\RiskScoreEngine::class)->calculate($country);

            return redirect()->route('economy.index')
                ->with('success', 'Data ekonomi berhasil diambil dari World Bank API.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("EconomicController@sync error for country {$country->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('economy.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan data ekonomi: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import economy data for all countries
     */
    public function import()
    {
        try {
            \App\Jobs\ImportEconomyJob::dispatch();

            return redirect()->route('economy.index')
                ->with('success', 'Economy import job dispatched. Processing in background.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("EconomicController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('economy.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan data ekonomi: ' . $e->getMessage());
        }
    }

    public function edit(EconomicData $economy)
    {
        $countries = Country::orderBy('country_name')->get();

        return view('economy.edit', compact('economy', 'countries'));
    }

    public function update(Request $request, EconomicData $economy)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'gdp' => 'required|numeric',
            'inflation' => 'required|numeric',
            'exports' => 'required|numeric',
            'imports' => 'required|numeric',
            'population' => 'required|numeric',
            'data_year' => 'required',
        ]);

        $economy->update($request->all());
        if ($economy->country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($economy->country);
        }

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil diperbarui.');
    }

    public function destroy(EconomicData $economy)
    {
        $economy->delete();

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil dihapus.');
    }
}