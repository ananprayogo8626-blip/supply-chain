<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::table('news')->where('sentiment', 'positive')->update(['sentiment' => 'Positive']);
            DB::table('news')->where('sentiment', 'neutral')->update(['sentiment' => 'Neutral']);
            DB::table('news')->where('sentiment', 'negative')->update(['sentiment' => 'Negative']);

            DB::statement("ALTER TABLE `news` MODIFY `sentiment` ENUM('Positive', 'Neutral', 'Negative') NOT NULL DEFAULT 'Neutral'");
        } catch (\Exception $e) {
            // Ignore if columns or table do not exist
        }
    }

    public function down(): void
    {
        try {
            DB::table('news')->where('sentiment', 'Positive')->update(['sentiment' => 'positive']);
            DB::table('news')->where('sentiment', 'Neutral')->update(['sentiment' => 'neutral']);
            DB::table('news')->where('sentiment', 'Negative')->update(['sentiment' => 'negative']);

            DB::statement("ALTER TABLE `news` MODIFY `sentiment` ENUM('positive', 'neutral', 'negative') NOT NULL DEFAULT 'neutral'");
        } catch (\Exception $e) {
            // Ignore if columns or table do not exist
        }
    }
};
