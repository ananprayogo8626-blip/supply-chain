<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\EconomicData;

class EconomySeed extends Command
{
    protected $signature = 'economy:seed';

    protected $description = 'Seed data ekonomi realistis untuk semua negara (tanpa API, offline mode)';

    /**
     * Realistic economic data for major trading countries
     */
    private array $knownEconomics = [
        'US' => ['gdp' => 27360000000000, 'inflation' => 3.4, 'exports' => 3052000000000, 'imports' => 3173000000000, 'population' => 335000000],
        'CN' => ['gdp' => 17794000000000, 'inflation' => 0.7, 'exports' => 3380000000000, 'imports' => 2716000000000, 'population' => 1412000000],
        'JP' => ['gdp' => 4213000000000, 'inflation' => 2.8, 'exports' => 705000000000, 'imports' => 786000000000, 'population' => 125000000],
        'DE' => ['gdp' => 4082000000000, 'inflation' => 5.9, 'exports' => 1693000000000, 'imports' => 1569000000000, 'population' => 84000000],
        'GB' => ['gdp' => 3088000000000, 'inflation' => 7.3, 'exports' => 470000000000, 'imports' => 694000000000, 'population' => 67000000],
        'IN' => ['gdp' => 3730000000000, 'inflation' => 5.7, 'exports' => 770000000000, 'imports' => 898000000000, 'population' => 1400000000],
        'FR' => ['gdp' => 2923000000000, 'inflation' => 5.7, 'exports' => 644000000000, 'imports' => 773000000000, 'population' => 68000000],
        'BR' => ['gdp' => 2174000000000, 'inflation' => 4.8, 'exports' => 339000000000, 'imports' => 235000000000, 'population' => 215000000],
        'IT' => ['gdp' => 2169000000000, 'inflation' => 5.9, 'exports' => 641000000000, 'imports' => 582000000000, 'population' => 60000000],
        'CA' => ['gdp' => 2139000000000, 'inflation' => 3.9, 'exports' => 606000000000, 'imports' => 558000000000, 'population' => 38000000],
        'KR' => ['gdp' => 1709000000000, 'inflation' => 3.6, 'exports' => 632000000000, 'imports' => 643000000000, 'population' => 52000000],
        'AU' => ['gdp' => 1688000000000, 'inflation' => 6.0, 'exports' => 431000000000, 'imports' => 332000000000, 'population' => 26000000],
        'RU' => ['gdp' => 1862000000000, 'inflation' => 8.4, 'exports' => 588000000000, 'imports' => 304000000000, 'population' => 144000000],
        'MX' => ['gdp' => 1322000000000, 'inflation' => 6.2, 'exports' => 580000000000, 'imports' => 559000000000, 'population' => 130000000],
        'ES' => ['gdp' => 1584000000000, 'inflation' => 8.4, 'exports' => 410000000000, 'imports' => 450000000000, 'population' => 47000000],
        'ID' => ['gdp' => 1319000000000, 'inflation' => 3.7, 'exports' => 292000000000, 'imports' => 237000000000, 'population' => 281000000],
        'NL' => ['gdp' => 1118000000000, 'inflation' => 10.0, 'exports' => 813000000000, 'imports' => 756000000000, 'population' => 17800000],
        'SA' => ['gdp' => 1068000000000, 'inflation' => 2.9, 'exports' => 410000000000, 'imports' => 205000000000, 'population' => 36000000],
        'TR' => ['gdp' => 906000000000, 'inflation' => 72.3, 'exports' => 254000000000, 'imports' => 361000000000, 'population' => 85000000],
        'SG' => ['gdp' => 497000000000, 'inflation' => 4.8, 'exports' => 457000000000, 'imports' => 471000000000, 'population' => 6000000],
        'MY' => ['gdp' => 408000000000, 'inflation' => 3.5, 'exports' => 298000000000, 'imports' => 264000000000, 'population' => 34000000],
        'AE' => ['gdp' => 504000000000, 'inflation' => 4.8, 'exports' => 361000000000, 'imports' => 299000000000, 'population' => 9400000],
        'ZA' => ['gdp' => 378000000000, 'inflation' => 5.9, 'exports' => 124000000000, 'imports' => 106000000000, 'population' => 60000000],
        'PH' => ['gdp' => 402000000000, 'inflation' => 6.0, 'exports' => 68000000000, 'imports' => 131000000000, 'population' => 115000000],
        'TH' => ['gdp' => 513000000000, 'inflation' => 1.4, 'exports' => 291000000000, 'imports' => 289000000000, 'population' => 70000000],
        'VN' => ['gdp' => 430000000000, 'inflation' => 3.3, 'exports' => 355000000000, 'imports' => 328000000000, 'population' => 98000000],
        'PK' => ['gdp' => 338000000000, 'inflation' => 29.2, 'exports' => 31000000000, 'imports' => 56000000000, 'population' => 230000000],
        'BD' => ['gdp' => 460000000000, 'inflation' => 9.9, 'exports' => 55000000000, 'imports' => 88000000000, 'population' => 170000000],
        'NG' => ['gdp' => 362000000000, 'inflation' => 18.0, 'exports' => 46000000000, 'imports' => 27000000000, 'population' => 220000000],
        'EG' => ['gdp' => 476000000000, 'inflation' => 15.3, 'exports' => 48000000000, 'imports' => 85000000000, 'population' => 107000000],
    ];

    public function handle()
    {
        $this->info('====================================');
        $this->info('SEED ECONOMY DATA (OFFLINE MODE)');
        $this->info('====================================');

        $countries = Country::all();

        if ($countries->count() == 0) {
            $this->error('Data negara kosong. Jalankan php artisan countries:import terlebih dahulu.');
            return Command::FAILURE;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            $code = strtoupper($country->country_code);

            if (isset($this->knownEconomics[$code])) {
                $data = $this->knownEconomics[$code];
            } else {
                // Generate realistic data based on population
                $pop = $country->population ?: 10000000;
                $gdpPerCapita = rand(2000, 45000);
                $gdp = $pop * $gdpPerCapita;
                $tradeRatio = rand(20, 60) / 100;
                $data = [
                    'gdp'        => $gdp,
                    'inflation'  => rand(10, 150) / 10,
                    'exports'    => $gdp * $tradeRatio * 0.9,
                    'imports'    => $gdp * $tradeRatio * 1.0,
                    'population' => $pop,
                ];
            }

            $gdpGrowth = $data['gdp_growth'] ?? (rand(-15, 65) / 10);

            EconomicData::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'gdp'        => $data['gdp'],
                    'gdp_growth' => $gdpGrowth,
                    'inflation'  => $data['inflation'],
                    'exports'    => $data['exports'],
                    'imports'    => $data['imports'],
                    'population' => $data['population'],
                    'data_year'  => 2023,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('====================================');
        $this->info('SEED EKONOMI BERHASIL: ' . $countries->count() . ' negara');
        $this->info('====================================');

        return Command::SUCCESS;
    }
}
