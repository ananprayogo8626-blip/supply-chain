<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyData;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Services\ExchangeRateService;
use App\Repositories\CurrencyRepository;
use App\Models\ImportProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected $currencyRepo;

    public function __construct(CurrencyRepository $currencyRepo)
    {
        $this->currencyRepo = $currencyRepo;
    }

    public function index(Request $request)
    {
        $currency = $this->currencyRepo->getAllWithCountries($request);

        // Check if latest sync failed
        $latestProgress = ImportProgress::where('service', 'currency')->latest()->first();
        $showWarning = $latestProgress && $latestProgress->status === 'failed';

        return view('currency.index', compact('currency', 'showWarning'));
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

        DB::beginTransaction();
        try {
            $currency = CurrencyData::create($request->all());
            $country = Country::find($request->country_id);
            if ($country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($country);
            }
            DB::commit();

            ActivityLog::log('Create', "Created Currency record for Country ID: {$currency->country_id} (#{$currency->id})", $currency);

            return redirect()->route('currency.index')
                ->with('success', 'Data currency berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("CurrencyController@store error: " . $e->getMessage());
            return redirect()->route('currency.index')
                ->with('error', 'Gagal menambahkan data currency: ' . $e->getMessage());
        }
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

            $currency = CurrencyData::updateOrCreate(
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

            ActivityLog::log('Sync', "Synced Currency data for Country: {$country->country_name} (#{$currency->id})", $currency);

            return redirect()->route('currency.index')
                ->with('success', 'Data nilai tukar berhasil disinkronkan.');
        } catch (\Throwable $e) {
            Log::error("CurrencyController@sync error for country {$country->id}: " . $e->getMessage(), [
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
            ActivityLog::log('Sync', 'Dispatched bulk Currency import job.');
            return response()->json([
                'status' => 'success',
                'message' => 'Currency import job dispatched. Processing in background.',
            ]);
        } catch (\Throwable $e) {
            Log::error("CurrencyController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyinkronkan data mata uang: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(CurrencyData $currency)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('currency.edit', compact('currency', 'countries'));
    }

    /**
     * Show currency detail page
     */
    public function show(CurrencyData $currency)
    {
        $currency->load('country');
        return view('currency.show', compact('currency'));
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

        DB::beginTransaction();
        try {
            $currency->update($request->all());
            if ($currency->country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($currency->country);
            }
            DB::commit();

            ActivityLog::log('Update', "Updated Currency record for Country ID: {$currency->country_id} (#{$currency->id})", $currency);

            return redirect()->route('currency.index')
                ->with('success', 'Data currency berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("CurrencyController@update error: " . $e->getMessage());
            return redirect()->route('currency.index')
                ->with('error', 'Gagal memperbarui data currency: ' . $e->getMessage());
        }
    }

    public function destroy(CurrencyData $currency)
    {
        $currency->delete();

        ActivityLog::log('Delete', "Soft-deleted Currency record ID: {$currency->id}", $currency);

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil dihapus.');
    }

    public function restore($id)
    {
        $currency = CurrencyData::onlyTrashed()->findOrFail($id);
        $currency->restore();

        ActivityLog::log('Restore', "Restored Currency record ID: {$currency->id}", $currency);

        return redirect()->route('currency.index')
            ->with('success', 'Data currency berhasil dipulihkan.');
    }

    /**
     * Export currency to CSV
     */
    public function exportCsv()
    {
        $currency = CurrencyData::with('country')->get();
        $headers = ['ID', 'Country Name', 'Currency Code', 'Currency Name', 'Base Currency', 'Exchange Rate', 'Change (%)'];

        return \App\Services\ExportImportHelper::exportCsv('currency_data', $headers, $currency, function($c) {
            return [
                $c->id,
                $c->country->country_name ?? '—',
                $c->currency_code,
                $c->currency_name,
                $c->base_currency,
                $c->exchange_rate,
                $c->change_percentage,
            ];
        });
    }

    /**
     * Export currency to PDF
     */
    public function exportPdf()
    {
        $currency = CurrencyData::with('country')->get();
        $headers = ['ID', 'Country', 'Code', 'Currency Name', 'Base', 'Rate', 'Change'];
        $rows = [];
        foreach ($currency as $c) {
            $rows[] = [
                $c->id,
                $c->country->country_name ?? '—',
                $c->currency_code,
                $c->currency_name,
                $c->base_currency,
                number_format($c->exchange_rate, 4),
                number_format($c->change_percentage, 2) . '%',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Currency Exchange Rates', $headers, $rows);
    }

    /**
     * Import currency from CSV
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

                $countryCode = $row[1] ?? '';
                $currCode = $row[2] ?? '';
                $currName = $row[3] ?? '';
                $base = $row[4] ?? 'USD';
                $rate = $row[5] ?? 1.0;
                $change = $row[6] ?? 0.0;

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $cData = CurrencyData::withTrashed()->updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'currency_code' => $currCode
                    ],
                    [
                        'currency_name' => $currName,
                        'base_currency' => $base,
                        'exchange_rate' => $rate,
                        'change_percentage' => $change,
                        'last_updated' => now(),
                    ]
                );

                if ($cData->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $cData->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} currency records, updated {$updated} records from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new currency records created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}