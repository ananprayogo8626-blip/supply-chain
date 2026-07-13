<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'image')) {
                $table->string('image')->nullable()->after('url');
            }
            if (!Schema::hasColumn('news', 'sentiment')) {
                $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])->default('Neutral')->after('summary');
            }
            if (!Schema::hasColumn('news', 'sentiment_score')) {
                $table->integer('sentiment_score')->default(0)->after('sentiment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('news', 'sentiment')) {
                $table->dropColumn('sentiment');
            }
            if (Schema::hasColumn('news', 'sentiment_score')) {
                $table->dropColumn('sentiment_score');
            }
        });
    }
};
