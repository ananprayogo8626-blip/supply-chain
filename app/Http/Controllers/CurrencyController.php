<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyData;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currency = CurrencyData::with('country')->latest()->get();

        return view('currency.index', compact('currency'));
    }

    public function create()
    {
        $countries = Country::orderBy('country_name')->get();

        return view('currency.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'currency_code' => 'required',
            'currency_name' => 'required',
            'base_currency' => 'required',
            'exchange_rate' => 'required|numeric',
            'change_percentage' => 'nullable|numeric',
            'last_updated' => 'nullable|date',
        ]);

        CurrencyData::create($request->all());

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil ditambahkan.');
    }

    public function edit(CurrencyData $currency)
    {
        $countries = Country::orderBy('country_name')->get();

        return view('currency.edit', compact('currency', 'countries'));
    }

    public function update(Request $request, CurrencyData $currency)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'currency_code' => 'required',
            'currency_name' => 'required',
            'base_currency' => 'required',
            'exchange_rate' => 'required|numeric',
            'change_percentage' => 'nullable|numeric',
            'last_updated' => 'nullable|date',
        ]);

        $currency->update($request->all());

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil diperbarui.');
    }

    public function destroy(CurrencyData $currency)
    {
        $currency->delete();

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil dihapus.');
    }
}