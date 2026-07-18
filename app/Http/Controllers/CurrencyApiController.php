<?php

namespace App\Http\Controllers;

use App\Models\ImportProgress;
use App\Repositories\CurrencyRepository;
use App\Jobs\ImportCurrencyJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyApiController extends Controller
{
    protected $currencyRepo;

    public function __construct(CurrencyRepository $currencyRepo)
    {
        $this->currencyRepo = $currencyRepo;
    }

    /**
     * Display Currency API Management page
     */
    public function index()
    {
        $key = config('services.exchangerate.key') ?? '';
        
        // Mask the key
        if (!empty($key)) {
            $maskedKey = strlen($key) > 8 
                ? substr($key, 0, 5) . str_repeat('*', 15) . substr($key, -4) 
                : str_repeat('*', 10);
        } else {
            $maskedKey = 'NOT CONFIGURATED';
        }

        // Get latest import progress
        $latestProgress = ImportProgress::where('service', 'currency')->latest()->first();

        // Calculate API Status
        if (empty($key)) {
            $status = 'INACTIVE';
        } elseif ($latestProgress && $latestProgress->status === 'failed') {
            $status = 'OFFLINE';
        } else {
            $status = 'ACTIVE';
        }

        // Get last successful sync timestamp
        $lastCompleted = ImportProgress::where('service', 'currency')
            ->where('status', 'completed')
            ->latest('finished_at')
            ->first();

        $lastSync = $lastCompleted && $lastCompleted->finished_at 
            ? $lastCompleted->finished_at->diffForHumans() 
            : 'Never';

        // Calculate Sync Duration (Response Time)
        $responseTime = 'N/A';
        if ($lastCompleted && $lastCompleted->started_at && $lastCompleted->finished_at) {
            $duration = abs($lastCompleted->finished_at->diffInSeconds($lastCompleted->started_at));
            $responseTime = $duration . ' seconds';
        }

        $totalCurrencies = $this->currencyRepo->count();
        $syncLogs = ImportProgress::where('service', 'currency')
            ->orderByDesc('started_at')
            ->take(15)
            ->get();

        return view('admin.currency-api', compact(
            'status',
            'lastSync',
            'maskedKey',
            'responseTime',
            'totalCurrencies',
            'syncLogs'
        ));
    }

    /**
     * Trigger API sync in the background
     */
    public function sync()
    {
        try {
            // Check if there is an active running sync to avoid parallel execution conflicts
            $activeSync = ImportProgress::where('service', 'currency')
                ->where('status', 'processing')
                ->first();

            if ($activeSync) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A synchronization job is already running in the background.'
                ], 422);
            }

            ImportCurrencyJob::dispatch();
            
            \App\Models\ActivityLog::log('Sync', 'Triggered background Currency API sync from admin panel.');

            return response()->json([
                'status' => 'success',
                'message' => 'Sync job successfully dispatched to background queue.'
            ]);
        } catch (\Throwable $e) {
            Log::error("CurrencyApiController@sync error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to queue sync job: ' . $e->getMessage()
            ], 500);
        }
    }
}
