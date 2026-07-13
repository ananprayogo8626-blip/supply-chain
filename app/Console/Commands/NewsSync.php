<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\News;
use App\Services\GNewsService;
use App\Services\SentimentService;
use Illuminate\Support\Facades\Log;

class NewsSync extends Command
{
    protected $signature = 'news:sync {--batch=1} {--total-batches=10}';
    protected $description = 'Sinkronisasi berita internasional dan analisis sentimen menggunakan GNews API';

    public function handle(GNewsService $gnewsService, SentimentService $sentimentService)
    {
        $this->info('=============================================');
        $this->info('SYNC NEWS AND ANALYZE SENTIMENT');
        $this->info('=============================================');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        $articles = $gnewsService->getSupplyChainNews();

        if (count($articles) === 0) {
            $this->error('Tidak ada berita ditemukan.');
            return Command::FAILURE;
        }

        $offset = ($batch - 1) * $batchSize;
        $countries = Country::offset($offset)->limit($batchSize)->get();

        if ($countries->count() == 0) {
            $this->warn('No countries in this batch.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($articles));
        $bar->start();

        foreach ($articles as $article) {
            try {
                $title = $article['title'] ?? '';
                $description = $article['description'] ?? $article['content'] ?? '';
                $fullText = $title . ' ' . $description;

                // Find matching countries in the text
                $matchedCountries = [];
                foreach ($countries as $country) {
                    if (
                        stripos($fullText, $country->country_name) !== false ||
                        stripos($fullText, $country->capital) !== false
                    ) {
                        $matchedCountries[] = $country;
                    }
                }

                // Skip if no country matches
                if (empty($matchedCountries)) {
                    $bar->advance();
                    continue;
                }

                // Perform sentiment analysis
                $sentimentResult = $sentimentService->analyze($fullText);
                $sentiment = $sentimentResult['sentiment'];
                $score = $sentimentResult['score'];

                // Calculate impact score (0-100) based on sentiment
                // Negative sentiment = higher risk impact
                if ($sentiment === 'Negative') {
                    $impactScore = min(100, 50 + (abs($score) * 10));
                } elseif ($sentiment === 'Positive') {
                    $impactScore = max(10, 30 - ($score * 10));
                } else {
                    $impactScore = 45; // Neutral impact
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
                }
            } catch (\Exception $e) {
                $this->error("Failed to process news article: " . $e->getMessage());
                Log::error("NewsSync error: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('=============================================');
        $this->info('SYNC NEWS AND SENTIMENT BATCH COMPLETED');
        $this->info('=============================================');

        return Command::SUCCESS;
    }
}
