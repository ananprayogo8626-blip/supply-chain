# Implementation Plan: API-Direct Real-Time Refactoring (No Database for Weather, Economics, & Currency)

This plan details the architectural changes required to transition Weather, Economic Indicators, and Currency Exchange Rates from database storage (which are not specified in the `PROJECT FINAL.pdf` database outline) to direct, real-time API integrations.

---

## User Review Required

> [!IMPORTANT]
> The database schema outlined in the project specification `PROJECT FINAL.pdf` (pages 8-9) **does not** include tables for weather, economics, or currency histories. Storing these persistent records is a deviation from the spec. 
> 
> By moving to a real-time API-driven model, we will:
> * Remove `weather_data`, `weather_histories`, `economic_data`, `economic_histories`, `currency_data`, and `currency_histories` tables.
> * Fetch weather (Open-Meteo), economics (World Bank), and exchange rates (ExchangeRate API) directly in controllers/services and cache them in-memory/via Laravel Cache.
> * Maintain compatibility with all Blade views by dynamically assigning response objects to `$country->weatherData`, `$country->economicData`, and `$country->currencyData`.

---

## Proposed Changes

### Database Layer
* **Remove / Disable Migrations**:
  * Drop the database tables that persist this real-time data:
    * `weather_data` / `weather_histories`
    * `economic_data` / `economic_histories`
    * `currency_data` / `currency_histories`
  * Add a migration to drop these tables or run fresh migrations without them.

* **Delete Models**:
  * `WeatherData.php`
  * `WeatherHistory.php`
  * `EconomicData.php`
  * `EconomicHistory.php`
  * `CurrencyData.php`
  * `CurrencyHistory.php`

---

### Service Layer

#### [MODIFY] [RiskScoreEngine.php](file:///d:/ProjekFullStack/supply-chain/app/Services/RiskScoreEngine.php)
* Refactor to obtain weather, economic, and currency scores by calling the service APIs directly (leveraging cache) instead of querying database records.

---

### Controller Layer

#### [MODIFY] [CountryController.php](file:///d:/ProjekFullStack/supply-chain/app/Http/Controllers/CountryController.php)
* Retrieve weather, economics, and currency information in real-time using service classes in `show()` and `dashboardData()` methods, binding them directly to the `$country` object properties before passing them to the view.

#### [MODIFY] [DashboardController.php](file:///d:/ProjekFullStack/supply-chain/app/Http/Controllers/DashboardController.php)
* Adjust dashboard widgets and KPI calculations to fetch exchange rates and weather extremes via cached API records instead of database aggregates.

---

### Console & Job Queue Layer

#### [DELETE] Cron Commands & Jobs
* Remove sync/import commands that are no longer needed for these three categories:
  * `WeatherSync.php` / `ImportWeatherJob.php`
  * `EconomySync.php` / `ImportEconomyJob.php`
  * `CurrencySync.php` / `ImportCurrencyJob.php`

---

## Verification Plan

### Automated Tests
Run PHPUnit tests to verify that unit test cases mock the service API responses:
```bash
php artisan test
```

### Manual Verification
1. Access the **Global Country Dashboard** and verify that Weather, GDP/Inflation, and Currency Exchange Rates render instantly via real-time cached API queries.
2. Verify that **Risk Scoring Engine** continues to compute weighted risk levels accurately on the fly.
