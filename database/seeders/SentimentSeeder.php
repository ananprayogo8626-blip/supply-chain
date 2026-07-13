<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SentimentSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = [
            'growth', 'rise', 'improving', 'improved', 'increase', 'stable', 'strong',
            'benefit', 'positive', 'gain', 'surplus', 'recovery', 'recovering', 'growth',
            'safe', 'boost', 'upgrade', 'success', 'expand', 'expansion', 'progress',
            'solution', 'resolved', 'active', 'opportunity', 'advantages', 'efficient'
        ];

        $negativeWords = [
            'disruption', 'delay', 'crisis', 'risk', 'inflation', 'congestion', 'decline',
            'fall', 'drop', 'war', 'conflict', 'strike', 'shortage', 'bottleneck', 'tariff',
            'sanction', 'threat', 'unsafe', 'unstable', 'shutdown', 'ban', 'protest',
            'storm', 'typhoon', 'hurricane', 'flood', 'earthquake', 'disaster', 'loss',
            'critical', 'danger', 'damage', 'worse', 'negative', 'weak', 'block', 'barrier'
        ];

        $data = [];

        foreach ($positiveWords as $word) {
            $data[] = [
                'word' => $word,
                'type' => 'positive',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        foreach ($negativeWords as $word) {
            $data[] = [
                'word' => $word,
                'type' => 'negative',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insert or ignore to prevent duplication
        foreach ($data as $item) {
            DB::table('sentiment_words')->updateOrInsert(
                ['word' => $item['word']],
                ['type' => $item['type'], 'updated_at' => now()]
            );
        }
    }
}
