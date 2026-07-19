<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $analyst;
    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->analyst = User::factory()->create(['role' => 'analyst']);

        // Create country
        $this->country = Country::create([
            'country_name' => 'United States',
            'country_code' => 'US',
            'region'       => 'Americas',
            'flag'         => '🇺🇸',
        ]);
    }

    public function test_news_routes_require_authentication(): void
    {
        $this->get(route('news.index'))->assertRedirect(route('login'));
    }

    public function test_index_displays_news(): void
    {
        News::create([
            'country_id' => $this->country->id,
            'title' => 'Important supply chain update',
            'source' => 'Test Source',
            'url' => 'https://example.com/test',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->analyst)->get(route('news.index'));
        $response->assertOk();
        $response->assertSee('Important supply chain update');
    }

    public function test_store_creates_news(): void
    {
        $response = $this->actingAs($this->analyst)->post(route('news.store'), [
            'country_id' => $this->country->id,
            'title' => 'New Logistic Center Opened',
            'source' => 'Port Authority',
            'url' => 'https://example.com/port-news',
            'impact_score' => 45,
            'summary' => 'A new logistic center is opened in NY.',
            'published_at' => '2026-07-19',
        ]);

        $response->assertRedirect(route('news.index'));
        $this->assertDatabaseHas('news', [
            'title' => 'New Logistic Center Opened',
            'country_id' => $this->country->id,
        ]);
    }

    public function test_show_displays_news_detail(): void
    {
        $news = News::create([
            'country_id' => $this->country->id,
            'title' => 'Logistics detail news',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/detail',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->analyst)->get(route('news.show', $news));
        $response->assertOk();
        $response->assertSee('Logistics detail news');
    }

    public function test_update_modifies_news(): void
    {
        $news = News::create([
            'country_id' => $this->country->id,
            'title' => 'Old Title',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/old',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->analyst)->put(route('news.update', $news), [
            'country_id' => $this->country->id,
            'title' => 'Updated Title',
            'source' => 'Daily Logistics Updated',
            'url' => 'https://example.com/updated',
            'impact_score' => 60,
            'summary' => 'Updated Summary',
            'published_at' => '2026-07-19',
        ]);

        $response->assertRedirect(route('news.index'));
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_destroy_soft_deletes_news(): void
    {
        $news = News::create([
            'country_id' => $this->country->id,
            'title' => 'Title to delete',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/delete',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('news.destroy', $news));
        $response->assertRedirect(route('news.index'));
        
        $this->assertSoftDeleted('news', [
            'id' => $news->id,
        ]);
    }

    public function test_restore_recovers_deleted_news(): void
    {
        $news = News::create([
            'country_id' => $this->country->id,
            'title' => 'Deleted news to restore',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/restore',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);
        $news->delete();

        $response = $this->actingAs($this->superAdmin)->post(route('news.restore', $news->id));
        $response->assertRedirect(route('news.index'));
        
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'deleted_at' => null,
        ]);
    }

    public function test_export_csv(): void
    {
        News::create([
            'country_id' => $this->country->id,
            'title' => 'News for CSV export',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/csv',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('news.export-csv'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('News for CSV export', $response->streamedContent());
    }

    public function test_export_pdf(): void
    {
        News::create([
            'country_id' => $this->country->id,
            'title' => 'News for PDF export',
            'source' => 'Daily Logistics',
            'url' => 'https://example.com/pdf',
            'sentiment' => 'Neutral',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('news.export-pdf'));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        $response->assertSee('News for PDF export');
    }

    public function test_import_csv(): void
    {
        Storage::fake('local');

        $csvContent = "ID,Country Code,Title,Source,Category,URL,Impact Score,Sentiment,Published At\n";
        $csvContent .= ",US,Imported news title,Logistics Daily,Logistics,https://example.com/imported,55,Neutral,2026-07-19\n";

        $file = UploadedFile::fake()->createWithContent('news.csv', $csvContent);

        $response = $this->actingAs($this->superAdmin)->post(route('news.import-csv'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('news', [
            'title' => 'Imported news title',
            'country_id' => $this->country->id,
        ]);
    }
}
