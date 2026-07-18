<?php



namespace App\Console\Commands;



use Illuminate\Console\Command;

use App\Models\Country;

use App\Services\CountryService;

use Illuminate\Support\Facades\Log;



class CountriesSync extends Command

{

    protected $signature = 'sync:countries {--batch=1} {--total-batches=10}';



    protected $description = 'Sync countries from REST Countries API';



    public function handle(CountryService $countryService)

    {

        $this->info('=======================================');

        $this->info('SYNC COUNTRIES REST COUNTRIES API');

        $this->info('=======================================');

        Log::info('[CountriesSync] Sync Started');



        $batch = (int) $this->option('batch');

        $totalBatches = (int) $this->option('total-batches');

        $batchSize = 25;

        

        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");



        try {

            $countries = $countryService->getAllCountries();



            if (empty($countries)) {

                $this->warn('Failed to get countries from REST Countries API.');

                Log::error('[CountriesSync] Sync Failed: Empty countries list returned.');

                return Command::FAILURE;

            }



            $this->info("Total countries fetched from API: " . count($countries));



            // Process countries in batches

            $offset = ($batch - 1) * $batchSize;

            $batchCountries = array_slice($countries, $offset, $batchSize);



            if (count($batchCountries) == 0) {

                $this->warn('No countries in this batch.');

                Log::info('[CountriesSync] Sync Success: No countries to process in this batch.');

                return Command::SUCCESS;

            }



            $bar = $this->output->createProgressBar(count($batchCountries));

            $bar->start();



            $successCount = 0;

            $updateCount = 0;

            $skipCount = 0;

            $errorCount = 0;



            foreach ($batchCountries as $country) {

                try {

                    // Validate required fields

                    if (empty($country['country_code'])) {

                        $errorCount++;

                        Log::warning("CountriesSync: Skipping country with missing country_code");

                        $bar->advance();

                        continue;

                    }



                    if (empty($country['country_name'])) {

                        $errorCount++;

                        Log::warning("CountriesSync: Skipping country {$country['country_code']} with missing country_name");

                        $bar->advance();

                        continue;

                    }



                    // Validate ISO2 code (should be 2 characters)

                    if (strlen($country['country_code']) != 2) {

                        $errorCount++;

                        Log::warning("CountriesSync: Invalid ISO2 code for {$country['country_code']}");

                        $bar->advance();

                        continue;

                    }



                    // Validate ISO3 code (should be 3 characters if present)

                    if (!empty($country['iso3']) && strlen($country['iso3']) != 3) {

                        $errorCount++;

                        Log::warning("CountriesSync: Invalid ISO3 code for {$country['country_code']}");

                        $bar->advance();

                        continue;

                    }



                    // Validate coordinates if present

                    if (!empty($country['latitude']) && !is_numeric($country['latitude'])) {

                        $errorCount++;

                        Log::warning("CountriesSync: Invalid latitude for {$country['country_code']}");

                        $bar->advance();

                        continue;

                    }



                    if (!empty($country['longitude']) && !is_numeric($country['longitude'])) {

                        $errorCount++;

                        Log::warning("CountriesSync: Invalid longitude for {$country['country_code']}");

                        $bar->advance();

                        continue;

                    }



                    // Validate population if present

                    if (!empty($country['population']) && !is_numeric($country['population'])) {

                        $errorCount++;

                        Log::warning("CountriesSync: Invalid population for {$country['country_code']}");

                        $bar->advance();

                        continue;

                    }



                    $dataToUpdate = [

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

                    ];



                    if (isset($country['timezone'])) {

                        $dataToUpdate['timezone'] = $country['timezone'];

                    }



                    $existing = Country::where('country_code', $country['country_code'])->first();



                    if ($existing) {

                        $isDifferent = false;

                        foreach ($dataToUpdate as $key => $val) {

                            if ($existing->$key != $val) {

                                $isDifferent = true;

                                break;

                            }

                        }



                        if ($isDifferent) {

                            $existing->update($dataToUpdate);

                            $updateCount++;

                            Log::info("CountriesSync: [Update Success] Country {$country['country_code']} updated.");

                        } else {

                            $skipCount++;

                            Log::info("CountriesSync: [Duplicate Skipped] Country {$country['country_code']} has no changes.");

                        }

                    } else {

                        Country::create(array_merge(['country_code' => $country['country_code']], $dataToUpdate));

                        $successCount++;

                        Log::info("CountriesSync: [Sync Success] Country {$country['country_code']} created.");

                    }



                } catch (\Exception $e) {

                    $errorCount++;

                    $this->error("Failed to sync country {$country['country_code']}: " . $e->getMessage());

                    Log::error("CountriesSync error for {$country['country_code']}: " . $e->getMessage());

                }



                $bar->advance();

            }



            $bar->finish();

            $this->newLine();



            $this->info("Created: {$successCount}, Updated: {$updateCount}, Skipped: {$skipCount}, Errors: {$errorCount}");

            $this->info('=======================================');

            $this->info('SYNC COUNTRIES BATCH COMPLETED');

            $this->info('=======================================');

            Log::info("[CountriesSync] Sync Success - Batch {$batch} complete.", [

                'created' => $successCount,

                'updated' => $updateCount,

                'skipped' => $skipCount,

                'errors' => $errorCount,

            ]);



            return Command::SUCCESS;

        } catch (\Exception $e) {

            $this->error('Failed to sync countries: ' . $e->getMessage());

            Log::error("[CountriesSync] Sync Failed: " . $e->getMessage());

            return Command::FAILURE;

        }

    }

}
