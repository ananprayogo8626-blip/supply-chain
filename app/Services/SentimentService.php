<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SentimentService
{
    /**
     * Positive words dictionary fallback
     */
    protected $positiveWords = [
        'growth', 'rise', 'improving', 'improved', 'increase', 'stable', 'strong',
        'benefit', 'positive', 'gain', 'surplus', 'recovery', 'recovering', 'growth',
        'safe', 'boost', 'upgrade', 'success', 'expand', 'expansion', 'progress',
        'solution', 'resolved', 'active', 'opportunity', 'advantages', 'efficient'
    ];

    /**
     * Negative words dictionary fallback
     */
    protected $negativeWords = [
        'disruption', 'delay', 'crisis', 'risk', 'inflation', 'congestion', 'decline',
        'fall', 'drop', 'war', 'conflict', 'strike', 'shortage', 'bottleneck', 'tariff',
        'sanction', 'threat', 'unsafe', 'unstable', 'shutdown', 'ban', 'protest',
        'storm', 'typhoon', 'hurricane', 'flood', 'earthquake', 'disaster', 'loss',
        'critical', 'danger', 'damage', 'worse', 'negative', 'weak', 'block', 'barrier'
    ];

    /**
     * Analyze sentiment of a text and return result
     *
     * @param string $text
     * @return array ['sentiment' => 'Positive|Neutral|Negative', 'score' => int]
     */
    public function analyze(string $text): array
    {
        if (empty($text)) {
            return ['sentiment' => 'Neutral', 'score' => 0];
        }

        $text = strtolower($text);
        
        // Clean text a bit (remove basic punctuation)
        $text = preg_replace('/[^\w\s]/', '', $text);
        $words = explode(' ', $text);

        // Try to load words from database if table exists
        $posDict = $this->positiveWords;
        $negDict = $this->negativeWords;

        try {
            $dbWords = DB::table('sentiment_words')->get();
            if ($dbWords->count() > 0) {
                $posDict = $dbWords->filter(fn($w) => ($w->type ?? $w->sentiment ?? '') === 'positive')->pluck('word')->toArray();
                $negDict = $dbWords->filter(fn($w) => ($w->type ?? $w->sentiment ?? '') === 'negative')->pluck('word')->toArray();
            }
        } catch (\Exception $e) {
            // Silence DB exception and use fallback
        }

        $posCount = 0;
        $negCount = 0;

        foreach ($words as $word) {
            if (empty($word)) continue;
            
            if (in_array($word, $posDict)) {
                $posCount++;
            } elseif (in_array($word, $negDict)) {
                $negCount++;
            }
        }

        $score = $posCount - $negCount;

        if ($score > 0) {
            $sentiment = 'Positive';
        } elseif ($score < 0) {
            $sentiment = 'Negative';
        } else {
            $sentiment = 'Neutral';
        }

        // Normalize score between -100 and 100 (cap it for safety)
        // E.g., if there are 5 negative words, net score is -5.
        // Let's make it a normalized impact score from 0 to 100 where higher score means higher risk (more negative).
        // Wait, for the impact_score in News table, is it the risk/disruption impact?
        // Usually, news impact_score is 0-100 where higher = higher risk.
        // Let's return sentiment and sentiment_score.
        
        return [
            'sentiment' => $sentiment,
            'score' => $score, // simple word count diff
        ];
    }
}
