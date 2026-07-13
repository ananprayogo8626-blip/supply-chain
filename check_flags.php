<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check flag status
$noFlag = App\Models\Country::where(function($q){ $q->whereNull('flag')->orWhere('flag',''); })->count();
echo "Countries with no flag: $noFlag\n";

$withFlag = App\Models\Country::whereNotNull('flag')->where('flag','!=','')->count();
echo "Countries with flag: $withFlag\n";

$sample = App\Models\Country::take(3)->get(['country_name','country_code','flag']);
foreach($sample as $c) {
    echo $c->country_name . ' | ' . $c->country_code . ' | ' . ($c->flag ?: 'NO FLAG') . "\n";
}
