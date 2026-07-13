<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Menampilkan semua data negara
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Country::with(['riskScore', 'weatherData', 'economicData', 'currencyData']);

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
        $perPage = in_array($perPage, [25, 50, 100]) ? $perPage : 25;

        $countries = $query->paginate($perPage)->withQueryString();

        return view('countries.index', compact('countries'));
    }

    /**
     * Form tambah negara
     */
    public function create()
    {
        return view('countries.create');
    }

    /**
     * Simpan negara manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required',
            'country_code' => 'required|unique:countries,country_code',
            'capital'      => 'required',
            'region'       => 'required',
            'currency'     => 'required',
            'language'     => 'required',
            'population'   => 'required|numeric',
            'flag'         => 'nullable',
        ]);

        Country::create($request->all());

        return redirect()
            ->route('countries.index')
            ->with('success', 'Negara berhasil ditambahkan.');
    }

    /**
     * Import data negara dari REST Countries API
     */
    public function import()
    {
        try {
            \App\Jobs\ImportCountriesJob::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Countries import started'
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("CountryController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to start countries import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Form edit
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
            'country_name' => 'required',
            'country_code' => 'required',
            'capital'      => 'required',
            'region'       => 'required',
            'currency'     => 'required',
            'language'     => 'required',
            'population'   => 'required|numeric',
            'flag'         => 'nullable',
        ]);

        $country->update($request->all());

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

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil dihapus.');
    }
}