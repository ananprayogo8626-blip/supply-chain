<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="sg-page-title flex items-center gap-2">
                    <i data-lucide="file-text" class="text-amber-400 w-6 h-6"></i>
                    Kelola Artikel Analisis
                </h1>
                <p class="text-xs text-slate-400 mt-1">Buat, publikasikan, dan kelola artikel analisis risiko rantai pasok global.</p>
            </div>
            <div>
                <a href="{{ route('articles.create') }}" class="sg-btn sg-btn-primary flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tulis Artikel Baru
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filter & Search Bar -->
    <div class="sg-panel sg-glass mb-6">
        <form method="GET" action="{{ route('articles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="sg-label">Cari Artikel</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, ringkasan, atau isi..." class="sg-input pl-9">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                </div>
            </div>
            <div>
                <label class="sg-label">Kategori</label>
                <select name="category" class="sg-input">
                    <option value="">Semua Kategori</option>
                    @foreach(['Economy', 'Logistics', 'Shipping', 'Weather', 'Geopolitics', 'Supply Chain', 'Other'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="sg-btn sg-btn-secondary flex-1">Filter</button>
                <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-dark">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="sg-panel sg-glass">
        <div class="overflow-x-auto">
            <table class="sg-table">
                <thead>
                    <tr>
                        <th>Artikel</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($article->thumbnail)
                                        <img src="{{ filter_var($article->thumbnail, FILTER_VALIDATE_URL) ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" class="w-12 h-10 object-cover rounded border border-white/10" alt="">
                                    @else
                                        <div class="w-12 h-10 bg-slate-800 rounded flex items-center justify-center text-slate-500 border border-white/10">
                                            <i data-lucide="image" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('articles.show', $article->id) }}" class="font-semibold text-white hover:text-amber-400 transition">
                                            {{ Str::limit($article->title, 45) }}
                                        </a>
                                        <p class="text-[11px] text-slate-400">{{ Str::limit($article->summary, 50) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    {{ $article->category }}
                                </span>
                            </td>
                            <td class="text-xs text-slate-300">
                                {{ $article->user->name ?? 'Admin' }}
                            </td>
                            <td>
                                @if($article->status === 'Published')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Published
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-400">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> {{ number_format($article->views) }}
                                </span>
                            </td>
                            <td class="text-xs text-slate-400">
                                {{ $article->created_at->format('d M Y') }}
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('articles.show', $article->id) }}" class="text-sky-400 hover:text-sky-300 p-1" title="Lihat">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('articles.edit', $article->id) }}" class="text-amber-400 hover:text-amber-300 p-1" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                Belum ada artikel analisis yang dibuat. Klik <strong>Tulis Artikel Baru</strong> di atas untuk membuat artikel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
