<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SentimentSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = [
            'growth', 'increase', 'profit', 'stable', 'improve',
            'rise', 'improving', 'improved', 'strong', 'benefit',
            'positive', 'gain', 'surplus', 'recovery', 'recovering',
            'safe', 'boost', 'upgrade', 'success', 'expand', 'expansion',
            'progress', 'solution', 'resolved', 'active', 'opportunity', 'advantages', 'efficient'
        ];

        $negativeWords = [
            'war', 'crisis', 'inflation', 'delay', 'disaster',
            'disruption', 'risk', 'congestion', 'decline', 'fall',
            'drop', 'conflict', 'strike', 'shortage', 'bottleneck',
            'tariff', 'sanction', 'threat', 'unsafe', 'unstable',
            'shutdown', 'ban', 'protest', 'storm', 'typhoon', 'hurricane',
            'flood', 'earthquake', 'loss', 'critical', 'danger', 'damage', 'worse', 'negative', 'weak', 'block', 'barrier'
        ];

        // Seed positive_words table (PDF Spec page 7)
        if (Schema::hasTable('positive_words')) {
            foreach ($positiveWords as $word) {
                DB::table('positive_words')->updateOrInsert(
                    ['word' => $word],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        // Seed negative_words table (PDF Spec page 7)
        if (Schema::hasTable('negative_words')) {
            foreach ($negativeWords as $word) {
                DB::table('negative_words')->updateOrInsert(
                    ['word' => $word],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        // Seed sentiment_words table (unified table)
        if (Schema::hasTable('sentiment_words')) {
            foreach ($positiveWords as $word) {
                DB::table('sentiment_words')->updateOrInsert(
                    ['word' => $word],
                    ['type' => 'positive', 'updated_at' => now()]
                );
            }
            foreach ($negativeWords as $word) {
                DB::table('sentiment_words')->updateOrInsert(
                    ['word' => $word],
                    ['type' => 'negative', 'updated_at' => now()]
                );
            }
        }
    }
}
