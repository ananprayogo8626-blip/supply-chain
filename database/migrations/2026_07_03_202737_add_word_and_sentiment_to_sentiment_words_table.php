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
        if (Schema::hasTable('sentiment_words')) {
            Schema::table('sentiment_words', function (Blueprint $table) {
                if (!Schema::hasColumn('sentiment_words', 'word')) {
                    $table->string('word')->unique();
                }
                if (!Schema::hasColumn('sentiment_words', 'sentiment')) {
                    $table->enum('sentiment', ['positive','neutral','negative']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sentiment_words')) {
            Schema::table('sentiment_words', function (Blueprint $table) {
                if (Schema::hasColumn('sentiment_words', 'word') || Schema::hasColumn('sentiment_words', 'sentiment')) {
                    $table->dropColumn(['word','sentiment']);
                }
            });
        }
    }
};
