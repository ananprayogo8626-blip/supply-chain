<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Country;
use App\Models\News;
use App\Models\ImportProgress;
use App\Jobs\ImportNewsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImportNewsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create fallback country
        Country::create([
            'country_name' => 'Global',
            'country_code' => 'GL',
            'region'       => 'Global',
            'flag'         => '🌐',
        ]);

        config(['services.gnews.key' => 'test_api_key']);
        Cache::forget('gnews_supply_chain_articles');
    }

    public function test_import_news_job_updates_progress_and_saves_data(): void
    {
        Http::fake([
            'gnews.io/*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Global shipping delays increase',
                        'description' => 'A overview of global trade delays.',
                        'url' => 'https://example.com/global-delays',
                        'publishedAt' => '2026-07-19T00:00:00Z',
                        'source' => ['name' => 'Trade Review'],
                    ]
                ]
            ], 200)
        ]);

        $progress = ImportProgress::create([
            'service'    => 'news',
            'processed'  => 0,
            'total'      => 0,
            'percentage' => 0,
            'status'     => 'pending',
            'started_at' => now(),
        ]);

        // Dispatch job and run immediately
        ImportNewsJob::dispatchSync($progress->id);

        // Verify status and percentage updated
        $progress->refresh();
        $this->assertEquals('completed', $progress->status);
        $this->assertEquals(100.00, (float) $progress->percentage);
        $this->assertEquals(1, $progress->total);

        // Verify data in DB
        $this->assertEquals(1, News::count());
        $news = News::first();
        $this->assertEquals('Global shipping delays increase', $news->title);
        $this->assertEquals('Global', $news->country->country_name);
    }
}
