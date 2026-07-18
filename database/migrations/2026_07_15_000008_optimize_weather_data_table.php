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
            // Add missing weather_code field
            if (!Schema::hasColumn('weather_data', 'weather_code')) {
                $table->integer('weather_code')->nullable()->after('weather_condition')->comment('WMO weather code');
            }
            
            // Add cloud field if missing
            if (!Schema::hasColumn('weather_data', 'cloud')) {
                $table->integer('cloud')->nullable()->after('humidity')->comment('Cloud coverage %');
            }
            
            // Add pressure field if missing
            if (!Schema::hasColumn('weather_data', 'pressure')) {
                $table->decimal('pressure', 8, 2)->nullable()->after('cloud')->comment('Atmospheric pressure hPa');
            }
            
            // Add unique constraint on country_id to prevent duplicates
            if (!Schema::hasIndex('weather_data', 'weather_data_country_id_unique')) {
                $table->unique('country_id');
            }
            
            // Add index on updated_at for performance
            if (!Schema::hasIndex('weather_data', 'weather_data_updated_at_index')) {
                $table->index('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropUnique(['country_id']);
            $table->dropIndex(['updated_at']);
            $table->dropColumnIfExists('weather_code');
            $table->dropColumnIfExists('cloud');
            $table->dropColumnIfExists('pressure');
        });
    }
};
