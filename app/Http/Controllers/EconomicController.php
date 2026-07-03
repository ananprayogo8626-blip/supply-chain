<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use Illuminate\Http\Request;

class EconomicController extends Controller
{
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

        EconomicData::create($request->all());

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil ditambahkan.');
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