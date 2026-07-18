<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan log sistem (activity, logins, dan api syncs)
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'activity');

        // 1. Activity Logs
        $activityQuery = ActivityLog::with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $activityQuery->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $activityLogs = $activityQuery->latest()->paginate(15, ['*'], 'activity_page')->withQueryString();

        // 2. Login Logs (Login / Logout events)
        $loginQuery = ActivityLog::with('user')->whereIn('action', ['Login', 'Logout']);
        if ($request->filled('search')) {
            $search = $request->search;
            $loginQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        $loginLogs = $loginQuery->latest()->paginate(15, ['*'], 'login_page')->withQueryString();

        // 3. API Logs (Sync Logs)
        $apiQuery = SyncLog::with('country');
        if ($request->filled('search')) {
            $search = $request->search;
            $apiQuery->where(function ($q) use ($search) {
                $q->where('stage', 'like', "%{$search}%")
                  ->orWhere('error_message', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%")
                  ->orWhereHas('country', function ($cq) use ($search) {
                      $cq->where('country_name', 'like', "%{$search}%");
                  });
            });
        }
        $apiLogs = $apiQuery->orderBy('failed_at', 'desc')->paginate(15, ['*'], 'api_page')->withQueryString();

        return view('admin.logs', compact('activityLogs', 'loginLogs', 'apiLogs', 'tab'));
    }
}
