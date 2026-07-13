<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncWorldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worlddata:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all data sync commands in sequence';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting global supply chain data sync...');

        $commands = [
            'countries:sync' => 'Importing Countries Data...',
            'economy:sync' => 'Syncing Economic Data...',
            'weather:sync' => 'Syncing Weather Data...',
            'currency:sync' => 'Syncing Exchange Rates...',
            'ports:import' => 'Importing Ports Data...',
            'news:sync' => 'Syncing News Data...',
            'risk:calculate' => 'Calculating Risk Scores...',
        ];

        foreach ($commands as $command => $description) {
            $this->info($description);
            try {
                $exitCode = Artisan::call($command);
                if ($exitCode === 0) {
                    $this->info("Successfully completed: {$command}");
                } else {
                    $this->error("Failed to execute: {$command} with exit code {$exitCode}");
                }
            } catch (\Exception $e) {
                $this->error("Error executing {$command}: " . $e->getMessage());
            }
            $this->newLine();
        }

        $this->info('All data sync processes have been completed successfully.');
    }
}
