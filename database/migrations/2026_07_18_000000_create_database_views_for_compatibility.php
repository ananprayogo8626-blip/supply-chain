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
        // Drop tables/views if they already exist to avoid errors
        $this->down();

        // Create positive_words view
        DB::statement("CREATE VIEW positive_words AS SELECT id, word, created_at, updated_at FROM sentiment_words WHERE type = 'positive'");

        // Create negative_words view
        DB::statement("CREATE VIEW negative_words AS SELECT id, word, created_at, updated_at FROM sentiment_words WHERE type = 'negative'");

        // Create news_cache view
        DB::statement("CREATE VIEW news_cache AS SELECT * FROM news");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS positive_words");
        DB::statement("DROP VIEW IF EXISTS negative_words");
        DB::statement("DROP VIEW IF EXISTS news_cache");
    }
};
