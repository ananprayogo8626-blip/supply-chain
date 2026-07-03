<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class RiskScoreController extends Controller
{
    public function index()
    {
        $scores = RiskScore::with('country')->latest()->get();

        return view('risk-scores.index', compact('scores'));
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

        $total = $request->weather_score
            + $request->economic_score
            + $request->currency_score
            + $request->news_score
            + $request->port_score;

        if ($total <= 150) {
            $level = 'Low';
        } elseif ($total <= 300) {
            $level = 'Medium';
        } else {
            $level = 'High';
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

        $total = $request->weather_score
            + $request->economic_score
            + $request->currency_score
            + $request->news_score
            + $request->port_score;

        if ($total <= 150) {
            $level = 'Low';
        } elseif ($total <= 300) {
            $level = 'Medium';
        } else {
            $level = 'High';
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