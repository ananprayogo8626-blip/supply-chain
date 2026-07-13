<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Countries: " . App\Models\Country::count() . "\n";
echo "Weather: " . App\Models\WeatherData::count() . "\n";
echo "Economy: " . App\Models\EconomicData::count() . "\n";
echo "Currency: " . App\Models\CurrencyData::count() . "\n";
echo "News: " . App\Models\News::count() . "\n";
echo "Ports: " . App\Models\Port::count() . "\n";
echo "Risk: " . App\Models\RiskScore::count() . "\n";
echo "Watchlist: " . App\Models\Watchlist::count() . "\n";


