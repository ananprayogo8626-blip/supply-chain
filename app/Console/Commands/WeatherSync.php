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
    protected $signature = 'sync:weather {--batch=1} {--total-batches=10}';

    /**
     * Deskripsi command
     */
    protected $description = 'Sync weather data from Open-Meteo API';

    /**
     * Execute the console command.
     */
    public function handle(OpenMeteoService $weatherService)
    {
        $this->info('=======================================');
        $this->info('SYNC WEATHER OPEN-METEO API');
        $this->info('=======================================');
        Log::info('[WeatherSync] Sync Started');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        // Get countries for this batch
        $offset = ($batch - 1) * $batchSize;
        $countries = Country::offset($offset)->limit($batchSize)->get();

        if ($countries->count() == 0) {
            $this->warn('No countries in this batch.');
            Log::info('[WeatherSync] Sync Success: No countries to process in this batch.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        $successCount = 0;
        $updateCount = 0;
        $skipCount = 0;
        $errorCount = 0;
        $skipCoordsCount = 0;

        foreach ($countries as $country) {
            try {
                $lat = $country->latitude;
                $lng = $country->longitude;

                // If coordinates are missing, attempt geocoding based on capital and country code
                if ($lat === null || $lng === null) {
                    Log::info("WeatherSync: Missing coordinates for {$country->country_name}, attempting geocoding");
                    $geocode = app(GeocodingService::class)->getCoordinates($country->capital, $country->country_code);
                    if ($geocode) {
                        $lat = $geocode['latitude'];
                        $lng = $geocode['longitude'];
                        Log::info("WeatherSync: Geocoding successful for {$country->country_name}: {$lat}, {$lng}");
                    } else {
                        Log::warning("WeatherSync: Geocoding failed for {$country->country_name}");
                    }
                }

                // Proceed only if we have valid coordinates
                if ($lat === null || $lng === null) {
                    $skipCoordsCount++;
                    Log::warning("WeatherSync: Skipping {$country->country_name} - no valid coordinates");
                    $bar->advance();
                    continue;
                }

                $weather = $weatherService->getWeather((float) $lat, (float) $lng);

                if ($weather) {
                    $dataToUpdate = [
                        'temperature' => $weather['temperature'],
                        'wind_speed' => $weather['wind_speed'],
                        'rainfall' => $weather['rainfall'],
                        'humidity' => $weather['humidity'],
                        'cloud' => $weather['cloud'] ?? null,
                        'pressure' => $weather['pressure'] ?? null,
                        'weather_condition' => $weather['weather_condition'],
                        'storm_risk' => $weather['storm_risk'],
                    ];

                    $existing = WeatherData::where('country_id', $country->id)->first();

                    if ($existing) {
                        $isDifferent = false;
                        foreach ($dataToUpdate as $key => $val) {
                            if ($existing->$key != $val) {
                                $isDifferent = true;
                                break;
                            }
                        }

                        if ($isDifferent) {
                            // Save current values to history
                            \App\Models\WeatherHistory::create([
                                'country_id' => $country->id,
                                'temperature' => $existing->temperature,
                                'wind_speed' => $existing->wind_speed,
                                'rainfall' => $existing->rainfall,
                                'humidity' => $existing->humidity,
                                'cloud' => $existing->cloud,
                                'pressure' => $existing->pressure,
                                'weather_condition' => $existing->weather_condition,
                                'storm_risk' => $existing->storm_risk,
                                'recorded_at' => $existing->updated_at ?? now(),
                            ]);

                            $existing->update($dataToUpdate);
                            $updateCount++;
                            Log::info("WeatherSync: [Update Success] Weather for {$country->country_name} updated.");
                        } else {
                            $skipCount++;
                            Log::info("WeatherSync: [Duplicate Skipped] Weather for {$country->country_name} has no changes.");
                        }
                    } else {
                        WeatherData::create(array_merge(['country_id' => $country->id], $dataToUpdate));
                        $successCount++;
                        Log::info("WeatherSync: [Sync Success] Weather for {$country->country_name} created.");
                    }
                } else {
                    $errorCount++;
                    Log::error("WeatherSync: Failed to get weather data for {$country->country_name}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to sync weather for {$country->country_name}: " . $e->getMessage());
                Log::error("WeatherSync error for {$country->country_code}: " . $e->getMessage(), [
                    'exception' => $e,
                    'country_id' => $country->id,
                    'latitude' => $country->latitude,
                    'longitude' => $country->longitude,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Created: {$successCount}, Updated: {$updateCount}, Skipped: {$skipCount}, Errors: {$errorCount}, Missing Coords: {$skipCoordsCount}");
        $this->info('=======================================');
        $this->info('SYNC WEATHER BATCH COMPLETED');
        $this->info('=======================================');

        Log::info("[WeatherSync] Sync Success - Batch {$batch} complete.", [
            'batch' => $batch,
            'total_batches' => $totalBatches,
            'created' => $successCount,
            'updated' => $updateCount,
            'skipped' => $skipCount,
            'errors' => $errorCount,
        ]);

        return Command::SUCCESS;
    }
}