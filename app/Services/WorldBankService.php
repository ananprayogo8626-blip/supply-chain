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
        try {
            $response = retry(2, function() use ($countryCode, $indicator) {
                return Http::timeout(15)->get(
                    "{$this->baseUrl}/{$countryCode}/indicator/{$indicator}",
                    [
                        'format'   => 'json',
                        'per_page' => 1,
                    ]
                );
            }, 500);
        } catch (\Exception $e) {
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        if (
            !isset($data[1]) ||
            !isset($data[1][0]) ||
            $data[1][0]['value'] === null
        ) {
            return null;
        }

        return [
            'value' => $data[1][0]['value'],
            'year'  => $data[1][0]['date'],
        ];
    }
}