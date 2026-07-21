<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Weather, economic, dan currency data sekarang diambil real-time dari API
 * (Open-Meteo, World Bank, ExchangeRate) tanpa persistensi, sesuai skema
 * database resmi di PROJECT FINAL.pdf yang tidak menyertakan tabel-tabel ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('weather_histories');
        Schema::dropIfExists('economic_histories');
        Schema::dropIfExists('currency_histories');
        Schema::dropIfExists('weather_data');
        Schema::dropIfExists('economic_data');
        Schema::dropIfExists('currency_data');
    }

    public function down(): void
    {
        // Intentionally left blank — these tables are permanently retired in favor
        // of real-time API calls (see App\Services\LiveCountryDataService).
    }
};
