<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncAll extends Command
{
    protected $signature = 'sync:all';

    protected $description = 'Execute all API synchronization commands in sequence';

    public function handle()
    {
        $startTime = microtime(true);
        $this->info('========================================');
        $this->info('SUPPLYGUARD - FULL API SYNCHRONIZATION');
        $this->info('========================================');
        $this->newLine();

        $commands = [
            'countries' => 'sync:countries',
            'weather' => 'sync:weather',
            'economy' => 'sync:economy',
            'currency' => 'sync:currency',
            'ports' => 'sync:ports',
            'news' => 'sync:news',
            'risk' => 'sync:risk',
        ];

        $results = [];

        foreach ($commands as $name => $command) {
            $this->newLine();
            $this->info("----------------------------------------");
            $this->info("Sync " . ucfirst($name));
            $this->info("----------------------------------------");

            $cmdStartTime = microtime(true);

            try {
                $exitCode = Artisan::call($command);

                $cmdEndTime = microtime(true);
                $cmdDuration = round($cmdEndTime - $cmdStartTime, 2);

                if ($exitCode === 0) {
                    $this->info("✓ Completed in {$cmdDuration}s");
                    $results[$name] = ['status' => 'success', 'duration' => $cmdDuration];
                } else {
                    $this->error("✗ Failed with exit code {$exitCode}");
                    $results[$name] = ['status' => 'failed', 'duration' => $cmdDuration];
                }
            } catch (\Throwable $e) {
                $cmdEndTime = microtime(true);
                $cmdDuration = round($cmdEndTime - $cmdStartTime, 2);
                $this->error("✗ Error: " . $e->getMessage());
                Log::error("SyncAll: Error executing {$command}: " . $e->getMessage(), [
                    'exception' => $e
                ]);
                $results[$name] = ['status' => 'error', 'duration' => $cmdDuration, 'error' => $e->getMessage()];
            }
        }

        $endTime = microtime(true);
        $totalDuration = round($endTime - $startTime, 2);

        $this->newLine();
        $this->info('========================================');
        $this->info('SYNCHRONIZATION SUMMARY');
        $this->info('========================================');

        foreach ($results as $name => $result) {
            $status = $result['status'] === 'success' ? '✓' : '✗';
            $duration = $result['duration'];
            $this->info("{$status} " . ucfirst($name) . " - {$duration}s");
        }

        $this->newLine();
        $this->info('========================================');
        $this->info("Total Execution Time: {$totalDuration}s");
        $this->info('========================================');

        // Log the summary
        Log::info("SyncAll completed", [
            'total_duration' => $totalDuration,
            'results' => $results
        ]);

        return Command::SUCCESS;
    }
}
