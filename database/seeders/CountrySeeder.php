<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/countries.json'));

        $countries = json_decode($json, true);

        $processedCount = 0;
        $errorCount = 0;

        foreach ($countries as $country) {
            try {
                Country::updateOrCreate(
                    [
                        'country_code' => $country['country_code']
                    ],
                    [
                        'iso3'         => $country['iso3'] ?? null,
                        'country_name' => $country['country_name'],
                        'capital'      => $country['capital'],
                        'region'       => $country['region'],
                        'subregion'    => $country['subregion'] ?? null,
                        'currency'     => $country['currency'],
                        'language'     => $country['language'],
                        'population'   => $country['population'],
                        'flag'         => $country['flag'],
                        'latitude'     => $country['latitude'] ?? null,
                        'longitude'    => $country['longitude'] ?? null,
                        'timezone'     => $country['timezone'] ?? null,
                    ]
                );

                $processedCount++;

            } catch (\Throwable $e) {
                $errorCount++;
                $this->command->error("Error processing country {$country['country_code']}: " . $e->getMessage());
                continue;
            }
        }

        $this->command->info('===================================');
        $this->command->info("Countries Imported: {$processedCount}");
        $this->command->info("Errors: {$errorCount}");
        $this->command->info('===================================');
    }
}