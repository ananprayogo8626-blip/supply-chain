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
            $table->dropIndex(['data_year']);
        });
    }
};
