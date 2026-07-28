<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely attempt creating compatibility views if sentiment_words table exists
        if (Schema::hasTable('sentiment_words')) {
            try {
                DB::statement("DROP VIEW IF EXISTS positive_words");
                DB::statement("CREATE VIEW positive_words AS SELECT id, word, created_at, updated_at FROM sentiment_words WHERE type = 'positive'");
            } catch (\Throwable $e) {
                // Ignore if positive_words is already a real table
            }

            try {
                DB::statement("DROP VIEW IF EXISTS negative_words");
                DB::statement("CREATE VIEW negative_words AS SELECT id, word, created_at, updated_at FROM sentiment_words WHERE type = 'negative'");
            } catch (\Throwable $e) {
                // Ignore if negative_words is already a real table
            }
        }

        if (Schema::hasTable('news')) {
            try {
                DB::statement("DROP VIEW IF EXISTS news_cache");
                DB::statement("CREATE VIEW news_cache AS SELECT * FROM news");
            } catch (\Throwable $e) {
                // Ignore if view creation fails
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("DROP VIEW IF EXISTS positive_words");
            DB::statement("DROP VIEW IF EXISTS negative_words");
            DB::statement("DROP VIEW IF EXISTS news_cache");
        } catch (\Throwable $e) {}
    }
};
