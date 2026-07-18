<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenWeather API
    |--------------------------------------------------------------------------
    */

    'openweather' => [
        'key' => env('OPENWEATHER_API_KEY'),
        'base_url' => env(
            'OPENWEATHER_BASE_URL',
            'https://api.openweathermap.org/data/2.5'
        ),
    ],

    'restcountries' => [
        'url' => env('REST_COUNTRIES_API_URL', 'https://restcountries.com/v3.1/all'),
    ],

    'openmeteo' => [
        'url' => env('OPEN_METEO_API_URL', 'https://api.open-meteo.com/v1/forecast'),
    ],

    'worldbank' => [
        'url' => env('WORLDBANK_API_URL', 'https://api.worldbank.org/v2/country'),
    ],

    'exchangerate' => [
        'key' => env('EXCHANGERATE_API_KEY'),
        'url' => env('EXCHANGERATE_API_URL', 'https://open.er-api.com/v6/latest'),
    ],

    'gnews' => [
        'key' => env('GNEWS_API_KEY'),
        'url' => env('GNEWS_API_URL', 'https://gnews.io/api/v4/search'),
    ],

];