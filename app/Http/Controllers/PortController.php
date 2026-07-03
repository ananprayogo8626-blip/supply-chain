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
    public function index()
    {
        $ports = Port::with('country')->latest()->get();

        return view('ports.index', compact('ports'));
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
}