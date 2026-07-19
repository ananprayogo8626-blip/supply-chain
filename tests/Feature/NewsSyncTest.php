<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Country;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create fallback "Global" country
        Country::create([
            'country_name' => 'Global',
            'country_code' => 'GL',
            'region'       => 'Global',
            'flag'         => '🌐',
        ]);

        // Create a specific country for matching test
        Country::create([
            'country_name' => 'Australia',
            'country_code' => 'AU',
            'region'       => 'Oceania',
            'flag'         => '🇦🇺',
        ]);

        config(['services.gnews.key' => 'test_api_key']);
        Cache::forget('gnews_supply_chain_articles');
    }

    public function test_news_sync_command_saves_and_deduplicates_articles(): void
    {
        // 327-character long image URL to test VARCHAR(255) vs TEXT fix
        $longImageUrl = 'https://example.com/img?' . str_repeat('a', 300);

        Http::fake([
            'gnews.io/*' => Http::response([
                'articles' => [
                    // Article 1: Regular article matching Australia with long image URL
                    [
                        'title' => 'Supply chain update in Australia today',
                        'description' => 'A detailed summary of shipping in the region.',
                        'url' => 'https://example.com/au-news-1',
                        'publishedAt' => '2026-07-19T00:00:00Z',
                        'source' => ['name' => 'Aussie Logistics News'],
                        'image' => $longImageUrl,
                    ],
                    // Article 2: Duplicate of Article 1 (same title and country), but different URL
                    [
                        'title' => 'Supply chain update in Australia today',
                        'description' => 'A syndicated summary of shipping in the region.',
                        'url' => 'https://example.com/au-news-2',
                        'publishedAt' => '2026-07-19T01:00:00Z',
                        'source' => ['name' => 'Global Logistics Press'],
                        'image' => 'https://example.com/short.jpg',
                    ],
                ]
            ], 200)
        ]);

        // Run artisan command
        $this->artisan('sync:news --force')
            ->expectsOutputToContain('Total Saved:      1')
            ->expectsOutputToContain('Total Updated:    1')
            ->expectsOutputToContain('Total Failed:     0')
            ->assertExitCode(0);

        // Verify databases
        $this->assertEquals(1, News::count());

        $news = News::first();
        $this->assertEquals('Supply chain update in Australia today', $news->title);
        $this->assertEquals('Australia', $news->country->country_name);
        $this->assertEquals('https://example.com/short.jpg', $news->image); // Updated to latest
        $this->assertEquals('https://example.com/au-news-2', $news->url); // Verifies it was updated to the latest URL

        // Verify we can save the long URL by inserting/updating directly
        $news->update(['image' => $longImageUrl]);
        $this->assertEquals($longImageUrl, $news->fresh()->image);
    }
}
