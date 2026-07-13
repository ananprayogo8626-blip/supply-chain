<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['countries', 'weather_data', 'economic_data', 'currency_data', 'news', 'ports', 'risk_scores'];
foreach ($tables as $table) {
    echo $table . ": " . implode(", ", Illuminate\Support\Facades\Schema::getColumnListing($table)) . "\n";
}
