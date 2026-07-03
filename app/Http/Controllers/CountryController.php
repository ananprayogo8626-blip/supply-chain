<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::latest()->get();

        return view('countries.index', compact('countries'));
    }

    public function create()
    {
        return view('countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required',
            'country_code' => 'required|unique:countries,country_code',
            'capital' => 'required',
            'region' => 'required',
            'currency' => 'required',
            'language' => 'required',
            'population' => 'required|numeric',
            'flag' => 'nullable',
        ]);

        Country::create($request->all());

        return redirect()
            ->route('countries.index')
            ->with('success', 'Negara berhasil ditambahkan.');
    }

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required',
            'country_code' => 'required',
            'capital' => 'required',
            'region' => 'required',
            'currency' => 'required',
            'language' => 'required',
            'population' => 'required|numeric',
            'flag' => 'nullable',
        ]);

        $country->update($request->all());

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil diperbarui.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()
            ->route('countries.index')
            ->with('success', 'Data negara berhasil dihapus.');
    }
}