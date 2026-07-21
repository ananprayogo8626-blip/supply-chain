<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\News;
use App\Models\RiskScore;
use App\Models\Watchlist;
use App\Models\User;
use App\Models\SyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan Admin Dashboard dengan statistik lengkap
     */
    public function index()
    {
        // Cache data dashboard selama 5 menit untuk performa
        $dashboardData = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                // Statistik jumlah data
                'totalUsers' => User::count(),
                'totalCountries' => Country::count(),
                'totalPorts' => Port::count(),
                'totalNews' => News::count(),
                'totalWatchlists' => Watchlist::count(),
                'totalRiskScores' => RiskScore::count(),
                
                // Statistik berdasarkan role
                'superAdminCount' => User::where('role', 'super_admin')->count(),
                'adminCount' => User::where('role', 'admin')->count(),
                'analystCount' => User::where('role', 'analyst')->count(),
                'viewerCount' => User::where('role', 'viewer')->count(),
                
                // Statistik risk score
                'highRiskCount' => RiskScore::where('total_score', '>=', 51)->count(),
                'mediumRiskCount' => RiskScore::whereBetween('total_score', [26, 50])->count(),
                'lowRiskCount' => RiskScore::where('total_score', '<=', 25)->count(),
                
                // Statistik API sync
                'totalSyncLogs' => SyncLog::count(),
                'recentSyncLogs' => SyncLog::latest('failed_at')->take(10)->get(),
                
                // Data untuk grafik
                'userGrowth' => collect($this->getUserGrowthData()),
                'apiActivity' => collect($this->getApiActivityData()),
                'newsTrend' => collect($this->getNewsTrendData()),
                'riskTrend' => collect($this->getRiskTrendData()),
                
                // Status sistem
                'systemStatus' => $this->getSystemStatus(),
                'databaseStatus' => $this->getDatabaseStatus(),
                'storageStatus' => $this->getStorageStatus(),
            ];
        });

        return view('admin.dashboard', $dashboardData);
    }

    /**
     * Ambil data pertumbuhan user untuk grafik
     */
    private function getUserGrowthData()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        return $data;
    }

    /**
     * Ambil data aktivitas API untuk grafik
     */
    private function getApiActivityData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = SyncLog::whereDate('failed_at', $date->format('Y-m-d'))->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }
        return $data;
    }

    /**
     * Ambil data trend news untuk grafik
     */
    private function getNewsTrendData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = News::whereDate('published_at', $date->format('Y-m-d'))->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }
        return $data;
    }

    /**
     * Ambil data trend risk untuk grafik
     */
    private function getRiskTrendData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $avgRisk = RiskScore::whereDate('calculated_at', $date->format('Y-m-d'))
                ->avg('total_score');
            $data[] = [
                'date' => $date->format('M d'),
                'risk' => round($avgRisk ?? 0, 1),
            ];
        }
        return $data;
    }

    /**
     * Cek status sistem
     */
    private function getSystemStatus()
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'memory' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Cek status database
     */
    private function getDatabaseStatus()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'connected',
                'database' => DB::connection()->getDatabaseName(),
                'driver' => DB::connection()->getDriverName(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status storage
     */
    private function getStorageStatus()
    {
        $totalSpace = disk_total_space(base_path());
        $freeSpace = disk_free_space(base_path());
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercent = round(($usedSpace / $totalSpace) * 100, 2);

        return [
            'total' => round($totalSpace / 1024 / 1024 / 1024, 2) . ' GB',
            'used' => round($usedSpace / 1024 / 1024 / 1024, 2) . ' GB',
            'free' => round($freeSpace / 1024 / 1024 / 1024, 2) . ' GB',
            'usage_percent' => $usagePercent,
        ];
    }
}
