<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\RiskHistory;
use App\Models\ImportProgress;
use App\Models\ActivityLog;
use App\Repositories\RiskScoreRepository;
use App\Services\RiskScoreEngine;
use App\Jobs\CalculateRiskScoresJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiskIntelligenceController extends Controller
{
    protected $riskRepo;

    public function __construct(RiskScoreRepository $riskRepo)
    {
        $this->riskRepo = $riskRepo;
    }

    /**
     * Display Risk Intelligence admin dashboard.
     */
    public function index()
    {
        // 1. Get statistics from repository
        $stats = $this->riskRepo->getRiskStats();

        // 2. Get top tables from repository
        $topHighRisk = $this->riskRepo->getTopHighRiskCountries(10);
        $topSafest = $this->riskRepo->getTopSafestCountries(10);

        // 3. Get historical trend from repository
        $riskTrend = $this->riskRepo->getRiskTrend(30);

        // 4. Job Logs (ImportProgress for risk_scores)
        $progressLogs = ImportProgress::where('service', 'risk_scores')
            ->orderByDesc('started_at')
            ->take(10)
            ->get();

        // 5. Individual risk history updates
        $riskHistoryLogs = RiskHistory::with('country')
            ->orderByDesc('calculated_at')
            ->take(15)
            ->get();

        // 6. Dropdown country selection list
        $countries = Country::orderBy('country_name')->get();

        return view('admin.risk-intelligence', compact(
            'stats',
            'topHighRisk',
            'topSafest',
            'riskTrend',
            'progressLogs',
            'riskHistoryLogs',
            'countries'
        ));
    }

    /**
     * Trigger recalculation for all countries in background
     */
    public function recalculateAll()
    {
        try {
            $activeJob = ImportProgress::where('service', 'risk_scores')
                ->where('status', 'processing')
                ->first();

            if ($activeJob) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Risk scores recalculation is already in progress.'
                ], 422);
            }

            CalculateRiskScoresJob::dispatch();
            ActivityLog::log('Risk', 'Triggered background recalculation of all country risk scores.');

            return response()->json([
                'status' => 'success',
                'message' => 'Batch recalculation job queued successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error("RiskIntelligenceController@recalculateAll error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to dispatch recalculation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate a single country synchronously
     */
    public function recalculateCountry(Country $country, RiskScoreEngine $engine)
    {
        try {
            Log::info("RiskIntelligenceController: Manual recalc triggered for {$country->country_name}");
            
            $score = $engine->calculate($country);

            if ($score) {
                ActivityLog::log('Risk', "Manually recalculated risk score for country {$country->country_name}.", $score);
                return redirect()->route('admin.risk-intelligence')
                    ->with('success', "Risk score for {$country->country_name} recalculated successfully: {$score->total_score} ({$score->risk_level}).");
            } else {
                return redirect()->route('admin.risk-intelligence')
                    ->with('error', "Failed to calculate risk score for {$country->country_name}. See logs for details.");
            }
        } catch (\Throwable $e) {
            Log::error("RiskIntelligenceController@recalculateCountry error: " . $e->getMessage());
            return redirect()->route('admin.risk-intelligence')
                ->with('error', "Recalculation error: " . $e->getMessage());
        }
    }
}
