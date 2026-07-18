<x-app-layout>
    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($news->country && $news->country->flag)
                        <img src="{{ $news->country->flag }}" alt="{{ $news->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:60px; height:40px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">Edit Article</h1>
                        <p class="sg-crud-description">
                            {{ $news->country->country_name ?? 'Unknown' }} — #{{ $news->id }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('news.show', $news->id) }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    View
                </a>
                <a href="{{ route('news.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="sg-flash sg-flash-error" style="margin-bottom:20px;">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="edit" class="w-5 h-5 text-orange-400"></i>
                <h2 class="sg-data-title">Edit Article Information</h2>
            </div>
        </div>

        <form action="{{ route('news.update', $news->id) }}" method="POST" style="padding:24px;">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Country -->
                <div>
                    <label class="sg-form-label">Country <span style="color:var(--sg-danger);">*</span></label>
                    <select name="country_id" class="sg-form-input" required>
                        <option value="">-- Select Country --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ (old('country_id', $news->country_id) == $country->id) ? 'selected' : '' }}>
                                {{ $country->country_name }} ({{ $country->country_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label class="sg-form-label">Source <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="source" class="sg-form-input"
                           value="{{ old('source', $news->source) }}"
                           placeholder="e.g. Reuters, Bloomberg" required>
                </div>

                <!-- Title (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Title <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="title" class="sg-form-input"
                           value="{{ old('title', $news->title) }}"
                           placeholder="Article headline..." required>
                </div>

                <!-- Category -->
                <div>
                    <label class="sg-form-label">Category</label>
                    <select name="category" class="sg-form-input">
                        <option value="">-- Select Category --</option>
                        @foreach(['Supply Chain Disruption','Port Congestion','Trade Policy','Economic Impact','Weather Advisory','Geopolitical Risk','Infrastructure Development','Technology Integration','Logistics Innovation','Sustainability Initiative'] as $cat)
                            <option value="{{ $cat }}"
                                {{ (old('category', $news->category) === $cat) ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Impact Score -->
                <div>
                    <label class="sg-form-label">Impact Score (0–100)</label>
                    <input type="number" name="impact_score" class="sg-form-input"
                           value="{{ old('impact_score', $news->impact_score) }}"
                           min="0" max="100">
                </div>

                <!-- URL (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Article URL</label>
                    <input type="url" name="url" class="sg-form-input"
                           value="{{ old('url', $news->url) }}"
                           placeholder="https://...">
                </div>

                <!-- Image URL (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Image URL</label>
                    <input type="url" name="image" class="sg-form-input"
                           value="{{ old('image', $news->image) }}"
                           placeholder="https://images.unsplash.com/...">
                    @if($news->image)
                        <div style="margin-top:10px;">
                            <img src="{{ $news->image }}" alt="Preview"
                                 style="height:120px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);"
                                 onerror="this.style.display='none'">
                        </div>
                    @endif
                </div>

                <!-- Published At -->
                <div>
                    <label class="sg-form-label">Published At</label>
                    <input type="datetime-local" name="published_at" class="sg-form-input"
                           value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                </div>

                <!-- Sentiment -->
                <div>
                    <label class="sg-form-label">Sentiment</label>
                    <select name="sentiment" class="sg-form-input">
                        @foreach(['Neutral','Positive','Negative'] as $s)
                            <option value="{{ $s }}"
                                {{ (old('sentiment', $news->sentiment) === $s) ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Summary (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Summary / Description</label>
                    <textarea name="summary" rows="5" class="sg-form-input"
                              placeholder="Brief description of the article...">{{ old('summary', $news->summary) }}</textarea>
                </div>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update Article
                </button>
                <a href="{{ route('news.index') }}" class="sg-btn sg-btn-secondary">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Cancel
                </a>
                <form action="{{ route('news.destroy', $news->id) }}" method="POST"
                      style="margin:0;"
                      onsubmit="return confirm('Delete this article? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sg-btn sg-btn-danger">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>

    <style>
        .sg-form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--sg-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
        .sg-form-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--sg-border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            color: var(--sg-text-primary);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            font-family: inherit;
            resize: vertical;
        }
        .sg-form-input:focus {
            border-color: rgba(255,107,0,0.5);
            box-shadow: 0 0 0 3px rgba(255,107,0,0.08);
        }
        .sg-form-input option {
            background: #1B2433;
            color: var(--sg-text-primary);
        }
    </style>
</x-app-layout>