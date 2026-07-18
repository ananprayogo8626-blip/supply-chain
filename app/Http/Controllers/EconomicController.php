<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use App\Models\ActivityLog;
use App\Services\WorldBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EconomicController extends Controller
{
    protected $worldBank;

    public function __construct(WorldBankService $worldBank)
    {
        $this->worldBank = $worldBank;
    }

    public function index(Request $request)
    {
        $query = EconomicData::with('country');

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

        $economy = $query->latest()->paginate(15)->withQueryString();

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

        DB::beginTransaction();
        try {
            $economy = EconomicData::create($request->all());
            $country = Country::find($request->country_id);
            if ($country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($country);
            }
            DB::commit();

            ActivityLog::log('Create', "Created Economy record for Country ID: {$economy->country_id} (#{$economy->id})", $economy);

            return redirect()->route('economy.index')
                ->with('success', 'Data ekonomi berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("EconomicController@store error: " . $e->getMessage());
            return redirect()->route('economy.index')
                ->with('error', 'Gagal menambahkan data ekonomi: ' . $e->getMessage());
        }
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

            $economy = EconomicData::updateOrCreate(
                [
                    'country_id' => $country->id,
                ],
                [
                    'gdp' => $gdp['value'] ?? 0,
                    'inflation' => $inflation['value'] ?? 0,
                    'exports' => $exports['value'] ?? 0,
                    'imports' => $imports['value'] ?? 0,
                    'trade_balance' => ($exports['value'] ?? 0) - ($imports['value'] ?? 0),
                    'population' => $population['value'] ?? 0,
                    'data_year' => $gdp['year'] ?? date('Y'),
                ]
            );

            app(\App\Services\RiskScoreEngine::class)->calculate($country);

            ActivityLog::log('Sync', "Synced Economy record for Country: {$country->country_name} (#{$economy->id})", $economy);

            return redirect()->route('economy.index')
                ->with('success', 'Data ekonomi berhasil diambil dari World Bank API.');
        } catch (\Throwable $e) {
            Log::error("EconomicController@sync error for country {$country->id}: " . $e->getMessage(), [
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
            ActivityLog::log('Sync', 'Dispatched bulk Economy import job.');
            return response()->json([
                'status' => 'success',
                'message' => 'Economy import job dispatched. Processing in background.',
            ]);
        } catch (\Throwable $e) {
            Log::error("EconomicController@import error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyinkronkan data ekonomi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(EconomicData $economy)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('economy.edit', compact('economy', 'countries'));
    }

    /**
     * Show economy detail page
     */
    public function show(EconomicData $economy)
    {
        $economy->load('country');
        return view('economy.show', compact('economy'));
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

        DB::beginTransaction();
        try {
            $economy->update($request->all());
            if ($economy->country) {
                app(\App\Services\RiskScoreEngine::class)->calculate($economy->country);
            }
            DB::commit();

            ActivityLog::log('Update', "Updated Economy record for Country ID: {$economy->country_id} (#{$economy->id})", $economy);

            return redirect()->route('economy.index')
                ->with('success', 'Data ekonomi berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("EconomicController@update error: " . $e->getMessage());
            return redirect()->route('economy.index')
                ->with('error', 'Gagal memperbarui data ekonomi: ' . $e->getMessage());
        }
    }

    public function destroy(EconomicData $economy)
    {
        $economy->delete();

        ActivityLog::log('Delete', "Soft-deleted Economy record ID: {$economy->id}", $economy);

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil dihapus.');
    }

    public function restore($id)
    {
        $economy = EconomicData::onlyTrashed()->findOrFail($id);
        $economy->restore();

        ActivityLog::log('Restore', "Restored Economy record ID: {$economy->id}", $economy);

        return redirect()->route('economy.index')
            ->with('success', 'Data ekonomi berhasil dipulihkan.');
    }

    /**
     * Export economy to CSV
     */
    public function exportCsv()
    {
        $economy = EconomicData::with('country')->get();
        $headers = ['ID', 'Country Name', 'GDP ($)', 'Inflation (%)', 'Exports ($)', 'Imports ($)', 'Trade Balance ($)', 'Population'];

        return \App\Services\ExportImportHelper::exportCsv('economy_data', $headers, $economy, function($e) {
            return [
                $e->id,
                $e->country->country_name ?? '—',
                $e->gdp,
                $e->inflation,
                $e->exports,
                $e->imports,
                $e->trade_balance,
                $e->population,
            ];
        });
    }

    /**
     * Export economy to PDF
     */
    public function exportPdf()
    {
        $economy = EconomicData::with('country')->get();
        $headers = ['ID', 'Country', 'GDP', 'Inflation', 'Exports', 'Imports', 'Balance'];
        $rows = [];
        foreach ($economy as $e) {
            $rows[] = [
                $e->id,
                $e->country->country_name ?? '—',
                '$' . number_format($e->gdp / 1e9, 2) . 'B',
                $e->inflation . '%',
                '$' . number_format($e->exports / 1e9, 2) . 'B',
                '$' . number_format($e->imports / 1e9, 2) . 'B',
                '$' . number_format($e->trade_balance / 1e9, 2) . 'B',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Economy Database', $headers, $rows);
    }

    /**
     * Import economy from CSV
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
                $gdp = $row[2] ?? 0;
                $inflation = $row[3] ?? 0;
                $exports = $row[4] ?? 0;
                $imports = $row[5] ?? 0;
                $pop = $row[7] ?? 0;

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $eData = EconomicData::withTrashed()->updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'gdp' => $gdp,
                        'inflation' => $inflation,
                        'exports' => $exports,
                        'imports' => $imports,
                        'trade_balance' => $exports - $imports,
                        'population' => $pop,
                        'data_year' => date('Y'),
                    ]
                );

                if ($eData->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $eData->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} economy records, updated {$updated} records from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new economy records created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}