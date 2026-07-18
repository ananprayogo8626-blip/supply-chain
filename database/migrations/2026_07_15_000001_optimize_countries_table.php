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
            // Add missing subregion column
            if (!Schema::hasColumn('countries', 'subregion')) {
                $table->string('subregion')->nullable()->after('region');
            }
            
            // Add indexes for performance
            $table->index('country_name');
            $table->index('region');
            $table->index('capital');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['country_name']);
            $table->dropIndex(['region']);
            $table->dropIndex(['capital']);
            $table->dropColumnIfExists('subregion');
        });
    }
};
