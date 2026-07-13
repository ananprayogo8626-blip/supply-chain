<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use Illuminate\Http\Request;

use App\Services\RiskScoreEngine;

class RiskScoreController extends Controller
{
    public function index()
    {
        $scores = RiskScore::with('country')->latest()->get();

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

            return redirect()->route('risk-scores.index')
                ->with('success', "Skor risiko untuk {$country->country_name} berhasil diperbarui.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("RiskScoreController@calculate error for country {$country->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('risk-scores.index')
                ->with('error', 'Terjadi kesalahan saat menghitung skor risiko: ' . $e->getMessage());
        }
    }

    /**
     * Bulk calculate risk scores for all countries
     */
    public function calculateAll(RiskScoreEngine $engine)
    {
        try {
            $countries = Country::all();
            $successCount = 0;
            $errorCount = 0;

            foreach ($countries as $country) {
                try {
                    $score = $engine->calculate($country);
                    if ($score) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Throwable $e) {
                    $errorCount++;
                    \Illuminate\Support\Facades\Log::error("RiskScoreController@calculateAll error for country {$country->id}: " . $e->getMessage());
                }
            }

            return redirect()->route('risk-scores.index')
                ->with('success', "Risk scores recalculated for {$successCount} countries. Failed: {$errorCount}.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("RiskScoreController@calculateAll error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('risk-scores.index')
                ->with('error', 'Terjadi kesalahan saat menghitung skor risiko: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $countries = Country::orderBy('country_name')->get();

        return view('risk-scores.create', compact('countries'));
    }

    public function store(Request $request)
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
            ($request->weather_score * 0.25) +
            ($request->economic_score * 0.25) +
            ($request->news_score * 0.25) +
            ($request->currency_score * 0.15) +
            ($request->port_score * 0.10)
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

        RiskScore::create([
            'country_id' => $request->country_id,
            'weather_score' => $request->weather_score,
            'economic_score' => $request->economic_score,
            'currency_score' => $request->currency_score,
            'news_score' => $request->news_score,
            'port_score' => $request->port_score,
            'total_score' => $total,
            'risk_level' => $level,
            'recommendation' => $request->recommendation,
            'calculated_at' => $request->calculated_at,
        ]);

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
            ($request->weather_score * 0.25) +
            ($request->economic_score * 0.25) +
            ($request->news_score * 0.25) +
            ($request->currency_score * 0.15) +
            ($request->port_score * 0.10)
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
            'calculated_at' => $request->calculated_at,
        ]);

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil diperbarui.');
    }

    public function destroy(RiskScore $risk_score)
    {
        $risk_score->delete();

        return redirect()->route('risk-scores.index')
            ->with('success', 'Risk Score berhasil dihapus.');
    }
}