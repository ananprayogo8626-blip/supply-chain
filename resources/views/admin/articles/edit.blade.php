<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="sg-page-title flex items-center gap-2">
                    <i data-lucide="edit" class="text-amber-400 w-6 h-6"></i>
                    Edit Artikel Analisis
                </h1>
                <p class="text-xs text-slate-400 mt-1">Perbarui konten artikel analisis risiko.</p>
            </div>
            <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-secondary flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="sg-panel sg-glass max-w-4xl mx-auto">
        <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="sg-label">Judul Artikel <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="sg-input">
                @error('title') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="sg-label">Kategori <span class="text-red-400">*</span></label>
                    <select name="category" required class="sg-input">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $article->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="sg-label">Status Publikasi <span class="text-red-400">*</span></label>
                    <select name="status" required class="sg-input">
                        <option value="Draft" {{ old('status', $article->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Published" {{ old('status', $article->status) == 'Published' ? 'selected' : '' }}>Published</option>
                    </select>
                    @error('status') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="sg-label">URL Gambar Thumbnail (Opsional)</label>
                <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', filter_var($article->thumbnail, FILTER_VALIDATE_URL) ? $article->thumbnail : '') }}" placeholder="https://example.com/image.jpg" class="sg-input">
            </div>

            <div>
                <label class="sg-label">Unggah Ganti Gambar Thumbnail (File)</label>
                <input type="file" name="thumbnail" accept="image/*" class="sg-input border-dashed">
                @if($article->thumbnail)
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-slate-400">Thumbnail saat ini:</span>
                        <img src="{{ filter_var($article->thumbnail, FILTER_VALIDATE_URL) ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" class="h-10 rounded border border-white/10" alt="">
                    </div>
                @endif
                @error('thumbnail') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sg-label">Ringkasan Artikel (Summary)</label>
                <textarea name="summary" rows="2" class="sg-input">{{ old('summary', $article->summary) }}</textarea>
                @error('summary') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sg-label">Isi Lengkap Artikel <span class="text-red-400">*</span></label>
                <textarea name="content" rows="10" required class="sg-input font-mono text-sm leading-relaxed">{{ old('content', $article->content) }}</textarea>
                @error('content') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-dark">Batal</a>
                <button type="submit" class="sg-btn sg-btn-primary flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
