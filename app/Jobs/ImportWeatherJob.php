<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\WeatherData;
use App\Models\ImportProgress;
use App\Services\OpenMeteoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'weather',
            'processed' => 0,
            'total' => Country::count(),
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle(OpenMeteoService $weatherService)
    {
        try {
            $this->progress->update(['status' => 'processing']);

            $successCount = 0;
            $errorCount = 0;
            $skippedCount = 0;
            $processedCount = 0;

            // Process countries in chunks of 20
            Country::chunk(20, function ($countries) use ($weatherService, &$successCount, &$errorCount, &$skippedCount, &$processedCount) {
                foreach ($countries as $country) {
                    try {
                        $processedCount++;

                        // Use existing coordinates, fallback to geocoding if missing
                        $lat = $country->latitude;
                        $lng = $country->longitude;

                        if ($lat === null || $lng === null) {
                            $geocode = app(\App\Services\GeocodingService::class)->getCoordinates($country->capital, $country->country_code);
                            if ($geocode) {
                                $lat = $geocode['latitude'];
                                $lng = $geocode['longitude'];
                            }
                        }

                        if ($lat === null || $lng === null) {
                            $skippedCount++;
                            Log::warning("ImportWeatherJob: Skipped country {$country->id} - no coordinates");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        $data = $weatherService->getWeather((float) $lat, (float) $lng);

                        if (!$data) {
                            $errorCount++;
                            Log::error("ImportWeatherJob: Failed to get weather data for country {$country->id}");
                            $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                            continue;
                        }

                        WeatherData::updateOrCreate(
                            [
                                'country_id' => $country->id,
                            ],
                            [
                                'temperature' => $data['temperature'],
                                'wind_speed' => $data['wind_speed'],
                                'rainfall' => $data['rainfall'],
                                'humidity' => $data['humidity'],
                                'cloud' => $data['cloud'] ?? null,
                                'pressure' => $data['pressure'] ?? null,
                                'weather_condition' => $data['weather_condition'],
                                'storm_risk' => $data['storm_risk'],
                            ]
                        );

                        // Calculate risk score for this country
                        app(\App\Services\RiskScoreEngine::class)->calculate($country);

                        $successCount++;
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportWeatherJob: Error processing country {$country->id}: " . $e->getMessage(), [
                            'exception' => $e
                        ]);
                        $this->updateProgress($processedCount, $successCount, $errorCount, $skippedCount);
                        continue;
                    }
                }
            });

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("ImportWeatherJob error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->progress->update([
                'status' => 'failed',
                'finished_at' => now(),
            ]);
        }
    }

    protected function updateProgress($processed, $success, $error, $skipped)
    {
        $progress = ($processed / $this->progress->total) * 100;
        $this->progress->update([
            'processed' => $processed,
            'percentage' => round($progress, 2),
        ]);
    }
}
