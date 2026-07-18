<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\News;
use App\Models\ImportProgress;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Services\GNewsService;
use App\Services\SentimentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /**
     * Menampilkan semua data berita
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $sentiment = $request->get('sentiment', '');
        $category = $request->get('category', '');
        $country = $request->get('country', '');
        $sort = $request->get('sort', 'latest');
        $status = $request->get('status', '');

        $cacheKey = "news_index_p{$page}_s" . md5($search) . "_sent{$sentiment}_cat{$category}_coun{$country}_sort{$sort}_st{$status}";

        $news = Cache::remember($cacheKey, 300, function() use ($search, $sentiment, $category, $country, $sort, $status) {
            $query = News::with('country');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('summary', 'LIKE', "%{$search}%")
                      ->orWhere('source', 'LIKE', "%{$search}%")
                      ->orWhereHas('country', function ($cq) use ($search) {
                          $cq->where('country_name', 'LIKE', "%{$search}%");
                      });
                });
            }

            if (!empty($sentiment)) {
                $query->where('sentiment', $sentiment);
            }

            if (!empty($category)) {
                $query->where('category', $category);
            }

            if (!empty($country)) {
                $query->where('country_id', $country);
            }

            if ($status === 'trash') {
                $query->onlyTrashed();
            }

            $sortDirection = str_ends_with($sort, '_desc') ? 'desc' : 'asc';
            $sortField     = str_replace('_desc', '', str_replace('_asc', '', $sort));

            if (in_array($sortField, ['title', 'published_at', 'impact_score', 'created_at'])) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->latest('published_at');
            }

            return $query->paginate(12);
        });

        $countries = Country::orderBy('country_name')->get();

        return view('news.index', compact('news', 'countries'));
    }

    /**
     * Sinkronisasi berita global dan hitung sentimen
     */
    public function sync(Request $request)
    {
        try {
            $progress = ImportProgress::create([
                'service'    => 'news',
                'processed'  => 0,
                'total'      => 0,
                'percentage' => 0,
                'status'     => 'pending',
                'started_at' => now(),
            ]);

            \App\Jobs\ImportNewsJob::dispatch($progress->id);

            $this->clearNewsCache();
            ActivityLog::log('Sync', 'Dispatched bulk News sync job.');

            return response()->json([
                'status'      => 'success',
                'message'     => 'News import job dispatched. Processing in background.',
                'progress_id' => $progress->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('NewsController@sync error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to start news import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan form tambah berita
     */
    public function create()
    {
        $countries = Country::orderBy('country_name')->get();
        return view('news.create', compact('countries'));
    }

    /**
     * Menyimpan data berita
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id'   => 'required|exists:countries,id',
            'title'        => 'required',
            'source'       => 'required',
            'category'     => 'nullable',
            'url'          => 'nullable|url',
            'impact_score' => 'nullable|integer|min:0|max:100',
            'summary'      => 'nullable',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|url',
            'sentiment'    => 'nullable',
            'author'       => 'nullable',
            'content'      => 'nullable',
        ]);

        $data = $request->all();

        // Auto Sentiment & Category Assignment
        if (empty($data['sentiment'])) {
            $textForAnalysis = ($data['title'] ?? '') . ' ' . ($data['summary'] ?? '') . ' ' . ($data['content'] ?? '');
            $analysis = app(SentimentService::class)->analyze($textForAnalysis);
            $data['sentiment'] = $analysis['sentiment'];
            $data['sentiment_score'] = $analysis['score'] ?? 0;
        }

        if (empty($data['category'])) {
            $data['category'] = $this->autoCategory($data['title'] ?? '', $data['summary'] ?? '');
        }

        if (empty($data['impact_score'])) {
            $data['impact_score'] = match($data['sentiment']) {
                'Negative' => 70,
                'Positive' => 20,
                default => 40
            };
        }

        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news = News::create($data);

        $this->clearNewsCache();
        ActivityLog::log('Create', "Created News: {$news->title} (#{$news->id})", $news);

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail berita
     */
    public function show(News $news)
    {
        $news->load('country');
        return view('news.show', compact('news'));
    }

    /**
     * Menampilkan form edit berita
     */
    public function edit(News $news)
    {
        $countries = Country::orderBy('country_name')->get();
        return view('news.edit', compact('news', 'countries'));
    }

    /**
     * Update data berita
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'country_id'   => 'required|exists:countries,id',
            'title'        => 'required',
            'source'       => 'required',
            'category'     => 'nullable',
            'url'          => 'nullable|url',
            'impact_score' => 'required|integer|min:0|max:100',
            'summary'      => 'nullable',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|url',
            'sentiment'    => 'nullable',
            'author'       => 'nullable',
            'content'      => 'nullable',
        ]);

        $data = $request->all();

        // Auto Sentiment & Category Assignment if modified/empty
        if (empty($data['sentiment'])) {
            $textForAnalysis = ($data['title'] ?? '') . ' ' . ($data['summary'] ?? '') . ' ' . ($data['content'] ?? '');
            $analysis = app(SentimentService::class)->analyze($textForAnalysis);
            $data['sentiment'] = $analysis['sentiment'];
            $data['sentiment_score'] = $analysis['score'] ?? 0;
        }

        if (empty($data['category'])) {
            $data['category'] = $this->autoCategory($data['title'] ?? '', $data['summary'] ?? '');
        }

        $news->update($data);

        $this->clearNewsCache();
        ActivityLog::log('Update', "Updated News: {$news->title} (#{$news->id})", $news);

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil diperbarui.');
    }

    /**
     * Hapus data berita
     */
    public function destroy(News $news)
    {
        $news->delete();

        $this->clearNewsCache();
        ActivityLog::log('Delete', "Soft-deleted News: {$news->title} (#{$news->id})", $news);

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil dihapus.');
    }

    /**
     * Restore berita
     */
    public function restore($id)
    {
        $news = News::onlyTrashed()->findOrFail($id);
        $news->restore();

        $this->clearNewsCache();
        ActivityLog::log('Restore', "Restored News: {$news->title} (#{$news->id})", $news);

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil dipulihkan.');
    }

    /**
     * Export news to CSV
     */
    public function exportCsv()
    {
        $news = News::with('country')->get();
        $headers = ['ID', 'Country Name', 'Title', 'Source', 'Category', 'URL', 'Impact Score', 'Sentiment', 'Published At'];

        return \App\Services\ExportImportHelper::exportCsv('news', $headers, $news, function($n) {
            return [
                $n->id,
                $n->country->country_name ?? '—',
                $n->title,
                $n->source,
                $n->category,
                $n->url,
                $n->impact_score,
                $n->sentiment,
                $n->published_at ? $n->published_at->toDateTimeString() : '—',
            ];
        });
    }

    /**
     * Export news to PDF
     */
    public function exportPdf()
    {
        $news = News::with('country')->get();
        $headers = ['ID', 'Country', 'Title', 'Source', 'Category', 'Impact', 'Sentiment', 'Published At'];
        $rows = [];
        foreach ($news as $n) {
            $rows[] = [
                $n->id,
                $n->country->country_name ?? '—',
                substr($n->title, 0, 40) . '...',
                $n->source,
                $n->category,
                $n->impact_score,
                $n->sentiment,
                $n->published_at ? $n->published_at->format('Y-m-d') : '—',
            ];
        }

        return \App\Services\ExportImportHelper::exportPdf('News Database', $headers, $rows);
    }

    /**
     * Import news from CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle);
            $imported = 0;
            $updated = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;

                $countryCode = $row[1] ?? '';
                $title = $row[2] ?? '';
                $source = $row[3] ?? '';
                $cat = $row[4] ?? '';
                $url = $row[5] ?? '';
                $impact = $row[6] ?? 50;
                $sentiment = $row[7] ?? 'Neutral';
                $pubAt = $row[8] ?? now();

                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) continue;

                $newsItem = News::withTrashed()->updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'title' => $title
                    ],
                    [
                        'source' => $source,
                        'category' => $cat ?: $this->autoCategory($title, ''),
                        'url' => $url,
                        'impact_score' => $impact,
                        'sentiment' => $sentiment,
                        'published_at' => $pubAt,
                    ]
                );

                if ($newsItem->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $newsItem->restore();
                    $updated++;
                }
            }

            fclose($handle);

            $this->clearNewsCache();
            ActivityLog::log('Import', "Imported {$imported} news articles, updated {$updated} from CSV.");

            return back()->with('success', "CSV Import Success: {$imported} new news articles created, {$updated} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Clear News caches
     */
    protected function clearNewsCache()
    {
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Auto assign category based on text analysis
     */
    protected function autoCategory($title, $summary)
    {
        $text = strtolower($title . ' ' . $summary);
        
        $categories = [
            'Disaster' => ['flood', 'storm', 'typhoon', 'hurricane', 'earthquake', 'tsunami', 'volcano', 'landslide', 'wildfire', 'weather', 'rain', 'drought'],
            'Political' => ['election', 'government', 'president', 'prime minister', 'parliament', 'coup', 'policy', 'protest', 'riot', 'sanction', 'tariff', 'trade war'],
            'Economic' => ['gdp', 'inflation', 'recession', 'currency', 'stock market', 'interest rate', 'central bank', 'trade', 'export', 'import', 'finance', 'deal'],
            'Security' => ['war', 'conflict', 'military', 'attack', 'bomb', 'terrorist', 'strike', 'protest', 'rebellion', 'cyberattack', 'piracy', 'hijack'],
            'Logistics' => ['port', 'shipping', 'delay', 'congestion', 'vessel', 'cargo', 'rail', 'truck', 'aviation', 'freight', 'carrier', 'supply chain', 'warehouse'],
        ];

        foreach ($categories as $cat => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $cat;
                }
            }
        }

        return 'General';
    }
}