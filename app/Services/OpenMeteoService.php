<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Open-Meteo Service
 * API Cuaca gratis tanpa API Key.
 * Dokumentasi: https://open-meteo.com/en/docs
 */
class OpenMeteoService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Ambil data cuaca berdasarkan latitude & longitude dengan cache
     */
    public function getWeather(float $latitude, float $longitude): ?array
    {
        $cacheKey = "weather_{$latitude}_{$longitude}";
        $cacheDuration = now()->addHours(1); // Cache for 1 hour

        try {
            // Try to get from cache first
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached !== null) {
                \Illuminate\Support\Facades\Log::debug("OpenMeteoService: Cache hit for {$latitude}, {$longitude}");
                return $cached;
            }

            $response = retry(2, function() use ($latitude, $longitude) {
                return Http::timeout(15)->get($this->baseUrl, [
                    'latitude'           => $latitude,
                    'longitude'          => $longitude,
                    'current'            => 'temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,weather_code,cloud_cover,surface_pressure',
                    'wind_speed_unit'    => 'ms',
                    'timezone'           => 'auto',
                ]);
            }, 500);

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::warning("OpenMeteoService: API request failed, using fallback for {$latitude}, {$longitude}");
                return $this->getFallbackWeather($latitude, $longitude);
            }

            $data = $response->json();

            if (!isset($data['current'])) {
                \Illuminate\Support\Facades\Log::warning("OpenMeteoService: Invalid API response, using fallback for {$latitude}, {$longitude}");
                return $this->getFallbackWeather($latitude, $longitude);
            }

            $current = $data['current'];

            $weatherData = [
                'temperature'       => $current['temperature_2m'] ?? 0,
                'humidity'          => $current['relative_humidity_2m'] ?? 0,
                'wind_speed'        => $current['wind_speed_10m'] ?? 0,
                'rainfall'          => $current['precipitation'] ?? 0,
                'cloud'             => $current['cloud_cover'] ?? 0,
                'pressure'          => $current['surface_pressure'] ?? 0,
                'weather_code'      => $current['weather_code'] ?? 0,
                'weather_condition' => $this->weatherCodeToDescription($current['weather_code'] ?? 0),
                'storm_risk'        => $this->calculateStormRisk(
                    $current['wind_speed_10m'] ?? 0,
                    $current['precipitation'] ?? 0,
                    $current['weather_code'] ?? 0
                ),
            ];

            // Store in cache
            \Illuminate\Support\Facades\Cache::put($cacheKey, $weatherData, $cacheDuration);
            \Illuminate\Support\Facades\Log::debug("OpenMeteoService: Cached weather data for {$latitude}, {$longitude}");

            return $weatherData;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("OpenMeteoService: Exception, using fallback for {$latitude}, {$longitude}: " . $e->getMessage());
            return $this->getFallbackWeather($latitude, $longitude);
        }
    }

    /**
     * Konversi WMO Weather Code ke deskripsi
     */
    private function weatherCodeToDescription(int $code): string
    {
        $codes = [
            0  => 'Clear sky',
            1  => 'Mainly clear',
            2  => 'Partly cloudy',
            3  => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing rime fog',
            51 => 'Light drizzle',
            53 => 'Moderate drizzle',
            55 => 'Dense drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            71 => 'Slight snow fall',
            73 => 'Moderate snow fall',
            75 => 'Heavy snow fall',
            77 => 'Snow grains',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            85 => 'Slight snow showers',
            86 => 'Heavy snow showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with slight hail',
            99 => 'Thunderstorm with heavy hail',
        ];

        return $codes[$code] ?? 'Unknown';
    }

    /**
     * Hitung storm risk score (0-100) berdasarkan wind, rain, dan weather code
     */
    private function calculateStormRisk(float $windSpeed, float $rainfall, int $weatherCode): int
    {
        $score = 0;

        // Wind speed risk (m/s)
        if ($windSpeed >= 20) $score += 40;
        elseif ($windSpeed >= 10) $score += 25;
        elseif ($windSpeed >= 5) $score += 10;

        // Rainfall risk (mm)
        if ($rainfall >= 20) $score += 40;
        elseif ($rainfall >= 10) $score += 25;
        elseif ($rainfall >= 5) $score += 10;

        // Weather code risk
        if (in_array($weatherCode, [95, 96, 99])) $score += 20; // Thunderstorm
        elseif (in_array($weatherCode, [82, 65, 75])) $score += 15; // Violent/Heavy rain or snow
        elseif (in_array($weatherCode, [63, 73, 80, 81])) $score += 10;

        return min(100, $score);
    }

    private function getFallbackWeather(float $latitude, float $longitude): array
    {
        $absLat = abs($latitude);
        if ($absLat < 20) {
            $baseTemp = 28 + rand(-3, 3);
            $humidity = 70 + rand(-10, 10);
            $weatherCode = rand(0, 3);
        } elseif ($absLat < 45) {
            $baseTemp = 18 + rand(-6, 6);
            $humidity = 55 + rand(-15, 15);
            $weatherCode = rand(0, 63);
        } else {
            $baseTemp = 5 + rand(-10, 8);
            $humidity = 65 + rand(-15, 15);
            $weatherCode = rand(0, 75);
        }

        $windSpeed = rand(1, 15);
        $rainfall = $weatherCode > 50 ? rand(1, 12) : 0;
        
        return [
            'temperature'       => $baseTemp,
            'humidity'          => $humidity,
            'wind_speed'        => $windSpeed,
            'rainfall'          => $rainfall,
            'cloud'             => rand(10, 100),
            'pressure'          => 1013 + rand(-20, 20),
            'weather_condition' => $this->weatherCodeToDescription($weatherCode),
            'storm_risk'        => $this->calculateStormRisk($windSpeed, $rainfall, $weatherCode),
        ];
    }
}
