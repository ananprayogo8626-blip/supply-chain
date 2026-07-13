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
        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso3', 3)->nullable()->after('country_code');
            $table->string('subregion')->nullable()->after('region');
        });

        Schema::table('economic_data', function (Blueprint $table) {
            $table->decimal('gdp_growth', 8, 2)->nullable()->after('gdp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['iso3', 'subregion']);
        });

        Schema::table('economic_data', function (Blueprint $table) {
            $table->dropColumn('gdp_growth');
        });
    }
};
