<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Support\Facades\Log;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds strictly via REST Countries API.
     */
    public function run(): void
    {
        $this->command->info('Fetching live country data from REST Countries API...');

        try {
            $countryService = app(CountryService::class);
            $result = $countryService->syncCountries();

            $this->command->info('===================================');
            $this->command->info("Source: REST Countries API");
            $this->command->info("Countries Created: {$result['created']}");
            $this->command->info("Countries Updated: {$result['updated']}");
            $this->command->info('===================================');
        } catch (\Throwable $e) {
            $this->command->error("Error fetching data from REST Countries API: " . $e->getMessage());
            Log::error("CountrySeeder failed: " . $e->getMessage());
        }
    }
}