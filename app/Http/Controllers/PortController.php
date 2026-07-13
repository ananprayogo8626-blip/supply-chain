<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;

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

        // Filter by status
        if ($request->status) {
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
                  ->select('ports.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $ports = $query->paginate(50);

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

        Port::create($request->all());

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

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil diperbarui.');
    }

    /**
     * Hapus data pelabuhan
     */
    public function destroy(Port $port)
    {
        $port->delete();

        return redirect()->route('ports.index')
            ->with('success', 'Data pelabuhan berhasil dihapus.');
    }

    /**
     * Bulk import port data from World Port Index dataset
     */
    public function import()
    {
        try {
            // Create or reset import progress
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

            // Dispatch the import job
            \App\Jobs\ImportPortsJob::dispatch($importProgress->id);

            return redirect()->route('ports.index')
                ->with('success', 'Port import started. This will run in the background.');
        } catch (\Exception $e) {
            return redirect()->route('ports.index')
                ->with('error', 'Failed to start port import: ' . $e->getMessage());
        }
    }
}