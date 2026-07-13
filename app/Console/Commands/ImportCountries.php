<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use Illuminate\Support\Facades\Http;
use App\Services\CountryService;
use Illuminate\Support\Facades\Log;

class ImportCountries extends Command
{
    /**
     * Nama command
     */
    protected $signature = 'countries:sync {--batch=1} {--total-batches=10}';

    /**
     * Deskripsi command
     */
    protected $description = 'Sinkronisasi semua negara dari REST Countries API secara penuh';

    /**
     * Jalankan command
     */
    public function handle(CountryService $service)
    {
        $this->info('Mengambil seluruh data negara dari REST Countries API...');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        try {
            $allCountries = $service->getAllCountries();

            if (empty($allCountries)) {
                $this->error('Failed to fetch countries from API. API tidak merespons atau mengembalikan data kosong.');
                return Command::FAILURE;
            }

            // Get countries for this batch
            $offset = ($batch - 1) * $batchSize;
            $countries = array_slice($allCountries, $offset, $batchSize);

            if (empty($countries)) {
                $this->warn('No countries in this batch.');
                return Command::SUCCESS;
            }

            $bar = $this->output->createProgressBar(count($countries));
            $bar->start();

            $imported = 0;

            foreach ($countries as $country) {
                try {
                    if (empty($country['country_code'])) {
                        $bar->advance();
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

                    $imported++;
                } catch (\Exception $e) {
                    $this->error("Failed to import country: " . $e->getMessage());
                    Log::error("ImportCountries error: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info('===========================================');
            $this->info('Import Countries Batch Completed');
            $this->info('Total Negara Diimpor : ' . $imported);
            $this->info('===========================================');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal mengambil data dari API: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}