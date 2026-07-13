<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\News;
use Illuminate\Http\Request;

use App\Services\GNewsService;
use App\Services\SentimentService;

class NewsController extends Controller
{
    /**
     * Menampilkan semua data berita
     */
    public function index()
    {
        $news = News::with('country')->latest()->get();

        return view('news.index', compact('news'));
    }

    /**
     * Sinkronisasi berita global dan hitung sentimen
     */
    public function sync(Request $request, GNewsService $gnewsService, SentimentService $sentimentService)
    {
        try {
            \App\Jobs\ImportNewsJob::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'News import started'
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("NewsController@sync error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to start news import: ' . $e->getMessage()
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
            'impact_score' => 'required|integer|min:0|max:100',
            'summary'      => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        News::create($request->all());

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail berita
     */
    public function show(News $news)
    {
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
        ]);

        $news->update($request->all());

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

        return redirect()
            ->route('news.index')
            ->with('success', 'Data berita berhasil dihapus.');
    }
}