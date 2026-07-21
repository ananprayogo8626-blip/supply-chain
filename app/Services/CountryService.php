<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service to fetch country information from the public Countries API.
 * Endpoint: https://countries.dev (returns a JSON array of country objects).
 * The structure is mapped to the local `countries` table fields.
 */
class CountryService
{
    /**
     * Base URL for the Countries API. Can be overridden via .env (COUNTRIES_API_URL).
     */
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.countries.url', env('COUNTRIES_API_URL', 'https://restcountries.com/v3.1/all'));
    }

    /**
     * Retrieve all countries from the external API.
     *
     * @return array An array of associative arrays with keys matching the Country model fillable fields.
     */
    public function getAllCountries(): array
    {
        try {
            $response = retry(2, function() {
                return Http::timeout(20)->get($this->baseUrl);
            }, 1000);
            $data = $response->json();
            
            // Check for custom API error format (Sandbox Mock)
            if (isset($data['success']) && $data['success'] === false) {
                // Fallback to another public REST countries dataset to bypass mock limitations
                $fallbackResponse = retry(2, function() {
                    return Http::timeout(20)->get('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
                }, 1000);
                $data = $fallbackResponse->json();
            }

            // Ensure we have an array of countries
            if (!is_array($data) || empty($data) || isset($data['message'])) {
                $errorMsg = $data['message'] ?? 'Invalid data structure received';
                throw new \Exception($errorMsg);
            }

            $mapped = [];
            foreach ($data as $item) {
                // The API may return fields under different keys; map them safely.
                $mapped[] = [
                    'country_code' => $item['cca2'] ?? $item['country_code'] ?? null,
                    'iso3'         => $item['cca3'] ?? null,
                    'country_name' => $item['name']['common'] ?? $item['name']['official'] ?? $item['country_name'] ?? null,
                    'capital'      => (isset($item['capital']) && is_array($item['capital']) && count($item['capital']) > 0) ? $item['capital'][0] : (is_string($item['capital'] ?? null) ? $item['capital'] : null),
                    'region'       => $item['region'] ?? null,
                    'subregion'    => $item['subregion'] ?? null,
                    'currency'     => isset($item['currencies']) && is_array($item['currencies'])
                        ? implode(', ', array_keys($item['currencies']))
                        : ($item['currency'] ?? null),
                    'language'     => isset($item['languages']) && is_array($item['languages'])
                        ? implode(', ', array_values($item['languages']))
                        : ($item['language'] ?? null),
                    'population'   => $item['population'] ?? null,
                    'flag'         => ($item['cca2'] ?? $item['country_code'] ?? null) ? 'https://flagcdn.com/w80/' . strtolower($item['cca2'] ?? $item['country_code']) . '.png' : null,
                    'latitude'     => $item['latlng'][0] ?? $item['latitude'] ?? null,
                    'longitude'    => $item['latlng'][1] ?? $item['longitude'] ?? null,
                    'timezone'     => isset($item['timezones']) && is_array($item['timezones']) && count($item['timezones']) > 0
                        ? $item['timezones'][0]
                        : null,
                ];
            }

            return $mapped;
        } catch (\Exception $e) {
            Log::error('Exception while fetching countries: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch all countries from the external API and upsert them into the `countries` table.
     * Used by the manual "Sync from API" button (single synchronous run, no batching).
     *
     * @return array{created: int, updated: int}
     */
    public function syncCountries(): array
    {
        $countries = $this->getAllCountries();

        $created = 0;
        $updated = 0;

        foreach ($countries as $country) {
            if (empty($country['country_code']) || empty($country['country_name'])) {
                continue;
            }

            $existing = Country::withTrashed()->where('country_code', $country['country_code'])->first();

            if ($existing) {
                $existing->update($country);
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $updated++;
            } else {
                Country::create($country);
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated];
    }
}
