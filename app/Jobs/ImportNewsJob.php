<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\News;
use App\Models\ImportProgress;
use App\Services\GNewsService;
use App\Services\SentimentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'news',
            'processed' => 0,
            'total' => 0,
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle(GNewsService $gnewsService, SentimentService $sentimentService)
    {
        try {
            $this->progress->update(['status' => 'processing']);

            $articles = $gnewsService->getSupplyChainNews();

            if (count($articles) === 0) {
                $this->progress->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                ]);
                Log::error("ImportNewsJob: Failed to get articles from GNews API");
                return;
            }

            $totalCountries = Country::count();
            $this->progress->update(['total' => count($articles) * $totalCountries]);

            $importedCount = 0;

            foreach ($articles as $article) {
                try {
                    $title = $article['title'] ?? '';
                    $description = $article['description'] ?? $article['content'] ?? '';
                    $fullText = $title . ' ' . $description;

                    $matchedCountries = [];
                    
                    // Process countries in chunks to avoid memory issues
                    Country::chunk(20, function ($countries) use ($fullText, &$matchedCountries) {
                        foreach ($countries as $country) {
                            if (
                                stripos($fullText, $country->country_name) !== false ||
                                stripos($fullText, $country->capital) !== false
                            ) {
                                $matchedCountries[] = $country;
                            }
                        }
                    });

                    if (empty($matchedCountries) && $totalCountries > 0) {
                        $matchedCountries[] = Country::inRandomOrder()->first();
                    }

                    $sentimentResult = $sentimentService->analyze($fullText);
                    $sentiment = $sentimentResult['sentiment'];
                    $score = $sentimentResult['score'];

                    if ($sentiment === 'Negative') {
                        $impactScore = min(100, 50 + (abs($score) * 10));
                    } elseif ($sentiment === 'Positive') {
                        $impactScore = max(10, 30 - ($score * 10));
                    } else {
                        $impactScore = rand(35, 55);
                    }

                    foreach ($matchedCountries as $country) {
                        News::updateOrCreate(
                            [
                                'country_id' => $country->id,
                                'title'      => substr($title, 0, 250),
                            ],
                            [
                                'source'          => $article['source']['name'] ?? 'Unknown',
                                'category'        => 'Supply Chain',
                                'url'             => $article['url'] ?? null,
                                'image'           => $article['image'] ?? null,
                                'impact_score'    => $impactScore,
                                'summary'         => $description,
                                'sentiment'       => $sentiment,
                                'sentiment_score' => $score,
                                'published_at'    => isset($article['publishedAt']) ? now()->parse($article['publishedAt']) : now(),
                            ]
                        );
                        $importedCount++;
                        
                        // Update progress
                        $this->progress->update([
                            'processed' => $importedCount,
                            'percentage' => ($importedCount / $this->progress->total) * 100,
                        ]);
                    }

                } catch (\Throwable $e) {
                    Log::error("ImportNewsJob: Error processing article: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                    continue;
                }
            }

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("ImportNewsJob error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->progress->update([
                'status' => 'failed',
                'finished_at' => now(),
            ]);
        }
    }
}
