<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\RiskScoreController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\ImportProgressController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApiManagementController;
use App\Http\Controllers\RiskIntelligenceController;
use App\Http\Controllers\ArticleController;

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

    Route::get('/dashboard/global', [DashboardController::class, 'global'])
        ->name('dashboard.global');

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('admin.dashboard');

    Route::get('/admin/logs', [ActivityLogController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('admin.logs');

    Route::get('/admin/api-management', [ApiManagementController::class, 'index'])
        ->middleware('role:super_admin,admin')
        ->name('admin.api-management');

    Route::post('/admin/api-management/sync', [ApiManagementController::class, 'sync'])
        ->middleware('role:super_admin,admin')
        ->name('admin.api-management.sync');

    Route::get('/admin/risk-intelligence', [RiskIntelligenceController::class, 'index'])
        ->middleware('role:super_admin,admin')
        ->name('admin.risk-intelligence');

    Route::post('/admin/risk-intelligence/recalculate-all', [RiskIntelligenceController::class, 'recalculateAll'])
        ->middleware('role:super_admin,admin')
        ->name('admin.risk-intelligence.recalculate-all');

    Route::post('/admin/risk-intelligence/recalculate-country/{country}', [RiskIntelligenceController::class, 'recalculateCountry'])
        ->middleware('role:super_admin,admin')
        ->name('admin.risk-intelligence.recalculate-country');

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    Route::get('/users/export', [UserController::class, 'export'])
        ->middleware('role:super_admin,admin')
        ->name('users.export');

    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->middleware('role:super_admin,admin')
        ->name('users.reset-password');

    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->middleware('role:super_admin,admin')
        ->name('users.toggle-status');

    Route::resource('users', UserController::class)
        ->middleware('role:super_admin,admin');

    Route::resource('articles', ArticleController::class)
        ->middleware('role:super_admin,admin');

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('role:super_admin,admin')
        ->name('settings.index');

    Route::post('/settings/system', [SettingsController::class, 'updateSystem'])
        ->middleware('role:super_admin')
        ->name('settings.system');

    Route::post('/settings/api', [SettingsController::class, 'updateApi'])
        ->middleware('role:super_admin')
        ->name('settings.api');

    Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])
        ->name('settings.theme');

    Route::post('/settings/backup', [SettingsController::class, 'backup'])
        ->middleware('role:super_admin')
        ->name('settings.backup');

    Route::post('/settings/restore', [SettingsController::class, 'restore'])
        ->middleware('role:super_admin')
        ->name('settings.restore');

    Route::get('/settings/logs', [SettingsController::class, 'logs'])
        ->middleware('role:super_admin')
        ->name('settings.logs');

    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])
        ->middleware('role:super_admin')
        ->name('settings.clear-cache');

    Route::get('/settings/backups', [SettingsController::class, 'getBackups'])
        ->middleware('role:super_admin')
        ->name('settings.backups');

    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    Route::get('/countries/import/api', [CountryController::class, 'import'])
        ->name('countries.import');

    Route::get('/countries/{country}/dashboard-data', [CountryController::class, 'dashboardData'])
        ->name('countries.dashboard-data');

    Route::resource('countries', CountryController::class);

    /*
    |--------------------------------------------------------------------------
    | Ports
    |--------------------------------------------------------------------------
    */

    Route::get('/ports/import/api', [PortController::class, 'import'])
        ->name('ports.import');

    Route::resource('ports', PortController::class);

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

    // Restore routes
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore')->middleware('role:super_admin');
    Route::post('/countries/{id}/restore', [CountryController::class, 'restore'])->name('countries.restore')->middleware('role:super_admin');
    Route::post('/ports/{id}/restore', [PortController::class, 'restore'])->name('ports.restore')->middleware('role:super_admin');
    Route::post('/news/{id}/restore', [NewsController::class, 'restore'])->name('news.restore')->middleware('role:super_admin');
    Route::post('/risk-scores/{id}/restore', [RiskScoreController::class, 'restore'])->name('risk-scores.restore')->middleware('role:super_admin');
    Route::post('/watchlists/{id}/restore', [WatchlistController::class, 'restore'])->name('watchlists.restore')->middleware('role:super_admin');

    // Export PDF/CSV routes
    Route::get('/countries/export/csv', [CountryController::class, 'exportCsv'])->name('countries.export-csv')->middleware('role:super_admin,admin');
    Route::get('/countries/export/pdf', [CountryController::class, 'exportPdf'])->name('countries.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/countries/import/csv', [CountryController::class, 'importCsv'])->name('countries.import-csv')->middleware('role:super_admin');

    Route::get('/users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/users/import/csv', [UserController::class, 'importCsv'])->name('users.import-csv')->middleware('role:super_admin');

    // Ports Excel/PDF Export & Import
    Route::get('/ports/export/csv', [PortController::class, 'exportCsv'])->name('ports.export-csv')->middleware('role:super_admin,admin');
    Route::get('/ports/export/pdf', [PortController::class, 'exportPdf'])->name('ports.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/ports/import/csv', [PortController::class, 'importCsv'])->name('ports.import-csv')->middleware('role:super_admin');

    Route::get('/news/export/csv', [NewsController::class, 'exportCsv'])->name('news.export-csv')->middleware('role:super_admin,admin');
    Route::get('/news/export/pdf', [NewsController::class, 'exportPdf'])->name('news.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/news/import/csv', [NewsController::class, 'importCsv'])->name('news.import-csv')->middleware('role:super_admin');

    Route::get('/risk-scores/export/csv', [RiskScoreController::class, 'exportCsv'])->name('risk-scores.export-csv')->middleware('role:super_admin,admin');
    Route::get('/risk-scores/export/pdf', [RiskScoreController::class, 'exportPdf'])->name('risk-scores.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/risk-scores/import/csv', [RiskScoreController::class, 'importCsv'])->name('risk-scores.import-csv')->middleware('role:super_admin');

    Route::get('/watchlists/export/csv', [WatchlistController::class, 'exportCsv'])->name('watchlists.export-csv')->middleware('role:super_admin,admin');
    Route::get('/watchlists/export/pdf', [WatchlistController::class, 'exportPdf'])->name('watchlists.export-pdf')->middleware('role:super_admin,admin');
    Route::post('/watchlists/import/csv', [WatchlistController::class, 'importCsv'])->name('watchlists.import-csv')->middleware('role:super_admin');

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

require __DIR__ . '/auth.php';