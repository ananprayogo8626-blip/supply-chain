<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    /**
     * Menampilkan semua watchlist
     */
    public function index(Request $request)
    {
        $query = Watchlist::with(['country.riskScore', 'country.weatherData', 'country.news'])
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

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $watchlists = $query->latest()->get();

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

        Watchlist::create([
            'user_id' => Auth::id(),
            'country_id' => $request->country_id,
            'company_name' => $request->company_name,
            'industry' => $request->industry,
            'priority' => $request->priority,
            'status' => $request->status,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'notes' => $request->notes,
        ]);

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
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
            'notes' => $request->notes,
        ]);

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

        return redirect()
            ->route('watchlists.index')
            ->with('success', 'Watchlist berhasil dihapus.');
    }
}