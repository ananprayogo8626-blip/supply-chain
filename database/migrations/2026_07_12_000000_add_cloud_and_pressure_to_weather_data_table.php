<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            if (!Schema::hasColumn('weather_data', 'cloud')) {
                $table->integer('cloud')->nullable()->after('humidity')->comment('Cloud cover percentage');
            }
            if (!Schema::hasColumn('weather_data', 'pressure')) {
                $table->decimal('pressure', 6, 1)->nullable()->after('cloud')->comment('Surface pressure in hPa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            if (Schema::hasColumn('weather_data', 'cloud')) {
                $table->dropColumn('cloud');
            }
            if (Schema::hasColumn('weather_data', 'pressure')) {
                $table->dropColumn('pressure');
            }
        });
    }
};
