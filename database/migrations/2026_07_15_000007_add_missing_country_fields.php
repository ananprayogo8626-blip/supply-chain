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
            // Add missing iso3 field
            if (!Schema::hasColumn('countries', 'iso3')) {
                $table->string('iso3', 3)->nullable()->after('country_code');
            }
            
            // Add missing timezone field
            if (!Schema::hasColumn('countries', 'timezone')) {
                $table->string('timezone')->nullable()->after('longitude');
            }
            
            // Add index on iso3 for performance
            if (!Schema::hasIndex('countries', 'countries_iso3_index')) {
                $table->index('iso3');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['iso3']);
            $table->dropColumnIfExists('iso3');
            $table->dropColumnIfExists('timezone');
        });
    }
};
