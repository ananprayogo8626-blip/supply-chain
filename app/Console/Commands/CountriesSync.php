<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Support\Facades\Log;

class CountriesSync extends Command
{
    protected $signature = 'countries:sync {--batch=1} {--total-batches=10}';

    protected $description = 'Sinkronisasi data negara dari REST Countries API';

    public function handle(CountryService $countryService)
    {
        $this->info('=======================================');
        $this->info('SYNC COUNTRIES REST COUNTRIES API');
        $this->info('=======================================');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        try {
            $countries = $countryService->getAllCountries();

            if (empty($countries)) {
                $this->warn('Failed to get countries from REST Countries API.');
                return Command::FAILURE;
            }

            // Process countries in batches
            $offset = ($batch - 1) * $batchSize;
            $batchCountries = array_slice($countries, $offset, $batchSize);

            if (count($batchCountries) == 0) {
                $this->warn('No countries in this batch.');
                return Command::SUCCESS;
            }

            $bar = $this->output->createProgressBar(count($batchCountries));
            $bar->start();

            foreach ($batchCountries as $country) {
                try {
                    if (empty($country['country_code'])) {
                        continue;
                    }

                    Country::updateOrCreate(
                        [
                            'country_code' => $country['country_code'],
                        ],
                        [
                            'iso3'         => $country['iso3'],
                            'country_name' => $country['country_name'] ?? 'Unknown',
                            'capital'      => $country['capital'],
                            'region'       => $country['region'],
                            'subregion'    => $country['subregion'],
                            'currency'     => $country['currency'],
                            'language'     => $country['language'],
                            'population'   => $country['population'] ?? 0,
                            'flag'         => $country['flag'],
                            'latitude'     => $country['latitude'],
                            'longitude'    => $country['longitude'],
                        ]
                    );
                } catch (\Exception $e) {
                    $this->error("Failed to sync country {$country['country_code']}: " . $e->getMessage());
                    Log::error("CountriesSync error for {$country['country_code']}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            $this->info('=======================================');
            $this->info('SYNC COUNTRIES BATCH COMPLETED');
            $this->info('=======================================');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync countries: ' . $e->getMessage());
            Log::error("CountriesSync error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
