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

        foreach ($countries as $country) {

            Country::updateOrCreate(

                [
                    'country_code' => $country['country_code']
                ],

                [
                    'country_name' => $country['country_name'],
                    'capital'      => $country['capital'],
                    'region'       => $country['region'],
                    'currency'     => $country['currency'],
                    'language'     => $country['language'],
                    'population'   => $country['population'],
                    'flag'         => $country['flag']
                ]

            );

        }

        $this->command->info('===================================');
        $this->command->info('250 Countries Imported Successfully');
        $this->command->info('===================================');
    }
}