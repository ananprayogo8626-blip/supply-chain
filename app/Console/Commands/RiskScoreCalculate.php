<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\RiskScoreEngine;
use Illuminate\Support\Facades\Log;

class RiskScoreCalculate extends Command
{
    protected $signature = 'sync:risk';
    protected $description = 'Calculate risk scores for all countries using Risk Score Engine';

    public function handle(RiskScoreEngine $engine)
    {
        $this->info('=============================================');
        $this->info('CALCULATING RISK SCORES');
        $this->info('=============================================');
        Log::info('[RiskScoreCalculate] Sync Started');

        try {
            $countries = Country::all();

            if ($countries->count() == 0) {
                $this->error('Tidak ada data negara.');
                Log::error('[RiskScoreCalculate] Sync Failed: No countries found in database.');
                return Command::FAILURE;
            }

            $bar = $this->output->createProgressBar($countries->count());
            $bar->start();

            foreach ($countries as $country) {
                $engine->calculate($country);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('=============================================');
            $this->info('RISK SCORES CALCULATED SUCCESSFULLY');
            $this->info('=============================================');
            Log::info('[RiskScoreCalculate] Sync Success - All risk scores recalculated.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal menghitung skor risiko: ' . $e->getMessage());
            Log::error('[RiskScoreCalculate] Sync Failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
