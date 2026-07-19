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
        // Drop news_cache view first to avoid SQLite column alteration issues
        DB::statement("DROP VIEW IF EXISTS news_cache");

        Schema::table('news', function (Blueprint $table) {
            $table->text('url')->nullable()->change();
            $table->text('image')->nullable()->change();
        });

        // Recreate news_cache view
        DB::statement("CREATE VIEW news_cache AS SELECT * FROM news");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS news_cache");

        Schema::table('news', function (Blueprint $table) {
            $table->string('url')->nullable()->change();
            $table->string('image')->nullable()->change();
        });

        DB::statement("CREATE VIEW news_cache AS SELECT * FROM news");
    }
};
