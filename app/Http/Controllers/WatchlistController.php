<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Country;
use App\Models\ActivityLog;
use App\Services\LiveCountryDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WatchlistController extends Controller
{
    public function __construct(protected LiveCountryDataService $liveDataService)
    {
    }

    /**
     * Menampilkan semua watchlist
     */
    public function index(Request $request)
    {
        $query = Watchlist::with(['country.riskScore', 'country.news'])
            ->where('user_id', Auth::id());

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('company_name', 'LIKE', "%{$s}%")
                  ->orWhere('industry', 'LIKE', "%{$s}%")
                  ->orWhereHas('country', function($cq) use ($s) {
                      $cq->where('country_name', 'LIKE', "%{$s}%");
                  });
            });
        }

        // Filter Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter Status (includes trash)
        if ($request->status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $watchlists = $query->latest()->paginate(15)->withQueryString();

        foreach ($watchlists as $watchlist) {
            if ($watchlist->country) {
                $this->liveDataService->attachLiveData($watchlist->country);
            }
        }

        return view('watchlists.index', compact('watchlists'));
    }

    /**
     * Form tambah watchlist
     */
    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('watchlists.create', compact('countries'));
    }

    /**
     * Simpan watchlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|in:Monitoring,Critical,Resolved',
            'is_active' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $watchlist = Watchlist::create([
            'user_id' => Auth::id(),
            'country_id' => $request->country_id,
            'company_name' => $request->company_name,
            'industry' => $request->industry,
            'priority' => $request->priority,
            'status' => $request->status,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('Create', "Created Watchlist item: {$watchlist->company_name} (#{$watchlist->id})", $watchlist);

        return redirect()
            ->route('watchlists.index')
            ->with('success', 'Watchlist berhasil ditambahkan.');
    }

    /**
     * Form edit
     */
    public function edit(Watchlist $watchlist)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('watchlists.edit', compact('watchlist', 'countries'));
    }

    /**
     * Update watchlist
     */
    public function update(Request $request, Watchlist $watchlist)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'priority' => 'required|integer|min:1|max:5',
            'status' => 'required|in:Monitoring,Critical,Resolved',
            'is_active' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $watchlist->update([
            'country_id' => $request->country_id,
            'company_name' => $request->company_name,
            'industry' => $request->industry,
            'priority' => $request->priority,
            'status' => $request->status,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('Update', "Updated Watchlist item: {$watchlist->company_name} (#{$watchlist->id})", $watchlist);

        return redirect()
            ->route('watchlists.index')
            ->with('success', 'Watchlist berhasil diperbarui.');
    }

    /**
     * Hapus watchlist
     */
    public function destroy(Watchlist $watchlist)
    {
        $watchlist->delete();

        ActivityLog::log('Delete', "Soft-deleted Watchlist item: {$watchlist->company_name} (#{$watchlist->id})", $watchlist);

        return redirect()
            ->route('watchlists.index')
            ->with('success', 'Watchlist berhasil dihapus.');
    }

    /**
     * Restore watchlist
     */
    public function restore($id)
    {
        $watchlist = Watchlist::onlyTrashed()->findOrFail($id);
        $watchlist->restore();

        ActivityLog::log('Restore', "Restored Watchlist item: {$watchlist->company_name} (#{$watchlist->id})", $watchlist);

        return redirect()
            ->route('watchlists.index')
            ->with('success', 'Watchlist berhasil dipulihkan.');
    }

    /**
     * Export watchlist to CSV
     */
    public function exportCsv()
    {
        $watchlists = Watchlist::with('country')->where('user_id', Auth::id())->get();
        $headers = ['ID', 'Company Name', 'Industry', 'Country Name', 'Priority', 'Status', 'Active'];

        return \App\Services\ExportImportHelper::exportCsv('watchlists', $headers, $watchlists, function($w) {
            return [
                $w->id,
                $w->company_name,
                $w->industry ?? '—',
                $w->country->country_name ?? '—',
                $w->priority,
                $w->status,
                $w->is_active ? 'Yes' : 'No',
            ];
        });
    }

    /**
     * Export watchlist to PDF
     */
    public function exportPdf()
    {
        $watchlists = Watchlist::with('country')->where('user_id', Auth::id())->get();
        $headers = ['ID', 'Company Name', 'Industry', 'Country', 'Priority', 'Status', 'Active'];
        $rows = [];
        foreach ($watchlists as $w) {
            $rows[] = [
                $w->id,
                $w->company_name,
                $w->industry ?? '—',
                $w->country->country_name ?? '—',
                $w->priority . '/5',
                $w->status,
                $w->is_active ? 'Yes' : 'No',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Watchlist Database', $headers, $rows);
    }

    /**
     * Import watchlist from CSV
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

                $comp = $row[1] ?? '';
                $ind = $row[2] ?? '';
                $countryCode = $row[3] ?? '';
                $prio = $row[4] ?? 3;
                $status = $row[5] ?? 'Monitoring';

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $w = Watchlist::withTrashed()->updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'company_name' => $comp
                    ],
                    [
                        'country_id' => $country->id,
                        'industry' => $ind,
                        'priority' => $prio,
                        'status' => in_array($status, ['Monitoring', 'Critical', 'Resolved']) ? $status : 'Monitoring',
                        'is_active' => true,
                    ]
                );

                if ($w->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $w->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} watchlist records, updated {$updated} records from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new watchlist records created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}