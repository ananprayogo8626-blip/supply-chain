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
        Schema::table('economic_data', function (Blueprint $table) {
            // Add missing gdp_growth field
            if (!Schema::hasColumn('economic_data', 'gdp_growth')) {
                $table->decimal('gdp_growth', 8, 2)->nullable()->after('gdp')->comment('GDP Growth %');
            }
            
            // Add unique constraint on country_id to prevent duplicates
            if (!Schema::hasIndex('economic_data', 'economic_data_country_id_unique')) {
                $table->unique('country_id');
            }
            
            // Add index on data_year for performance
            if (!Schema::hasIndex('economic_data', 'economic_data_data_year_index')) {
                $table->index('data_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('economic_data', function (Blueprint $table) {
            $table->dropUnique(['country_id']);
            $table->dropIndex(['data_year']);
            $table->dropColumnIfExists('gdp_growth');
        });
    }
};
