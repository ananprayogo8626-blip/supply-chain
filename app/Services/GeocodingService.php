<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Geocoding Service using Open-Meteo Geocoding API.
 * Returns latitude and longitude for a given city and country code.
 */
class GeocodingService
{
    protected $baseUrl = 'https://geocoding-api.open-meteo.com/v1/search';

    /**
     * Get coordinates (latitude, longitude) for a city.
     *
     * @param string $city        City name.
     * @param string $countryCode ISO 3166-1 alpha-2 country code.
     * @return array|null         ['latitude' => float, 'longitude' => float] or null on failure.
     */
    public function getCoordinates(string $city, string $countryCode): ?array
    {
        try {
            $response = retry(2, function() use ($city, $countryCode) {
                return Http::timeout(20)->get($this->baseUrl, [
                    'name'    => $city,
                    'country' => $countryCode,
                    'count'   => 1,
                ]);
            }, 1000);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            if (empty($data['results'][0])) {
                return null;
            }

            $result = $data['results'][0];
            return [
                'latitude'  => $result['latitude'] ?? null,
                'longitude' => $result['longitude'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('GeocodingService error: ' . $e->getMessage());
            return null;
        }
    }
}
?>
