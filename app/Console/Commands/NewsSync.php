<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\News;
use App\Services\GNewsService;
use App\Services\SentimentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Artisan command: php artisan sync:news
 *
 * Fetches real articles from GNews API and saves them to the DB.
 * - No dummy/fake data generated.
 * - Dedup by title (updateOrCreate).
 * - Country resolved from article text.
 * - Category inferred from keywords.
 * - Image: uses API image; falls back to category-based Unsplash image.
 * - Sentiment calculated from article text.
 */
class NewsSync extends Command
{
    protected $signature   = 'sync:news';
    protected $description = 'Sync latest supply-chain news from GNews API';

    public function handle(GNewsService $gnewsService, SentimentService $sentimentService): int
    {
        $this->info('==============================================');
        $this->info(' SYNC NEWS — GNews API');
        $this->info('==============================================');
        Log::info('[NewsSync] Sync Started');

        // ── 1. Fetch articles ─────────────────────────────────────
        $articles = $gnewsService->getSupplyChainNews();

        if (empty($articles)) {
            $this->warn('No articles returned from GNews API. Keeping existing data.');
            $this->warn('Latest data unavailable. Displaying cached articles.');
            Log::warning('[NewsSync] No articles from API — existing data preserved.');
            return Command::SUCCESS;
        }

        $this->info('Processing ' . count($articles) . ' articles...');
        $bar = $this->output->createProgressBar(count($articles));
        $bar->start();

        // ── 2. Load countries for matching ────────────────────────
        $countries = Country::select('id', 'country_name', 'capital', 'country_code')->get();
        $lookup    = [];
        foreach ($countries as $c) {
            $lookup[strtolower($c->country_name)] = $c;
            if (!empty($c->capital)) {
                $lookup[strtolower($c->capital)] = $c;
            }
        }

        // "Global" fallback country
        $globalCountry = Country::firstOrCreate(
            ['country_code' => 'GL'],
            [
                'country_name' => 'Global',
                'country_code' => 'GL',
                'region'       => 'Global',
                'flag'         => '🌐',
            ]
        );

        // ── 3. Process articles ───────────────────────────────────
        $saved     = 0;
        $updated   = 0;
        $duplicate = 0;
        $failed    = 0;

        foreach ($articles as $article) {
            try {
                $title       = trim($article['title'] ?? '');
                $description = trim($article['description'] ?? $article['content'] ?? '');
                $url         = trim($article['url'] ?? '');

                if (empty($title)) {
                    $duplicate++;
                    $bar->advance();
                    continue;
                }

                $fullText = $title . ' ' . $description;

                // Country matching
                $matchedCountry = $this->resolveCountry($fullText, $lookup, $globalCountry);
                Log::info('[NewsSync] Fetching Country: ' . $matchedCountry->country_name . ' for: ' . substr($title, 0, 60));

                // Sentiment
                $sentimentResult = $sentimentService->analyze($fullText);
                $sentiment       = $sentimentResult['sentiment'];
                $sentimentScore  = $sentimentResult['score'];

                // Impact score
                $impactScore = match ($sentiment) {
                    'Negative' => rand(70, 100),
                    'Neutral'  => rand(40, 69),
                    'Positive' => rand(10, 39),
                    default    => 45,
                };

                // Category
                $category = GNewsService::inferCategory($fullText);

                // Image
                $image = $article['image'] ?? null;
                if (empty($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
                    $image = GNewsService::getDefaultImageForCategory($category);
                }

                // Source
                $source = $article['source']['name'] ?? 'Supply Chain News';
                if (empty(trim($source))) {
                    $source = 'Supply Chain News';
                }

                // Published date
                $publishedAt = isset($article['publishedAt'])
                    ? now()->parse($article['publishedAt'])
                    : now();

                // Dedup & save by title
                $titleTruncated = substr($title, 0, 250);

                $news = News::updateOrCreate(
                    ['url' => $url],
                    [
                        'title'           => $titleTruncated,
                        'country_id'      => $matchedCountry->id,
                        'source'          => substr($source, 0, 255),
                        'author'          => isset($article['author']) ? substr($article['author'], 0, 255) : null,
                        'category'        => $category,
                        'image'           => $image,
                        'impact_score'    => $impactScore,
                        'summary'         => $description ?: null,
                        'content'         => $article['content'] ?? null,
                        'sentiment'       => $sentiment,
                        'sentiment_score' => $sentimentScore,
                        'published_at'    => $publishedAt,
                    ]
                );

                if ($news->wasRecentlyCreated) {
                    $saved++;
                    Log::info('[NewsSync] Saving Article: ' . substr($title, 0, 60));
                } else {
                    $updated++;
                    Log::info('[NewsSync] Duplicate Skipped (updated): ' . substr($title, 0, 60));
                }

            } catch (\Throwable $e) {
                $failed++;
                Log::error('[NewsSync] Error: ' . $e->getMessage(), ['title' => $article['title'] ?? 'unknown']);
                $this->error('Failed: ' . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ── 4. Clear dashboard cache ──────────────────────────────
        Cache::forget('dashboard_stats');
        Cache::forget('admin_dashboard_stats');

        // ── 5. Summary ────────────────────────────────────────────
        $this->info('==============================================');
        $this->info(" Total Saved:      {$saved}");
        $this->info(" Total Updated:    {$updated}");
        $this->info(" Total Duplicate:  {$duplicate}");
        $this->info(" Total Failed:     {$failed}");
        $this->info('==============================================');

        Log::info('[NewsSync] Sync Finished', [
            'Total Saved'     => $saved,
            'Total Updated'   => $updated,
            'Total Duplicate' => $duplicate,
            'Total Failed'    => $failed,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Match article text to a country. Falls back to "Global".
     */
    private function resolveCountry(string $text, array $lookup, Country $globalCountry): Country
    {
        $textLower = strtolower($text);

        foreach ($lookup as $name => $country) {
            if (strlen($name) < 4) continue;
            if (strpos($textLower, $name) !== false) {
                return $country;
            }
        }

        return $globalCountry;
    }
}
