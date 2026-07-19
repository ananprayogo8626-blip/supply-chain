<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SentimentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class SentimentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SentimentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SentimentService();
    }

    public function test_analyze_with_empty_text(): void
    {
        $result = $this->service->analyze('');
        $this->assertEquals('Neutral', $result['sentiment']);
        $this->assertEquals(0, $result['score']);
    }

    public function test_analyze_with_fallback_positive_words(): void
    {
        $result = $this->service->analyze('The economy is showing stable growth and safe upgrade.');
        $this->assertEquals('Positive', $result['sentiment']);
        $this->assertGreaterThan(0, $result['score']);
    }

    public function test_analyze_with_fallback_negative_words(): void
    {
        $result = $this->service->analyze('A severe disruption and crisis delay occurred.');
        $this->assertEquals('Negative', $result['sentiment']);
        $this->assertLessThan(0, $result['score']);
    }

    public function test_analyze_with_database_words(): void
    {
        // Seed database words
        DB::table('sentiment_words')->insert([
            ['word' => 'stellar', 'type' => 'positive'],
            ['word' => 'catastrophe', 'type' => 'negative'],
        ]);

        // Test positive match from DB
        $resultPos = $this->service->analyze('We had a stellar performance today!');
        $this->assertEquals('Positive', $resultPos['sentiment']);
        $this->assertEquals(1, $resultPos['score']);

        // Test negative match from DB
        $resultNeg = $this->service->analyze('The situation is a complete catastrophe.');
        $this->assertEquals('Negative', $resultNeg['sentiment']);
        $this->assertEquals(-1, $resultNeg['score']);
    }
}
