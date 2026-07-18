<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Port;
use App\Models\Country;
use App\Models\ImportProgress;
use App\Services\PortService;

class ImportPortsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes for large dataset
    public $tries = 3;

    public function __construct()
    {
    }

    public function handle(PortService $portService)
    {
        try {
            Log::info("ImportPortsJob started");

            // Update progress status
            $progress = ImportProgress::where('service', 'ports')->first();
            if ($progress) {
                $progress->status = 'processing';
                $progress->stage = 'Fetching port data from multiple sources...';
                $progress->save();
            }

            // Fetch ports from PortService
            $portsData = retry(2, function() use ($portService) {
                return $portService->fetchAllPorts();
            }, 500);

            if (empty($portsData)) {
                Log::error("ImportPortsJob: Failed to fetch port data from all sources");
                if ($progress) {
                    $progress->status = 'failed';
                    $progress->error_message = 'Failed to fetch port data from all sources';
                    $progress->finished_at = now();
                    $progress->save();
                }
                return;
            }

            $totalPorts = count($portsData);
            Log::info("ImportPortsJob: Fetched {$totalPorts} ports from data sources");

            if ($progress) {
                $progress->total = $totalPorts;
                $progress->processed = 0;
                $progress->stage = 'Importing ports...';
                $progress->save();
            }

            $importedCount = 0;
            $errorCount = 0;
            $skippedCount = 0;
            $processedCount = 0;

            // Process port data in chunks to prevent memory issues and timeout
            collect($portsData)->chunk(20)->each(function ($chunk) use ($progress, $totalPorts, &$importedCount, &$errorCount, &$skippedCount, &$processedCount) {
                foreach ($chunk as $portData) {
                    try {
                        $processedCount++;

                        $countryCode = $portData['country_code'] ?? null;
                        $country = Country::where('country_code', $countryCode)->first();

                        if (!$country) {
                            $skippedCount++;
                            Log::warning("ImportPortsJob: Skipped port {$portData['port_name']} - country code {$countryCode} not found");
                            $this->updateProgress($processedCount, $importedCount, $errorCount, $skippedCount, $totalPorts, $progress);
                            continue;
                        }

                        // Use retry for individual port processing with transaction
                        retry(2, function() use ($portData, $country, &$importedCount) {
                            \Illuminate\Support\Facades\DB::transaction(function() use ($portData, $country, &$importedCount) {
                                // Generate port code if not provided
                                $portCode = $portData['unlocode'] ?? strtoupper(substr($country->country_code, 0, 2) . substr($portData['city'], 0, 3));
                                
                                // Generate UNLOCODE if not provided
                                $unlocode = $portData['unlocode'] ?? strtoupper($country->country_code . substr($portData['city'], 0, 3));

                                Port::updateOrCreate(
                                    [
                                        'unlocode' => $unlocode,
                                    ],
                                    [
                                        'country_id' => $country->id,
                                        'port_name' => $portData['port_name'],
                                        'port_code' => $portCode,
                                        'city' => $portData['city'],
                                        'latitude' => $portData['latitude'],
                                        'longitude' => $portData['longitude'],
                                        'port_type' => $portData['port_type'] ?? 'Seaport',
                                        'status' => $portData['status'] ?? 'Active',
                                        'description' => "Key maritime transport hub located in {$portData['city']}, {$country->country_name}.",
                                    ]
                                );
                            });
                        }, 500);

                        $importedCount++;
                        Log::info("ImportPortsJob: Successfully imported port {$portData['port_name']} in {$portData['city']} ({$countryCode})");

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportPortsJob: Error processing port {$portData['port_name']}: " . $e->getMessage(), [
                            'exception' => $e,
                            'port_data' => $portData
                        ]);
                        continue;
                    }

                    // Update progress every 10 ports
                    if ($processedCount % 10 === 0 && $progress) {
                        $this->updateProgress($processedCount, $importedCount, $errorCount, $skippedCount, $totalPorts, $progress);
                    }
                }
            });

            // Final progress update
            if ($progress) {
                $progress->processed = $processedCount;
                $progress->percentage = 100;
                $progress->status = 'completed';
                $progress->stage = 'Completed';
                $progress->finished_at = now();
                $progress->save();
            }

            \App\Jobs\CalculateRiskScoresJob::dispatch();

            Log::info("ImportPortsJob completed: Imported: {$importedCount}, Errors: {$errorCount}, Skipped: {$skippedCount}");
        } catch (\Exception $e) {
            Log::error("ImportPortsJob error: " . $e->getMessage());
            
            if ($progress) {
                $progress->status = 'failed';
                $progress->error_message = $e->getMessage();
                $progress->save();
            }
            
            throw $e;
        }
    }

    protected function updateProgress($processed, $imported, $error, $skipped, $total, $progress)
    {
        $percentage = round(($processed / $total) * 100);
        $progress->update([
            'processed' => $processed,
            'percentage' => $percentage,
        ]);
    }
}
