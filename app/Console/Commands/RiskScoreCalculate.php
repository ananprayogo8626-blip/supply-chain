<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\RiskScoreEngine;

class RiskScoreCalculate extends Command
{
    protected $signature = 'risk:calculate';
    protected $description = 'Hitung otomatis skor risiko (Risk Score Engine) untuk semua negara';

    public function handle(RiskScoreEngine $engine)
    {
        $this->info('=============================================');
        $this->info('CALCULATING RISK SCORES');
        $this->info('=============================================');

        $countries = Country::all();

        if ($countries->count() == 0) {
            $this->error('Tidak ada data negara.');
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

        return Command::SUCCESS;
    }
}
