<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('economic_data', function (Blueprint $table) {
            $table->decimal('trade_balance', 20, 2)->nullable()->after('imports');
        });

        // Auto-compute trade_balance from existing exports - imports data
        DB::statement('UPDATE economic_data SET trade_balance = COALESCE(exports, 0) - COALESCE(imports, 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('economic_data', function (Blueprint $table) {
            $table->dropColumn('trade_balance');
        });
    }
};
