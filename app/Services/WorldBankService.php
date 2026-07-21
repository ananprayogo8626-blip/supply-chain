<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    protected $baseUrl = 'https://api.worldbank.org/v2/country';

    /**
     * Mengambil data indikator dari World Bank API
     */
    public function getIndicator($countryCode, $indicator)
    {
        $cacheKey = "worldbank_{$countryCode}_{$indicator}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(12), function () use ($countryCode, $indicator) {
            return $this->fetchIndicator($countryCode, $indicator);
        });
    }

    protected function fetchIndicator($countryCode, $indicator)
    {
        try {
            \Illuminate\Support\Facades\Log::debug("WorldBankService: Fetching indicator {$indicator} for {$countryCode}");

            $response = retry(2, function() use ($countryCode, $indicator) {
                return Http::timeout(20)->get(
                    "{$this->baseUrl}/{$countryCode}/indicator/{$indicator}",
                    [
                        'format'   => 'json',
                        'per_page' => 1,
                    ]
                );
            }, 1000);

            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::warning("WorldBankService: API request failed for {$countryCode} indicator {$indicator}");
                return null;
            }

            $data = $response->json();

            if (
                !isset($data[1]) ||
                !isset($data[1][0]) ||
                $data[1][0]['value'] === null
            ) {
                \Illuminate\Support\Facades\Log::info("WorldBankService: No data available for {$countryCode} indicator {$indicator}");
                return null;
            }

            $result = [
                'value' => $data[1][0]['value'],
                'year'  => $data[1][0]['date'],
            ];

            \Illuminate\Support\Facades\Log::debug("WorldBankService: Successfully fetched indicator {$indicator} for {$countryCode}: {$result['value']} ({$result['year']})");
            
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("WorldBankService: Exception for {$countryCode} indicator {$indicator}: " . $e->getMessage());
            return null;
        }
    }
}