<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RestCountriesService
{
    protected $baseUrl = 'https://restcountries.com/v3.1';

    public function getAllCountries()
    {
        $response = Http::get($this->baseUrl . '/all');

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }
}