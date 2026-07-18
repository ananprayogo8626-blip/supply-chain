<?php

namespace App\Http\Controllers;

use App\Models\ImportProgress;
use App\Models\News;
use App\Models\ActivityLog;
use App\Jobs\ImportNewsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiManagementController extends Controller
{
    /**
     * Display API Management dashboard.
     */
    public function index()
    {
        // 1. GNews API Key obfuscation
        $rawKey = config('services.gnews.key');
        $maskedKey = 'Not Configured';
        if (!empty($rawKey)) {
            $maskedKey = substr($rawKey, 0, 5) . str_repeat('*', 22) . substr($rawKey, -4);
        }

        // 2. GNews API Status
        $latestImport = ImportProgress::where('service', 'news')->latest()->first();
        $status = 'ACTIVE';
        if (empty($rawKey)) {
            $status = 'INACTIVE';
        } elseif ($latestImport && $latestImport->status === 'failed') {
            $status = 'OFFLINE';
        }

        // 3. Last Sync
        $lastCompletedImport = ImportProgress::where('service', 'news')
            ->where('status', 'completed')
            ->latest('finished_at')
            ->first();
        $lastSync = $lastCompletedImport && $lastCompletedImport->finished_at
            ? $lastCompletedImport->finished_at->diffForHumans()
            : 'Never';

        // 4. Total Articles
        $totalArticles = News::count();

        // 5. Sync logs history (limit 15)
        $syncLogs = ImportProgress::where('service', 'news')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.api-management', compact(
            'maskedKey',
            'status',
            'lastSync',
            'totalArticles',
            'syncLogs'
        ));
    }

    /**
     * Trigger background synchronization job.
     */
    public function sync(Request $request)
    {
        try {
            // Check if there is an active job running
            $activeJob = ImportProgress::where('service', 'news')
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($activeJob) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Sync is already in progress.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Sync is already in progress.');
            }

            // Create progress tracker
            $progress = ImportProgress::create([
                'service'    => 'news',
                'processed'  => 0,
                'total'      => 0,
                'percentage' => 0,
                'status'     => 'pending',
                'started_at' => now(),
            ]);

            // Dispatch background job
            ImportNewsJob::dispatch($progress->id);

            // Log activity
            ActivityLog::log('Sync', 'Triggered background GNews sync from API Management.');

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sync job started in the background.',
                    'progress_id' => $progress->id,
                ]);
            }

            return redirect()->back()->with('success', 'Background synchronization started successfully.');

        } catch (\Exception $e) {
            Log::error('ApiManagementController: Sync failed: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to trigger sync: ' . $e->getMessage());
        }
    }
}
