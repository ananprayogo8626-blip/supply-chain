<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyData;
use Illuminate\Http\Request;

use App\Services\ExchangeRateService;

class CurrencyController extends Controller
{
    public function index()
    {
        $currency = CurrencyData::with('country')->orderByDesc('last_updated')->get();

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

        $currency = CurrencyData::create($request->all());
        $country = Country::find($request->country_id);
        if ($country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($country);
        }

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil ditambahkan.');
    }

    public function sync(Country $country, ExchangeRateService $service)
    {
        try {
            $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
            $currencyCode = $currencyCodes[0] ?? null;

            if (!$currencyCode) {
                return redirect()->route('currency.index')
                    ->with('error', 'Negara ini tidak memiliki kode mata uang.');
            }

            $rate = $service->getRate($currencyCode, 'USD');

            if ($rate === null) {
                return redirect()->route('currency.index')
                    ->with('error', 'Gagal mendapatkan nilai tukar dari ExchangeRate API.');
            }

            $existing = CurrencyData::where('country_id', $country->id)
                ->where('currency_code', $currencyCode)
                ->first();

            $oldRate = $existing ? (float) $existing->exchange_rate : 0.0;
            $changePercentage = 0.0;
            if ($oldRate > 0) {
                $changePercentage = (($rate - $oldRate) / $oldRate) * 100;
            }

            CurrencyData::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'currency_code' => $currencyCode,
                ],
                [
                    'currency_name' => $currencyCode,
                    'base_currency' => 'USD',
                    'exchange_rate' => $rate,
                    'change_percentage' => $changePercentage,
                    'last_updated' => now(),
                ]
            );

            app(\App\Services\RiskScoreEngine::class)->calculate($country);

            return redirect()->route('currency.index')
                ->with('success', 'Data nilai tukar berhasil disinkronkan.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("CurrencyController@sync error for country {$country->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('currency.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan data mata uang: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import currency data for all countries
     */
    public function import(ExchangeRateService $service)
    {
        try {
            \App\Jobs\ImportCurrencyJob::dispatch();

            return redirect()->route('currency.index')
                ->with('success', 'Currency import job dispatched. Processing in background.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("CurrencyController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('currency.index')
                ->with('error', 'Terjadi kesalahan saat menyinkronkan data mata uang: ' . $e->getMessage());
        }
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
        if ($currency->country) {
            app(\App\Services\RiskScoreEngine::class)->calculate($currency->country);
        }

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