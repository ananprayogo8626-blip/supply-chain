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

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Countries
    Route::resource('countries', CountryController::class);

    // Weather
    Route::resource('weather', WeatherController::class);

    // Economy
    Route::resource('economy', EconomicController::class);

    // Currency
    Route::resource('currency', CurrencyController::class);

    // Ports
    Route::resource('ports', PortController::class);

    // Risk Score
    Route::resource('risk-scores', RiskScoreController::class);

    // News
    Route::resource('news', NewsController::class);

    // Watchlist
    Route::resource('watchlists', WatchlistController::class);

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

require __DIR__.'/auth.php';