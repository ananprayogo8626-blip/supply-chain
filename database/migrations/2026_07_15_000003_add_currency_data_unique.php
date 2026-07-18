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
        Schema::table('currency_data', function (Blueprint $table) {
            // Add index on currency_code
            if (!Schema::hasIndex('currency_data', 'currency_data_currency_code_index')) {
                $table->index('currency_code');
            }
            
            // Add unique constraint to prevent duplicates
            if (!Schema::hasIndex('currency_data', 'currency_data_country_id_currency_code_unique')) {
                $table->unique(['country_id', 'currency_code']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currency_data', function (Blueprint $table) {
            $table->dropUnique(['country_id', 'currency_code']);
            $table->dropIndex(['currency_code']);
        });
    }
};
