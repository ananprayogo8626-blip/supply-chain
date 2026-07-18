<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PortController extends Controller
{
    /**
     * Menampilkan semua data pelabuhan
     */
    public function index(Request $request)
    {
        $query = Port::with('country');

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('port_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('port_code', 'like', "%{$search}%")
                  ->orWhere('unlocode', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->country) {
            $query->where('country_id', $request->country);
        }

        // Filter by status (includes trash check)
        if ($request->status === 'trash') {
            $query->onlyTrashed();
        } elseif ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->type) {
            $query->where('port_type', $request->type);
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $sortDirection = str_ends_with($sort, '_desc') ? 'desc' : 'asc';
        $sortField = str_replace('_desc', '', str_replace('_asc', '', $sort));

        if ($sortField === 'country') {
            $query->join('countries', 'ports.country_id', '=', 'countries.id')
                  ->orderBy('countries.country_name', $sortDirection)
                  ->select('ports.*', 'countries.country_name as country_name');
        } else {
            $query->orderBy('ports.' . $sortField, $sortDirection);
        }

        // Pagination
        $ports = $query->paginate(15)->withQueryString();

        // Get countries for filter dropdown
        $countries = Country::orderBy('country_name')->get();

        return view('ports.index', compact('ports', 'countries'));
    }

    /**
     * Menampilkan form tambah pelabuhan
     */
    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('ports.create', compact('countries'));
    }

    /**
     * Menyimpan data pelabuhan
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name' => 'required',
            'port_code' => 'nullable',
            'city' => 'nullable',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'port_type' => 'nullable',
            'status' => 'required',
            'description' => 'nullable',
        ]);

        $port = Port::create($request->all());

        ActivityLog::log('Create', "Created Port: {$port->port_name} (#{$port->id})", $port);

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit pelabuhan
     */
    public function edit(Port $port)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('ports.edit', compact('port', 'countries'));
    }

    /**
     * Show port detail page
     */
    public function show(Port $port)
    {
        $port->load('country');
        return view('ports.show', compact('port'));
    }

    /**
     * Update data pelabuhan
     */
    public function update(Request $request, Port $port)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name' => 'required',
            'port_code' => 'nullable',
            'city' => 'nullable',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'port_type' => 'nullable',
            'status' => 'required',
            'description' => 'nullable',
        ]);

        $port->update($request->all());

        ActivityLog::log('Update', "Updated Port: {$port->port_name} (#{$port->id})", $port);

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil diperbarui.');
    }

    /**
     * Hapus data pelabuhan
     */
    public function destroy(Port $port)
    {
        $port->delete();

        ActivityLog::log('Delete', "Soft-deleted Port: {$port->port_name} (#{$port->id})", $port);

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil dihapus.');
    }

    /**
     * Restore data pelabuhan
     */
    public function restore($id)
    {
        $port = Port::onlyTrashed()->findOrFail($id);
        $port->restore();

        ActivityLog::log('Restore', "Restored Port: {$port->port_name} (#{$port->id})", $port);

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil dipulihkan.');
    }

    /**
     * Bulk import port data from World Port Index dataset
     */
    public function import()
    {
        try {
            $importProgress = \App\Models\ImportProgress::updateOrCreate(
                ['service' => 'ports'],
                [
                    'service' => 'ports',
                    'status' => 'pending',
                    'processed' => 0,
                    'total' => 0,
                    'percentage' => 0,
                    'stage' => 'Preparing...',
                    'started_at' => now(),
                    'finished_at' => null,
                    'error_message' => null,
                ]
            );

            \App\Jobs\ImportPortsJob::dispatch($importProgress->id);
            ActivityLog::log('Sync', 'Dispatched bulk Port import job.');

            return redirect()->route('ports.index')
                ->with('success', 'Port import started. This will run in the background.');
        } catch (\Exception $e) {
            return redirect()->route('ports.index')
                ->with('error', 'Failed to start port import: ' . $e->getMessage());
        }
    }

    /**
     * Export ports to CSV
     */
    public function exportCsv()
    {
        $ports = Port::with('country')->get();
        $headers = ['ID', 'Port Name', 'Port Code', 'City', 'Country Name', 'Latitude', 'Longitude', 'Type', 'Status', 'Capacity'];

        return \App\Services\ExportImportHelper::exportCsv('ports', $headers, $ports, function($p) {
            return [
                $p->id,
                $p->port_name,
                $p->port_code ?? '—',
                $p->city ?? '—',
                $p->country->country_name ?? '—',
                $p->latitude,
                $p->longitude,
                $p->port_type ?? '—',
                $p->status,
                $p->capacity ?? '—',
            ];
        });
    }

    /**
     * Export ports to PDF
     */
    public function exportPdf()
    {
        $ports = Port::with('country')->get();
        $headers = ['ID', 'Port Name', 'Code', 'City', 'Country', 'Type', 'Status', 'Capacity'];
        $rows = [];
        foreach ($ports as $p) {
            $rows[] = [
                $p->id,
                $p->port_name,
                $p->port_code ?? '—',
                $p->city ?? '—',
                $p->country->country_name ?? '—',
                $p->port_type ?? '—',
                $p->status,
                $p->capacity ?? '—',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Ports Database', $headers, $rows);
    }

    /**
     * Import ports from CSV
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

                $name = $row[1] ?? '';
                $code = $row[2] ?? '';
                $city = $row[3] ?? '';
                $countryCode = $row[4] ?? '';
                $lat = $row[5] ?? 0.0;
                $lng = $row[6] ?? 0.0;
                $type = $row[7] ?? 'Port';
                $status = $row[8] ?? 'Open';
                $cap = $row[9] ?? null;

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $port = Port::withTrashed()->updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'port_name' => $name
                    ],
                    [
                        'port_code' => $code,
                        'city' => $city,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'port_type' => $type,
                        'status' => $status,
                        'capacity' => $cap,
                    ]
                );

                if ($port->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $port->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} ports, updated {$updated} ports from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new ports created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}