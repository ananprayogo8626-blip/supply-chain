<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * GNews API Service
 * Fetches real supply-chain news from https://gnews.io
 */
class GNewsService
{
    protected ?string $apiKey = null;
    protected string $baseUrl = 'https://gnews.io/api/v4/search';

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key') ?? env('GNEWS_API_KEY') ?? '';
        $this->baseUrl = config('services.gnews.url') ?? 'https://gnews.io/api/v4/search';
    }

    /**
     * Fetch news by keyword from GNews API.
     *
     * @param string $query  Search keyword
     * @param string $lang   Language (default: en)
     * @param int    $max    Maximum articles per request (GNews free: max 10)
     * @return array
     */
    public function getNews(string $query, string $lang = 'en', int $max = 10): array
    {
        if (empty($this->apiKey)) {
            Log::warning('GNewsService: No API key configured. Returning empty array.');
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->get($this->baseUrl, [
                    'q'      => $query,
                    'lang'   => $lang,
                    'max'    => $max,
                    'apikey' => $this->apiKey,
                    'sortby' => 'publishedAt',
                ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = isset($errorData['errors'])
                    ? implode(', ', (array) $errorData['errors'])
                    : ($errorData['message'] ?? 'Unknown API error');

                Log::error('GNewsService: API returned HTTP ' . $response->status() . ' for query "' . $query . '". Error: ' . $errorMessage);
                throw new \Exception("GNews API error (HTTP {$response->status()}): {$errorMessage}");
            }

            $data = $response->json();
            $articles = $data['articles'] ?? [];

            Log::info('GNewsService: Fetched ' . count($articles) . ' articles for query "' . $query . '"');
            return $articles;

        } catch (\Throwable $e) {
            Log::warning('GNewsService: Exception for query "' . $query . '": ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch supply-chain news across multiple topics.
     * Returns only real API data — no dummy/fallback data generated.
     *
     * @param bool $forceRefresh
     * @return array
     */
    public function getSupplyChainNews(bool $forceRefresh = false): array
    {
        $cacheKey = 'gnews_supply_chain_articles';

        if (!$forceRefresh && Cache::has($cacheKey)) {
            Log::info('GNewsService: Returning cached supply chain articles.');
            return Cache::get($cacheKey) ?: [];
        }

        $topics = [
            '"supply chain" OR logistics OR freight OR warehousing',
            'shipping OR cargo OR container OR port OR harbor OR terminal',
            'economy OR trade OR inflation OR export OR import'
        ];

        $allArticles = [];
        $seenUrls    = [];

        foreach ($topics as $topic) {
            Log::info('GNewsService: Fetching articles for topic "' . $topic . '"');
            try {
                $articles = $this->getNews($topic, 'en', 10);

                foreach ($articles as $article) {
                    $url = $article['url'] ?? '';
                    if ($url && isset($seenUrls[$url])) {
                        continue; // skip URL duplicates within this batch
                    }
                    if ($url) {
                        $seenUrls[$url] = true;
                    }
                    $allArticles[] = $article;
                }
            } catch (\Throwable $e) {
                Log::error('GNewsService: Error fetching topic "' . $topic . '": ' . $e->getMessage());
                // Propagate the exception to halt further queries to save quota on failures
                throw $e;
            }

            // Respect GNews rate-limit on free plan (100 req/day)
            usleep(300000); // 0.3 second between requests
        }

        Log::info('GNewsService: Total unique articles fetched: ' . count($allArticles));

        // Save to cache before returning
        if (!empty($allArticles)) {
            Cache::put($cacheKey, $allArticles, 600); // cache for 10 minutes
        }

        return $allArticles;
    }

    /**
     * Returns category-specific fallback image URLs (Unsplash).
     * Used when an article has no image from the API.
     */
    public static function getDefaultImageForCategory(string $category): string
    {
        $map = [
            'PORT AUTHORITY NEWS'      => 'https://images.unsplash.com/photo-1590674899484-d5640e854abe?w=800&q=80',
            'SUPPLY CHAIN DIGEST'      => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80',
            'FINANCE LOGISTICS DAILY'  => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80',
            'METEO SHIPPING NEWS'      => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=800&q=80',
            'TECHNOLOGY LOGISTICS'     => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&q=80',
            'GEOPOLITICAL RISK'        => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80',
            'TRADE POLICY'             => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800&q=80',
            'LOGISTICS INNOVATION'     => 'https://images.unsplash.com/photo-1553413077-190dd305871c?w=800&q=80',
            'GREEN SHIPPING'           => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=800&q=80',
        ];

        return $map[$category]
            ?? 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=800&q=80';
    }

    /**
     * Infer a display category label from article title + description text.
     */
    public static function inferCategory(string $text): string
    {
        $text = strtolower($text);

        $rules = [
            'PORT AUTHORITY NEWS'     => ['port', 'harbor', 'harbour', 'terminal', 'berth', 'wharf', 'quay', 'dock'],
            'SUPPLY CHAIN DIGEST'     => ['shipping', 'vessel', 'container', 'cargo', 'freight', 'logistics', 'supply chain', 'fleet'],
            'FINANCE LOGISTICS DAILY' => ['economy', 'economic', 'inflation', 'trade', 'gdp', 'financial', 'finance', 'tariff', 'export', 'import', 'sanction', 'currency'],
            'METEO SHIPPING NEWS'     => ['weather', 'storm', 'typhoon', 'hurricane', 'cyclone', 'flood', 'earthquake', 'rain', 'wind', 'drought'],
            'TECHNOLOGY LOGISTICS'    => ['technology', 'ai ', 'automation', 'digital', 'robot', 'software', 'blockchain', 'iot'],
            'GEOPOLITICAL RISK'       => ['war', 'conflict', 'geopolit', 'military', 'sanction', 'protest', 'coup', 'crisis', 'tension'],
            'TRADE POLICY'            => ['policy', 'regulation', 'agreement', 'treaty', 'wto', 'customs', 'duty', 'quota'],
            'LOGISTICS INNOVATION'    => ['innovation', 'efficiency', 'warehouse', 'distribution', 'drone', 'electric vehicle'],
            'GREEN SHIPPING'          => ['sustainable', 'green', 'carbon', 'emission', 'eco', 'renewable', 'environment'],
        ];

        foreach ($rules as $label => $keywords) {
            foreach ($keywords as $kw) {
                if (strpos($text, $kw) !== false) {
                    return $label;
                }
            }
        }

        return 'SUPPLY CHAIN DIGEST';
    }
}
