<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenWeatherService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->baseUrl = config('services.openweather.base_url');
    }

    /**
     * Mengambil data cuaca berdasarkan nama kota
     */
    public function getWeather($city)
    {
        $response = Http::get($this->baseUrl . '/weather', [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'id',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}