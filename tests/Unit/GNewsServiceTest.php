<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GNewsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GNewsServiceTest extends TestCase
{
    protected GNewsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gnews.key' => 'test_api_key']);
        $this->service = new GNewsService();
    }

    public function test_get_news_makes_http_request(): void
    {
        Http::fake([
            'gnews.io/*' => Http::response([
                'articles' => [
                    [
                        'title' => 'Test Article',
                        'description' => 'Test Description',
                        'url' => 'https://example.com/test',
                        'publishedAt' => '2026-07-19T00:00:00Z',
                        'source' => ['name' => 'Test Source'],
                    ]
                ]
            ], 200)
        ]);

        $articles = $this->service->getNews('supply chain');
        $this->assertCount(1, $articles);
        $this->assertEquals('Test Article', $articles[0]['title']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'gnews.io') &&
                   $request['q'] === 'supply chain' &&
                   $request['apikey'] === 'test_api_key';
        });
    }

    public function test_get_supply_chain_news_uses_cache(): void
    {
        Cache::forget('gnews_supply_chain_articles');

        Http::fake([
            'gnews.io/*' => Http::response([
                'articles' => [
                    ['title' => 'Cached Title', 'url' => 'https://example.com/cached']
                ]
            ], 200)
        ]);

        // First call, should call API
        $articles = $this->service->getSupplyChainNews();
        $this->assertCount(1, $articles);
        $this->assertEquals('Cached Title', $articles[0]['title']);

        // Check if cache has it
        $this->assertTrue(Cache::has('gnews_supply_chain_articles'));

        // Clear HTTP fakes, if it makes another request it will fail or return empty
        Http::fake([
            'gnews.io/*' => Http::response([], 500)
        ]);

        // Second call, should return cached version
        $articlesSecond = $this->service->getSupplyChainNews();
        $this->assertEquals('Cached Title', $articlesSecond[0]['title']);
    }

    public function test_infer_category(): void
    {
        $categoryPorts = GNewsService::inferCategory('A new terminal at the shipping port');
        $this->assertEquals('PORT AUTHORITY NEWS', $categoryPorts);

        $categoryFinance = GNewsService::inferCategory('Inflation is affecting the GDP of the country');
        $this->assertEquals('FINANCE LOGISTICS DAILY', $categoryFinance);

        $categoryGeneral = GNewsService::inferCategory('Random unrelated text');
        $this->assertEquals('SUPPLY CHAIN DIGEST', $categoryGeneral);
    }

    public function test_default_image_for_category(): void
    {
        $image = GNewsService::getDefaultImageForCategory('PORT AUTHORITY NEWS');
        $this->assertNotNull($image);
        $this->assertStringStartsWith('https://', $image);
    }
}
