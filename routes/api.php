<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| REST API Endpoints (as specified in PROJECT FINAL (1).pdf)
|--------------------------------------------------------------------------
|
| GET /api/countries  - Filter/search countries
| GET /api/risk       - Risk scores & risk levels
| GET /api/ports      - World port dataset
| GET /api/news       - News & sentiment analysis
| GET /api/currency   - Currency & exchange rates
| GET /api/weather    - Real-time weather per country
| GET /api/economy    - Real-time World Bank economy data per country
| GET /api/dashboard  - Aggregated dashboard analytics payload
|
*/

Route::get('/countries', [ApiController::class, 'countries']);
Route::get('/risk', [ApiController::class, 'risk']);
Route::get('/ports', [ApiController::class, 'ports']);
Route::get('/news', [ApiController::class, 'news']);
Route::get('/currency', [ApiController::class, 'currency']);
Route::get('/weather', [ApiController::class, 'weather']);
Route::get('/economy', [ApiController::class, 'economy']);
Route::get('/dashboard', [ApiController::class, 'dashboard']);
