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
        Schema::table('news', function (Blueprint $table) {
            // Add unique constraint on country_id and title to prevent duplicates
            if (!Schema::hasIndex('news', 'news_country_title_unique')) {
                $table->unique(['country_id', 'title'], 'news_country_title_unique');
            }
            
            // Add index on sentiment for filtering (if not exists)
            if (!Schema::hasIndex('news', 'news_sentiment_index')) {
                $table->index('sentiment', 'news_sentiment_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasIndex('news', 'news_published_at_index')) {
                $table->dropIndex('news_published_at_index');
            }
            if (Schema::hasIndex('news', 'news_sentiment_index')) {
                $table->dropIndex('news_sentiment_index');
            }
            if (Schema::hasIndex('news', 'news_country_title_unique')) {
                $table->dropUnique('news_country_title_unique');
            }
        });
    }
};
