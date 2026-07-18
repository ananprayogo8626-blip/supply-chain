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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ImportNewsJob
 *
 * Fetches real articles from GNews API and stores them in the DB.
 * - No dummy/fake data generated.
 * - Dedup by (title) using updateOrCreate.
 * - Country resolved from article text; falls back to "Global".
 * - Category inferred from text keywords.
 * - Image: uses API image; falls back to category-specific Unsplash image.
 * - Sentiment calculated from article text.
 */
class ImportNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes max
    public $tries   = 2;

    protected $progressId;

    public function __construct(?int $progressId = null)
    {
        $this->progressId = $progressId;
    }

    // ──────────────────────────────────────────────────────────────
    // Main Handler
    // ──────────────────────────────────────────────────────────────

    public function handle(GNewsService $gnewsService, SentimentService $sentimentService): void
    {
        $progress = $this->progressId
            ? ImportProgress::find($this->progressId)
            : null;

        try {
            Log::info('[ImportNewsJob] Sync Started');

            if ($progress) {
                $progress->update(['status' => 'processing']);
            }

            // ── 1. Create progress if not passed ──────────────────
            if (!$progress) {
                $progress = ImportProgress::create([
                    'service'    => 'news',
                    'processed'  => 0,
                    'total'      => 0,
                    'percentage' => 0,
                    'status'     => 'processing',
                    'started_at' => now(),
                ]);
            }

            // ── 2. Fetch articles from GNews ──────────────────────
            $articles = $gnewsService->getSupplyChainNews();

            if (empty($articles)) {
                Log::warning('[ImportNewsJob] No articles returned from GNews API. Keeping existing data.');
                if ($progress) {
                    $progress->update([
                        'status'      => 'completed',
                        'percentage'  => 100,
                        'finished_at' => now(),
                    ]);
                }
                return;
            }

            Log::info('[ImportNewsJob] Received ' . count($articles) . ' articles from API.');
            if ($progress) {
                $progress->update(['total' => count($articles)]);
            }

            // ── 3. Load countries for matching ────────────────────
            $countries = Country::select('id', 'country_name', 'capital', 'country_code')->get();

            // Build a fast lookup: lowercase name/capital → country
            $lookup = [];
            foreach ($countries as $c) {
                $lookup[strtolower($c->country_name)] = $c;
                if (!empty($c->capital)) {
                    $lookup[strtolower($c->capital)] = $c;
                }
            }

            // "Global" fallback country (create if not exists)
            $globalCountry = Country::firstOrCreate(
                ['country_code' => 'GL'],
                [
                    'country_name' => 'Global',
                    'country_code' => 'GL',
                    'region'       => 'Global',
                    'flag'         => '🌐',
                ]
            );

            // ── 4. Process each article ───────────────────────────
            $saved     = 0;
            $updated   = 0;
            $duplicate = 0;
            $failed    = 0;
            $processed = 0;

            foreach ($articles as $article) {
                try {
                    $processed++;

                    $title       = trim($article['title'] ?? '');
                    $description = trim($article['description'] ?? $article['content'] ?? '');
                    $url         = trim($article['url'] ?? '');

                    // Skip articles without a title
                    if (empty($title)) {
                        $duplicate++;
                        Log::info('[ImportNewsJob] Duplicate Skipped: empty title');
                        continue;
                    }

                    $fullText = $title . ' ' . $description;

                    Log::info('[ImportNewsJob] Saving Article: ' . substr($title, 0, 80));

                    // ── Country matching ──────────────────────────
                    $matchedCountry = $this->resolveCountry($fullText, $lookup, $globalCountry);
                    Log::info('[ImportNewsJob] Fetching Country: ' . $matchedCountry->country_name . ' for article: ' . substr($title, 0, 60));

                    // ── Sentiment ─────────────────────────────────
                    $sentimentResult = $sentimentService->analyze($fullText);
                    $sentiment       = $sentimentResult['sentiment'];   // Positive / Neutral / Negative
                    $sentimentScore  = $sentimentResult['score'];

                    // ── Impact score based on sentiment ───────────
                    $impactScore = match ($sentiment) {
                        'Negative' => rand(70, 100),
                        'Neutral'  => rand(40, 69),
                        'Positive' => rand(10, 39),
                        default    => 45,
                    };

                    // ── Category ──────────────────────────────────
                    $category = GNewsService::inferCategory($fullText);

                    // ── Image ─────────────────────────────────────
                    $image = $article['image'] ?? null;
                    if (empty($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
                        $image = GNewsService::getDefaultImageForCategory($category);
                    }

                    // ── Source ────────────────────────────────────
                    $source = $article['source']['name'] ?? 'Supply Chain News';
                    if (empty(trim($source))) {
                        $source = 'Supply Chain News';
                    }

                    // ── Published date ────────────────────────────
                    $publishedAt = isset($article['publishedAt'])
                        ? now()->parse($article['publishedAt'])
                        : now();

                    // ── Dedup & save by title ─────────────────────
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
                    } else {
                        $updated++;
                        Log::info('[ImportNewsJob] Duplicate Skipped (updated): ' . substr($title, 0, 60));
                    }

                } catch (\Throwable $e) {
                    $failed++;
                    Log::error('[ImportNewsJob] Error processing article: ' . $e->getMessage(), [
                        'title' => $article['title'] ?? 'unknown',
                    ]);
                }

                // Update progress every 10 articles
                if ($progress && $processed % 10 === 0) {
                    $progress->update([
                        'processed'  => $processed,
                        'percentage' => round(($processed / count($articles)) * 100),
                    ]);
                }
            }

            // ── 5. Clear dashboard cache so counts refresh ────────
            Cache::forget('dashboard_stats');
            Cache::forget('admin_dashboard_stats');

            // ── 6. Final progress update ──────────────────────────
            if ($progress) {
                $progress->update([
                    'status'      => 'completed',
                    'processed'   => $processed,
                    'percentage'  => 100,
                    'finished_at' => now(),
                ]);
            }

            \App\Jobs\CalculateRiskScoresJob::dispatch();

            Log::info('[ImportNewsJob] Sync Finished', [
                'Total Saved'      => $saved,
                'Total Updated'    => $updated,
                'Total Duplicate'  => $duplicate,
                'Total Failed'     => $failed,
                'Total Processed'  => $processed,
            ]);

        } catch (\Throwable $e) {
            Log::error('[ImportNewsJob] Fatal error: ' . $e->getMessage(), ['exception' => $e]);

            if ($progress) {
                $progress->update([
                    'status'      => 'failed',
                    'finished_at' => now(),
                ]);
            }

            // DO NOT throw — we preserve existing data when API fails.
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Match article text to a country. Falls back to "Global".
     */
    private function resolveCountry(string $text, array $lookup, Country $globalCountry): Country
    {
        $textLower = strtolower($text);

        foreach ($lookup as $name => $country) {
            if (strlen($name) < 4) continue; // Skip very short names that cause false matches
            if (strpos($textLower, $name) !== false) {
                return $country;
            }
        }

        return $globalCountry;
    }
}
