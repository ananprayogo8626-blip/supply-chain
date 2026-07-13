<?php

namespace App\Http\Controllers;

use App\Models\ImportProgress;
use Illuminate\Http\Request;

class ImportProgressController extends Controller
{
    /**
     * Get import progress for a specific service
     */
    public function getProgress($service)
    {
        $progress = ImportProgress::where('service', $service)
            ->latest()
            ->first();

        if (!$progress) {
            return response()->json([
                'status' => 'not_found',
                'service' => $service,
                'percentage' => 0,
                'processed' => 0,
                'total' => 0,
            ]);
        }

        return response()->json([
            'status' => $progress->status,
            'service' => $progress->service,
            'percentage' => $progress->percentage,
            'processed' => $progress->processed,
            'total' => $progress->total,
            'started_at' => $progress->started_at,
            'finished_at' => $progress->finished_at,
        ]);
    }
}
