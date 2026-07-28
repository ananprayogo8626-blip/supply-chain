<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="sg-page-title flex items-center gap-2">
                    <i data-lucide="plus-circle" class="text-amber-400 w-6 h-6"></i>
                    Tulis Artikel Analisis Baru
                </h1>
                <p class="text-xs text-slate-400 mt-1">Buat analisis dan intelijen risiko rantai pasok terkini.</p>
            </div>
            <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-secondary flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="sg-panel sg-glass max-w-4xl mx-auto">
        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="sg-label">Judul Artikel <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Dampak Konflik Geopolitik terhadap Jalur Logistik Laut Merah" class="sg-input">
                @error('title') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="sg-label">Kategori <span class="text-red-400">*</span></label>
                    <select name="category" required class="sg-input">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="sg-label">Status Publikasi <span class="text-red-400">*</span></label>
                    <select name="status" required class="sg-input">
                        <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft (Simpan saja)</option>
                        <option value="Published" {{ old('status') == 'Published' ? 'selected' : '' }}>Published (Publikasikan)</option>
                    </select>
                    @error('status') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="sg-label">URL Gambar Thumbnail (Opsional)</label>
                <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url') }}" placeholder="https://example.com/image.jpg" class="sg-input">
                <p class="text-[11px] text-slate-400 mt-1">Atau unggah file gambar lokal di bawah ini.</p>
            </div>

            <div>
                <label class="sg-label">Unggah Gambar Thumbnail (File)</label>
                <input type="file" name="thumbnail" accept="image/*" class="sg-input border-dashed">
                @error('thumbnail') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sg-label">Ringkasan Artikel (Summary)</label>
                <textarea name="summary" rows="2" placeholder="Ringkasan singkat artikel untuk preview..." class="sg-input">{{ old('summary') }}</textarea>
                @error('summary') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sg-label">Isi Lengkap Artikel <span class="text-red-400">*</span></label>
                <textarea name="content" rows="10" required placeholder="Tuliskan analisis rinci di sini..." class="sg-input font-mono text-sm leading-relaxed">{{ old('content') }}</textarea>
                @error('content') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('articles.index') }}" class="sg-btn sg-btn-dark">Batal</a>
                <button type="submit" class="sg-btn sg-btn-primary flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Simpan Artikel
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
