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
        // Safely drop compatibility views if present
        try {
            DB::statement("DROP VIEW IF EXISTS positive_words");
            DB::statement("DROP VIEW IF EXISTS negative_words");
        } catch (\Throwable $e) {
            // Ignore if view drop is not supported or fails
        }

        if (!Schema::hasTable('positive_words')) {
            Schema::create('positive_words', function (Blueprint $table) {
                $table->id();
                $table->string('word')->unique();
                $table->timestamps();
            });

            // Seed initial positive dictionary words from PDF spec
            $positives = [
                'growth', 'increase', 'profit', 'stable', 'improve',
                'rise', 'improving', 'improved', 'strong', 'benefit',
                'positive', 'gain', 'surplus', 'recovery', 'recovering',
                'safe', 'boost', 'upgrade', 'success', 'expansion',
                'progress', 'solution', 'resolved', 'opportunity', 'advantages', 'efficient'
            ];

            foreach ($positives as $word) {
                DB::table('positive_words')->insertOrIgnore([
                    'word' => $word,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (!Schema::hasTable('negative_words')) {
            Schema::create('negative_words', function (Blueprint $table) {
                $table->id();
                $table->string('word')->unique();
                $table->timestamps();
            });

            // Seed initial negative dictionary words from PDF spec
            $negatives = [
                'war', 'crisis', 'inflation', 'delay', 'disaster',
                'disruption', 'risk', 'congestion', 'decline', 'fall',
                'drop', 'conflict', 'strike', 'shortage', 'bottleneck',
                'tariff', 'sanction', 'threat', 'unsafe', 'unstable',
                'shutdown', 'ban', 'protest', 'storm', 'typhoon', 'hurricane', 'flood', 'earthquake'
            ];

            foreach ($negatives as $word) {
                DB::table('negative_words')->insertOrIgnore([
                    'word' => $word,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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
        } catch (\Throwable $e) {}

        Schema::dropIfExists('positive_words');
        Schema::dropIfExists('negative_words');
    }
};
