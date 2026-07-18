<?php

namespace App\Repositories;

use App\Models\RiskScore;
use App\Models\RiskHistory;
use Illuminate\Support\Facades\DB;

class RiskScoreRepository
{
    /**
     * Get aggregate statistics for risk scoring.
     */
    public function getRiskStats(): array
    {
        $lowCount = RiskScore::where('risk_level', 'Low')->count();
        $mediumCount = RiskScore::where('risk_level', 'Medium')->count();
        $highCount = RiskScore::where('risk_level', 'High')->count();
        $criticalCount = RiskScore::where('risk_level', 'Critical')->count();
        $averageScore = RiskScore::avg('total_score') ?? 0;

        return [
            'low' => $lowCount,
            'medium' => $mediumCount,
            'high' => $highCount,
            'critical' => $criticalCount,
            'average' => round($averageScore, 1)
        ];
    }

    /**
     * Get top high risk countries.
     */
    public function getTopHighRiskCountries(int $limit = 10)
    {
        return RiskScore::with('country')
            ->orderByDesc('total_score')
            ->take($limit)
            ->get();
    }

    /**
     * Get top safest countries.
     */
    public function getTopSafestCountries(int $limit = 10)
    {
        return RiskScore::with('country')
            ->orderBy('total_score')
            ->take($limit)
            ->get();
    }

    /**
     * Get historical risk score trend.
     */
    public function getRiskTrend(int $days = 30): array
    {
        $trend = RiskHistory::selectRaw('DATE(calculated_at) as date, avg(total_score) as avg_score')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take($days)
            ->get()
            ->map(fn($row) => [
                'label' => \Carbon\Carbon::parse($row->date)->format('M d'),
                'value' => round($row->avg_score, 1)
            ])
            ->values()
            ->toArray();

        // Fallback mock trend if history is empty
        if (empty($trend)) {
            $avgCurrent = RiskScore::avg('total_score') ?? 50;
            for ($i = $days - 1; $i >= 0; $i--) {
                $trend[] = [
                    'label' => now()->subDays($i)->format('M d'),
                    'value' => round($avgCurrent + rand(-5, 5), 1)
                ];
            }
        }

        return $trend;
    }
}
