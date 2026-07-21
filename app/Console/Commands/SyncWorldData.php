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
            'sync:countries' => 'Importing Countries Data...',
            'sync:ports' => 'Importing Ports Data...',
            'sync:news' => 'Syncing News Data...',
            'sync:risk' => 'Calculating Risk Scores...',
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
