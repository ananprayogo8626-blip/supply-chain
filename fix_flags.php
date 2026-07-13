<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update all countries: set flag to flagcdn.com URL based on country_code (ISO2)
$countries = App\Models\Country::whereNotNull('country_code')->where('country_code', '!=', '')->get();
$updated = 0;
$skipped = 0;

foreach ($countries as $country) {
    $iso2 = strtolower(trim($country->country_code));
    if (strlen($iso2) === 2) {
        $flagUrl = "https://flagcdn.com/w80/{$iso2}.png";
        $country->flag = $flagUrl;
        $country->save();
        $updated++;
    } else {
        $skipped++;
    }
}

echo "Updated: $updated countries with flagcdn.com URLs\n";
echo "Skipped: $skipped (invalid country code)\n";

// Verify
$sample = App\Models\Country::whereNotNull('flag')->take(5)->get(['country_name','country_code','flag']);
foreach ($sample as $c) {
    echo $c->country_name . ' => ' . $c->flag . "\n";
}
