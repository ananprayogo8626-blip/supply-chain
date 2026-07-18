<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Services\RiskScoreEngine;
use Illuminate\Support\Facades\Log;

class RiskScoreController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskScore::with('country');

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

        $scores = $query->latest()->paginate(15)->withQueryString();

        return view('risk-scores.index', compact('scores'));
    }

    public function show(RiskScore $risk_score)
    {
        $risk_score->load(['country.weatherData', 'country.economicData', 'country.currencyData', 'country.news', 'country.ports']);
        return view('risk-scores.show', [
            'riskScore' => $risk_score
        ]);
    }

    /**
     * Hitung otomatis skor risiko untuk satu negara
     */
    public function calculate(Country $country, RiskScoreEngine $engine)
    {
        try {
            $score = $engine->calculate($country);

            if (!$score) {
                return redirect()->route('risk-scores.index')
                    ->with('error', 'Gagal menghitung skor risiko.');
            }

            ActivityLog::log('Calculate', "Recalculated risk score for Country: {$country->country_name} (#{$score->id})", $score);

            return redirect()->route('risk-scores.index')
                ->with('success', "Skor risiko untuk {$country->country_name} berhasil diperbarui.");
        } catch (\Throwable $e) {
            Log::error("RiskScoreController@calculate error for country {$country->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('risk-scores.index')
                ->with('error', 'Terjadi kesalahan saat menghitung skor risiko: ' . $e->getMessage());
        }
    }

    /**
     * Bulk calculate risk scores for all countries
     */
    public function calculateAll()
    {
        try {
            \App\Jobs\CalculateRiskScoresJob::dispatch();
            ActivityLog::log('Calculate', "Dispatched bulk risk score calculation job.");

            return response()->json([
                'success' => true,
                'message' => 'Risk scores calculation started. Processing in background.',
            ]);
        } catch (\Throwable $e) {
            Log::error("RiskScoreController@calculateAll error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to start risk scores calculation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('risk-scores.create', compact('countries'));
    }

    public function store(Request $request, RiskScoreEngine $engine)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'weather_score' => 'required|integer',
            'economic_score' => 'required|integer',
            'currency_score' => 'required|integer',
            'news_score' => 'required|integer',
            'port_score' => 'required|integer',
            'recommendation' => 'nullable',
            'calculated_at' => 'nullable|date',
        ]);

        $total = (int) round(
            ($request->weather_score * 0.20) +
            ($request->economic_score * 0.25) +
            ($request->currency_score * 0.20) +
            ($request->port_score * 0.15) +
            ($request->news_score * 0.20)
        );

        if ($total >= 76) {
            $level = 'Critical';
        } elseif ($total >= 51) {
            $level = 'High';
        } elseif ($total >= 26) {
            $level = 'Medium';
        } else {
            $level = 'Low';
        }

        $risk_score = RiskScore::create([
            'country_id' => $request->country_id,
            'weather_score' => $request->weather_score,
            'economic_score' => $request->economic_score,
            'currency_score' => $request->currency_score,
            'news_score' => $request->news_score,
            'port_score' => $request->port_score,
            'total_score' => $total,
            'risk_level' => $level,
            'recommendation' => $request->recommendation,
            'calculated_at' => $request->calculated_at ?? now(),
        ]);

        ActivityLog::log('Create', "Created Risk Score for Country ID: {$risk_score->country_id} (#{$risk_score->id})", $risk_score);

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil ditambahkan.');
    }

    public function edit(RiskScore $risk_score)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('risk-scores.edit', compact('risk_score', 'countries'));
    }

    public function update(Request $request, RiskScore $risk_score)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'weather_score' => 'required|integer',
            'economic_score' => 'required|integer',
            'currency_score' => 'required|integer',
            'news_score' => 'required|integer',
            'port_score' => 'required|integer',
            'recommendation' => 'nullable',
            'calculated_at' => 'nullable|date',
        ]);

        $total = (int) round(
            ($request->weather_score * 0.20) +
            ($request->economic_score * 0.25) +
            ($request->currency_score * 0.20) +
            ($request->port_score * 0.15) +
            ($request->news_score * 0.20)
        );

        if ($total >= 76) {
            $level = 'Critical';
        } elseif ($total >= 51) {
            $level = 'High';
        } elseif ($total >= 26) {
            $level = 'Medium';
        } else {
            $level = 'Low';
        }

        $risk_score->update([
            'country_id' => $request->country_id,
            'weather_score' => $request->weather_score,
            'economic_score' => $request->economic_score,
            'currency_score' => $request->currency_score,
            'news_score' => $request->news_score,
            'port_score' => $request->port_score,
            'total_score' => $total,
            'risk_level' => $level,
            'recommendation' => $request->recommendation,
            'calculated_at' => $request->calculated_at ?? now(),
        ]);

        ActivityLog::log('Update', "Updated Risk Score for Country ID: {$risk_score->country_id} (#{$risk_score->id})", $risk_score);

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil diperbarui.');
    }

    public function destroy(RiskScore $risk_score)
    {
        $risk_score->delete();

        ActivityLog::log('Delete', "Soft-deleted Risk Score ID: {$risk_score->id}", $risk_score);

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil dihapus.');
    }

    public function restore($id)
    {
        $risk_score = RiskScore::onlyTrashed()->findOrFail($id);
        $risk_score->restore();

        ActivityLog::log('Restore', "Restored Risk Score ID: {$risk_score->id}", $risk_score);

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil dipulihkan.');
    }

    /**
     * Export risk scores to CSV
     */
    public function exportCsv()
    {
        $scores = RiskScore::with('country')->get();
        $headers = ['ID', 'Country Name', 'Weather Score', 'Economic Score', 'Currency Score', 'News Score', 'Port Score', 'Total Score', 'Risk Level'];

        return \App\Services\ExportImportHelper::exportCsv('risk_scores', $headers, $scores, function($s) {
            return [
                $s->id,
                $s->country->country_name ?? '—',
                $s->weather_score,
                $s->economic_score,
                $s->currency_score,
                $s->news_score,
                $s->port_score,
                $s->total_score,
                $s->risk_level,
            ];
        });
    }

    /**
     * Export risk scores to PDF
     */
    public function exportPdf()
    {
        $scores = RiskScore::with('country')->get();
        $headers = ['ID', 'Country', 'Weather', 'Economy', 'Currency', 'News', 'Ports', 'Total', 'Risk Level'];
        $rows = [];
        foreach ($scores as $s) {
            $rows[] = [
                $s->id,
                $s->country->country_name ?? '—',
                $s->weather_score,
                $s->economic_score,
                $s->currency_score,
                $s->news_score,
                $s->port_score,
                $s->total_score,
                $s->risk_level,
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('Global Risk Index Scores', $headers, $rows);
    }

    /**
     * Import risk scores from CSV
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
                $w = $row[2] ?? 0;
                $e = $row[3] ?? 0;
                $c = $row[4] ?? 0;
                $n = $row[5] ?? 0;
                $p = $row[6] ?? 0;
                $tot = $row[7] ?? 0;
                $level = $row[8] ?? 'Low';

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $score = RiskScore::withTrashed()->updateOrCreate(
                    ['country_id' => $country->id],
                    [
                        'weather_score' => $w,
                        'economic_score' => $e,
                        'currency_score' => $c,
                        'news_score' => $n,
                        'port_score' => $p,
                        'total_score' => $tot,
                        'risk_level' => $level,
                        'calculated_at' => now(),
                    ]
                );

                if ($score->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $score->restore();
                    $updated++;
                }
            }

            fclose($handle);

            ActivityLog::log('Import', "Imported {$imported} risk scores, updated {$updated} records from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new risk scores created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }
}