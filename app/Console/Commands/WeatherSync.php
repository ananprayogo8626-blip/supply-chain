<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\WeatherData;
use App\Services\OpenMeteoService;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Log;

class WeatherSync extends Command
{
    /**
     * Nama command
     */
    protected $signature = 'weather:sync {--batch=1} {--total-batches=10}';

    /**
     * Deskripsi command
     */
    protected $description = 'Sinkronisasi data cuaca dari Open-Meteo API';

    /**
     * Execute the console command.
     */
    public function handle(OpenMeteoService $weatherService)
    {
        $this->info('=======================================');
        $this->info('SYNC WEATHER OPEN-METEO API');
        $this->info('=======================================');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        // Get countries for this batch
        $offset = ($batch - 1) * $batchSize;
        $countries = Country::offset($offset)->limit($batchSize)->get();

        if ($countries->count() == 0) {
            $this->warn('No countries in this batch.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            try {
                $lat = $country->latitude;
                $lng = $country->longitude;

                // If coordinates are missing, attempt geocoding based on capital and country code
                if ($lat === null || $lng === null) {
                    $geocode = app(GeocodingService::class)->getCoordinates($country->capital, $country->country_code);
                    if ($geocode) {
                        $lat = $geocode['latitude'];
                        $lng = $geocode['longitude'];
                    }
                }

                // Proceed only if we have valid coordinates
                if ($lat === null || $lng === null) {
                    $bar->advance();
                    continue;
                }

                $weather = $weatherService->getWeather((float) $lat, (float) $lng);

                if ($weather) {
                    WeatherData::updateOrCreate(
                        [
                            'country_id' => $country->id
                        ],
                        [
                            'temperature' => $weather['temperature'],
                            'wind_speed' => $weather['wind_speed'],
                            'rainfall' => $weather['rainfall'],
                            'humidity' => $weather['humidity'],
                            'cloud' => $weather['cloud'] ?? null,
                            'pressure' => $weather['pressure'] ?? null,
                            'weather_condition' => $weather['weather_condition'],
                            'storm_risk' => $weather['storm_risk'],
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync weather for {$country->country_name}: " . $e->getMessage());
                Log::error("WeatherSync error for {$country->country_code}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('=======================================');
        $this->info('SYNC WEATHER BATCH COMPLETED');
        $this->info('=======================================');

        return Command::SUCCESS;
    }
}