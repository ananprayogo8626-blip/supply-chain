<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\EconomicController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\RiskScoreController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\ImportProgressController;

use App\Services\WorldBankService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/import/progress/{service}', [ImportProgressController::class, 'getProgress'])
    ->name('api.import.progress');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    Route::resource('countries', CountryController::class);

    Route::get('/countries/import/api', [CountryController::class, 'import'])
        ->name('countries.import');

    /*
    |--------------------------------------------------------------------------
    | Weather
    |--------------------------------------------------------------------------
    */

    Route::resource('weather', WeatherController::class);

    Route::get('/weather/sync/{country}', [WeatherController::class, 'sync'])
        ->name('weather.sync');

    Route::get('/weather/import/api', [WeatherController::class, 'import'])
        ->name('weather.import');

    /*
    |--------------------------------------------------------------------------
    | Economy
    |--------------------------------------------------------------------------
    */

    Route::resource('economy', EconomicController::class);

    // Sync Economy dari World Bank API
    Route::get('/economy/sync/{country}', [EconomicController::class, 'sync'])
        ->name('economy.sync');

    Route::get('/economy/import/api', [EconomicController::class, 'import'])
        ->name('economy.import');

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    // Sync Currency dari Exchange Rate API
    Route::get('/currency/sync/{country}', [CurrencyController::class, 'sync'])
        ->name('currency.sync');

    Route::get('/currency/import/api', [CurrencyController::class, 'import'])
        ->name('currency.import');

    Route::resource('currency', CurrencyController::class);

    /*
    |--------------------------------------------------------------------------
    | Ports
    |--------------------------------------------------------------------------
    */

    Route::resource('ports', PortController::class);

    Route::get('/ports/import/api', [PortController::class, 'import'])
        ->name('ports.import');

    /*
    |--------------------------------------------------------------------------
    | Risk Scores
    |--------------------------------------------------------------------------
    */

    Route::get('/risk-scores/calculate/{country}', [RiskScoreController::class, 'calculate'])
        ->name('risk-scores.calculate');

    Route::get('/risk-scores/calculate-all', [RiskScoreController::class, 'calculateAll'])
        ->name('risk-scores.calculate-all');

    Route::resource('risk-scores', RiskScoreController::class);

    /*
    |--------------------------------------------------------------------------
    | News
    |--------------------------------------------------------------------------
    */

    // Sync News dari GNews API
    Route::get('/news/sync/api', [NewsController::class, 'sync'])
        ->name('news.sync');

    Route::resource('news', NewsController::class);

    /*
    |--------------------------------------------------------------------------
    | Watchlists
    |--------------------------------------------------------------------------
    */

    Route::resource('watchlists', WatchlistController::class);

    /*
    |--------------------------------------------------------------------------
    | Global Risk Map & Country Comparison
    |--------------------------------------------------------------------------
    */

    Route::get('/map', [MapController::class, 'index'])
        ->name('map');

    Route::get('/comparison', [ComparisonController::class, 'index'])
        ->name('comparison');

    Route::get('/sync-all', [SyncController::class, 'syncAll'])
        ->name('sync.all');

    Route::get('/sync/step/{step}', [SyncController::class, 'syncStep'])
        ->name('sync.step');

    Route::get('/sync/progress/{batchId}', [SyncController::class, 'getProgress'])
        ->name('sync.progress');

});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| TEST API
|--------------------------------------------------------------------------
*/

Route::get('/test-worldbank', function (WorldBankService $service) {
    return response()->json(
        $service->getIndicator(
            'IDN',
            'NY.GDP.MKTP.CD'
        )
    );
});

/*
|--------------------------------------------------------------------------
| REST API Endpoints
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/countries', [ApiController::class, 'countries']);
    Route::get('/weather', [ApiController::class, 'weather']);
    Route::get('/economy', [ApiController::class, 'economy']);
    Route::get('/currency', [ApiController::class, 'currency']);
    Route::get('/news', [ApiController::class, 'news']);
    Route::get('/ports', [ApiController::class, 'ports']);
    Route::get('/risk', [ApiController::class, 'risk']);
});

require __DIR__.'/auth.php';