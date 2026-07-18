<x-app-layout>
    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; background:rgba(168,85,247,0.15); border:1px solid rgba(168,85,247,0.3); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-purple-400"></i>
                    </div>
                    <div>
                        <h1 class="sg-crud-title">Add News Article</h1>
                        <p class="sg-crud-description">Manually add a supply chain news article to the database.</p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('news.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
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
                <i data-lucide="newspaper" class="w-5 h-5 text-purple-400"></i>
                <h2 class="sg-data-title">Article Information</h2>
            </div>
        </div>

        <form action="{{ route('news.store') }}" method="POST" style="padding:24px;">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Country -->
                <div>
                    <label class="sg-form-label">Country <span style="color:var(--sg-danger);">*</span></label>
                    <select name="country_id" class="sg-form-input" required>
                        <option value="">-- Select Country --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->country_name }} ({{ $country->country_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label class="sg-form-label">Source <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="source" class="sg-form-input"
                           value="{{ old('source') }}"
                           placeholder="e.g. Reuters, Bloomberg" required>
                </div>

                <!-- Title (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Title <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="title" class="sg-form-input"
                           value="{{ old('title') }}"
                           placeholder="Article headline..." required>
                </div>

                <!-- Category -->
                <div>
                    <label class="sg-form-label">Category</label>
                    <select name="category" class="sg-form-input">
                        <option value="">-- Select Category --</option>
                        @foreach(['Supply Chain Disruption','Port Congestion','Trade Policy','Economic Impact','Weather Advisory','Geopolitical Risk','Infrastructure Development','Technology Integration','Logistics Innovation','Sustainability Initiative'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Impact Score -->
                <div>
                    <label class="sg-form-label">Impact Score (0–100)</label>
                    <input type="number" name="impact_score" class="sg-form-input"
                           value="{{ old('impact_score', 50) }}"
                           min="0" max="100">
                </div>

                <!-- URL (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Article URL</label>
                    <input type="url" name="url" class="sg-form-input"
                           value="{{ old('url') }}"
                           placeholder="https://...">
                </div>

                <!-- Image URL (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Image URL</label>
                    <input type="url" name="image" class="sg-form-input"
                           value="{{ old('image') }}"
                           placeholder="https://images.unsplash.com/...">
                </div>

                <!-- Published At -->
                <div>
                    <label class="sg-form-label">Published At</label>
                    <input type="datetime-local" name="published_at" class="sg-form-input"
                           value="{{ old('published_at') }}">
                </div>

                <!-- Sentiment -->
                <div>
                    <label class="sg-form-label">Sentiment</label>
                    <select name="sentiment" class="sg-form-input">
                        <option value="Neutral" {{ old('sentiment', 'Neutral') === 'Neutral' ? 'selected' : '' }}>Neutral</option>
                        <option value="Positive" {{ old('sentiment') === 'Positive' ? 'selected' : '' }}>Positive</option>
                        <option value="Negative" {{ old('sentiment') === 'Negative' ? 'selected' : '' }}>Negative</option>
                    </select>
                </div>

                <!-- Summary (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Summary / Description</label>
                    <textarea name="summary" rows="5" class="sg-form-input"
                              placeholder="Brief description of the article...">{{ old('summary') }}</textarea>
                </div>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Article
                </button>
                <a href="{{ route('news.index') }}" class="sg-btn sg-btn-secondary">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Cancel
                </a>
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