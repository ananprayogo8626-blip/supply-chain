<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $query = Article::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->paginate(10)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $categories = ['Economy', 'Logistics', 'Shipping', 'Weather', 'Geopolitics', 'Supply Chain', 'Other'];
        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:Economy,Logistics,Shipping,Weather,Geopolitics,Supply Chain,Other',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'status'       => 'required|in:Draft,Published',
            'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'thumbnail_url'=> 'nullable|url|max:500',
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(5);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailPath = $request->thumbnail_url;
        }

        Article::create([
            'user_id'      => Auth::id() ?? 1,
            'title'        => $validated['title'],
            'slug'         => $slug,
            'category'     => $validated['category'],
            'summary'      => $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content'      => $validated['content'],
            'status'       => $validated['status'],
            'thumbnail'    => $thumbnailPath,
            'published_at' => $validated['status'] === 'Published' ? now() : null,
            'views'        => 0,
        ]);

        return redirect()->route('articles.index')->with('success', 'Artikel analisis berhasil dibuat.');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        $article->increment('views');
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        $categories = ['Economy', 'Logistics', 'Shipping', 'Weather', 'Geopolitics', 'Supply Chain', 'Other'];
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:Economy,Logistics,Shipping,Weather,Geopolitics,Supply Chain,Other',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'status'       => 'required|in:Draft,Published',
            'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'thumbnail_url'=> 'nullable|url|max:500',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail && !filter_var($article->thumbnail, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $article->thumbnail = $request->file('thumbnail')->store('articles', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $article->thumbnail = $request->thumbnail_url;
        }

        $article->title    = $validated['title'];
        $article->category = $validated['category'];
        $article->summary  = $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150);
        $article->content  = $validated['content'];

        if ($article->status !== 'Published' && $validated['status'] === 'Published') {
            $article->published_at = now();
        }
        $article->status = $validated['status'];
        $article->save();

        return redirect()->route('articles.index')->with('success', 'Artikel analisis berhasil diperbarui.');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->thumbnail && !filter_var($article->thumbnail, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artikel analisis berhasil dihapus.');
    }
}
