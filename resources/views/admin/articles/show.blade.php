<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="sg-page-title flex items-center gap-2">
                    <i data-lucide="file-text" class="text-amber-400 w-6 h-6"></i>
                    Detail Artikel Analisis
                </h1>
                <p class="text-xs text-slate-400 mt-1">Diterbitkan oleh {{ $article->user->name ?? 'Admin' }} pada {{ optional($article->published_at ?? $article->created_at)->format('d M Y, H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('articles.edit', $article->id) }}" class="sg-btn sg-btn-secondary flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                </a>
                <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-dark flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="sg-panel sg-glass max-w-4xl mx-auto space-y-6">
        <!-- Metadata Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-4">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 text-xs font-semibold rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $article->category }}
                </span>
                @if($article->status === 'Published')
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        Published
                    </span>
                @else
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-500/10 text-slate-400 border border-slate-500/20">
                        Draft
                    </span>
                @endif
            </div>

            <div class="text-xs text-slate-400 flex items-center gap-4">
                <span class="flex items-center gap-1"><i data-lucide="eye" class="w-4 h-4"></i> {{ number_format($article->views) }} views</span>
                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $article->created_at->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-white tracking-tight leading-snug">
            {{ $article->title }}
        </h2>

        <!-- Thumbnail Image -->
        @if($article->thumbnail)
            <div class="rounded-xl overflow-hidden border border-white/10 max-h-96">
                <img src="{{ filter_var($article->thumbnail, FILTER_VALIDATE_URL) ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $article->title }}">
            </div>
        @endif

        <!-- Summary -->
        @if($article->summary)
            <div class="p-4 rounded-lg bg-amber-500/5 border border-amber-500/20 text-slate-300 text-sm font-medium italic leading-relaxed">
                "{{ $article->summary }}"
            </div>
        @endif

        <!-- Content -->
        <div class="text-slate-200 leading-relaxed space-y-4 whitespace-pre-line font-sans">
            {{ $article->content }}
        </div>
    </div>
</x-app-layout>
