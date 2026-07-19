<x-app-layout>

    @push('head')
        {{-- Bootstrap Grid only (no Bootstrap JS / reset — keeps SupplyGuard design intact) --}}
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css"
              crossorigin="anonymous">
        <style>
            /* ── News-page local overrides ───────────────────────────── */

            /* Card wrapper — all cards same height */
            .news-card-col {
                display: flex;
            }

            /* The card itself */
            .news-card {
                background: var(--sg-glass);
                border: 1px solid var(--sg-border);
                border-radius: 14px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                width: 100%;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
                text-decoration: none;
                color: inherit;
            }
            .news-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
                border-color: rgba(249, 115, 22, 0.35);
            }

            /* Image always exactly 170 px high */
            .news-card-img-wrapper {
                display: block;
                width: 100%;
                height: 170px;
                overflow: hidden;
                flex-shrink: 0;
            }
            .news-card-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
                border-radius: 0;
                border-bottom: 1px solid var(--sg-border);
                transition: transform 0.3s ease;
            }
            .news-card:hover .news-card-img {
                transform: scale(1.03);
            }

            /* Body grows to fill remaining space */
            .news-card-body {
                flex: 1;
                display: flex;
                flex-direction: column;
                padding: 14px 16px 0 16px;
            }

            /* Title: max 2 lines */
            .news-card-title {
                font-size: 13.5px;
                font-weight: 700;
                line-height: 1.45;
                color: var(--sg-text-primary);
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                margin: 6px 0 8px 0;
                flex-shrink: 0;
            }
            .news-card-title a { color: inherit; text-decoration: none; }
            .news-card-title a:hover { color: var(--accent-orange); }

            /* Description: max 3 lines */
            .news-card-desc {
                font-size: 12px;
                color: var(--sg-text-secondary);
                line-height: 1.6;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
                flex: 1;
                margin-bottom: 0;
            }

            /* Footer — always pinned to bottom */
            .news-card-footer {
                padding: 10px 16px 14px 16px;
                border-top: 1px solid rgba(255,255,255,0.05);
                margin-top: 12px;
                flex-shrink: 0;
            }

            /* Meta row: country flag + date */
            .news-meta-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 11px;
                color: var(--sg-text-muted);
                margin-bottom: 8px;
            }
            .news-flag {
                width: 20px; height: 14px;
                object-fit: cover;
                border-radius: 2px;
                border: 1px solid #e2e8f0;
                margin-right: 5px;
            }

            /* Action buttons row */
            .news-btn-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            /* Source badge */
            .news-source-badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 8px;
                border-radius: 6px;
                font-size: 10px;
                font-weight: 700;
                background: rgba(249, 115, 22, 0.1);
                color: var(--accent-orange);
                border: 1px solid rgba(249, 115, 22, 0.2);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 140px;
            }

            /* Sentiment badges */
            .senti-badge {
                display: inline-flex; align-items: center; gap: 3px;
                padding: 3px 8px; border-radius: 6px;
                font-size: 10px; font-weight: 700;
                text-transform: uppercase; letter-spacing: 0.5px;
                flex-shrink: 0;
            }
            .senti-positive { background: rgba(16,185,129,.1); color:#10b981; border:1px solid rgba(16,185,129,.2); }
            .senti-negative { background: rgba(239,68,68,.1);  color:#ef4444; border:1px solid rgba(239,68,68,.2); }
            .senti-neutral  { background: rgba(148,163,184,.1);color:#94a3b8; border:1px solid rgba(148,163,184,.2); }

            /* Bootstrap row/col gutter in dark design */
            .news-grid-row { --bs-gutter-x: 1.25rem; --bs-gutter-y: 1.25rem; }

            /* Impact score colour */
            .impact-high   { color: #ef4444; font-weight: 700; }
            .impact-medium { color: #f97316; font-weight: 700; }
            .impact-low    { color: #10b981; font-weight: 700; }

            /* Progress animation */
            .progress-container { width:100%;height:20px;background:#334155;border-radius:10px;overflow:hidden;margin:20px 0; }
            .progress-bar { height:100%;background:linear-gradient(90deg,#FF6B00,#FF8C42);transition:width .3s ease; }
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
            }
            .sg-form-input:focus {
                border-color: rgba(255,107,0,0.5);
                box-shadow: 0 0 0 3px rgba(255,107,0,0.08);
            }
        </style>
    @endpush

    {{-- Header --}}
    <x-crud-header
        title="News & Events"
        description="Supply chain news with AI sentiment analysis, synced from GNews API."
        icon="newspaper"
        iconColor="text-purple-400"
    >

        <button onclick="startImport('news')" class="sg-btn sg-btn-sm sg-btn-outline-orange">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            Sync News API
        </button>
        <a href="{{ route('news.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Article
        </a>
    </x-crud-header>

    @if(session('success'))
        <div class="sg-flash sg-flash-success">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <x-crud-toolbar
        searchPlaceholder="Search news articles..."
        searchValue="{{ request('search') }}"
        :showRefresh="true"
        :showExport="false"
        :showImport="false"
        :showAdd="false"
    >
        <select name="country" onchange="this.form.submit()">
            <option value="">All Countries</option>
            @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('country') == $c->id ? 'selected' : '' }}>
                    {{ $c->country_name }}
                </option>
            @endforeach
        </select>
        <select name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <option value="General" {{ request('category') === 'General' ? 'selected' : '' }}>General</option>
            <option value="Disaster" {{ request('category') === 'Disaster' ? 'selected' : '' }}>Disaster</option>
            <option value="Political" {{ request('category') === 'Political' ? 'selected' : '' }}>Political</option>
            <option value="Economic" {{ request('category') === 'Economic' ? 'selected' : '' }}>Economic</option>
            <option value="Security" {{ request('category') === 'Security' ? 'selected' : '' }}>Security</option>
            <option value="Logistics" {{ request('category') === 'Logistics' ? 'selected' : '' }}>Logistics</option>
        </select>
        <select name="sentiment" onchange="this.form.submit()">
            <option value="">All Sentiments</option>
            <option value="Positive" {{ request('sentiment') === 'Positive' ? 'selected' : '' }}>Positive</option>
            <option value="Negative" {{ request('sentiment') === 'Negative' ? 'selected' : '' }}>Negative</option>
            <option value="Neutral" {{ request('sentiment') === 'Neutral' ? 'selected' : '' }}>Neutral</option>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Active Only</option>
            <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
        </select>
    </x-crud-toolbar>

    {{-- Main card --}}
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="newspaper" class="w-5 h-5 text-purple-400"></i>
                <h2 class="sg-data-title">Latest Articles
                    <span class="sg-count-badge">{{ $news->total() }} articles</span>
                </h2>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-blue-400"></i>
                Source: GNews API & Sentiment Engine
            </div>
        </div>

        {{-- Bootstrap 4-col grid --}}
        <div class="container-fluid p-0">
            <div class="row news-grid-row">
                @forelse($news as $item)
                    <div class="col-lg-3 col-md-6 col-12 news-card-col">
                        <div class="news-card">

                            {{-- Image Link --}}
                            @if($item->url)
                                <a href="{{ $item->url }}" target="_blank" rel="noopener" class="news-card-img-wrapper">
                                    <img
                                        src="{{ $item->image ?: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80' }}"
                                        class="news-card-img"
                                        alt=""
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80'"
                                    >
                                </a>
                            @else
                                <a href="{{ route('news.show', $item->id) }}" class="news-card-img-wrapper">
                                    <img
                                        src="{{ $item->image ?: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80' }}"
                                        class="news-card-img"
                                        alt=""
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80'"
                                    >
                                </a>
                            @endif

                            {{-- Body --}}
                            <div class="news-card-body">
                                {{-- Source + Sentiment row --}}
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:6px">
                                    <span class="news-source-badge" title="{{ $item->source }}">
                                        {{ Str::limit($item->source ?? 'Unknown', 22) }}
                                    </span>
                                    @if($item->sentiment === 'Positive')
                                        <span class="senti-badge senti-positive">
                                            <i data-lucide="trending-up" class="w-3 h-3"></i> Positive
                                        </span>
                                    @elseif($item->sentiment === 'Negative')
                                        <span class="senti-badge senti-negative">
                                            <i data-lucide="trending-down" class="w-3 h-3"></i> Negative
                                        </span>
                                    @else
                                        <span class="senti-badge senti-neutral">
                                            <i data-lucide="minus" class="w-3 h-3"></i> Neutral
                                        </span>
                                    @endif
                                </div>

                                {{-- Title Link --}}
                                <h3 class="news-card-title">
                                    @if($item->url)
                                        <a href="{{ $item->url }}" target="_blank" rel="noopener">
                                            {{ $item->title }}
                                        </a>
                                    @else
                                        <a href="{{ route('news.show', $item->id) }}">
                                            {{ $item->title }}
                                        </a>
                                    @endif
                                </h3>

                                {{-- Description --}}
                                <p class="news-card-desc">{{ $item->summary }}</p>
                            </div>

                            {{-- Footer --}}
                            <div class="news-card-footer">
                                {{-- Country + Date --}}
                                <div class="news-meta-row">
                                    <div style="display:flex;align-items:center;gap:4px">
                                        @if($item->country && $item->country->flag)
                                            <img src="{{ $item->country->flag }}"
                                                 class="news-flag"
                                                 alt="{{ $item->country->country_name }}">
                                        @endif
                                        <span style="font-weight:600;font-size:12px;color:#475569">
                                            {{ $item->country->country_name ?? 'Global' }}
                                        </span>
                                    </div>
                                    <span>
                                        {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('M d, Y') : '—' }}
                                    </span>
                                </div>

                                {{-- Impact + Action buttons --}}
                                <div class="news-btn-row">
                                    <div style="display:flex;align-items:center;gap:4px">
                                        <span class="text-xs text-slate-400">Risk:</span>
                                        <span class="{{ ($item->impact_score ?? 0) >= 70 ? 'impact-high' : (($item->impact_score ?? 0) >= 40 ? 'impact-medium' : 'impact-low') }} text-sm">
                                            {{ $item->impact_score ?? 0 }}%
                                        </span>
                                    </div>
                                    <div style="display:flex;gap:5px">
                                        @if($item->trashed())
                                            <form action="{{ route('news.restore', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore News">
                                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            @if($item->url)
                                                <a href="{{ $item->url }}"
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="sg-btn sg-btn-xs sg-btn-secondary"
                                                   title="Open Original News Article">
                                                    <i data-lucide="external-link" class="w-3 h-3"></i> View
                                                </a>
                                            @else
                                                <a href="{{ route('news.show', $item->id) }}"
                                                   class="sg-btn sg-btn-xs sg-btn-secondary"
                                                   title="View News Locally">
                                                    <i data-lucide="eye" class="w-3 h-3"></i> View
                                                </a>
                                            @endif
                                            <a href="{{ route('news.edit', $item->id) }}"
                                               class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                            <form action="{{ route('news.destroy', $item->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete this article?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger">Del</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="sg-empty-state">
                            <div class="sg-empty-icon">
                                <i data-lucide="newspaper" class="w-8 h-8"></i>
                            </div>
                            <h3>No News Articles</h3>
                            <p>
                                Click "Sync News API" to fetch articles from GNews, or add manually.
                            </p>
                            <button onclick="startImport('news')" class="sg-btn sg-btn-sm sg-btn-teal">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                Sync Now
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if(method_exists($news, 'hasPages') && $news->hasPages())
            <div class="sg-pagination" style="margin-top:1.5rem">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $news->firstItem() }}</strong>–<strong>{{ $news->lastItem() }}</strong>
                    of <strong>{{ $news->total() }}</strong> articles
                </div>
                <div class="sg-pagination-nav">
                    {{ $news->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    <script>
        function startImport(service) {
            Swal.fire({
                title: 'Syncing ' + service + ' data...',
                html: '<div class="progress-container"><div class="progress-bar" id="import-progress-bar" style="width:0%"></div></div><p id="import-status">Initializing...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1B2433',
                color: '#F8FAFC',
                didOpen: () => {
                    fetch('/' + service + '/sync/api', { method: 'GET' })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                pollProgress(service);
                            } else {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Import Failed',
                                    text: data.message || 'Failed to start import',
                                    confirmButtonColor: '#FF6B00',
                                    background: '#1B2433',
                                    color: '#F8FAFC'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: err.message,
                                confirmButtonColor: '#FF6B00',
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                        });
                }
            });
        }

        function pollProgress(service) {
            const pollInterval = setInterval(async () => {
                try {
                    const res  = await fetch('/api/import/progress/' + service);
                    const json = await res.json();

                    const pBar     = document.getElementById('import-progress-bar');
                    const statusEl = document.getElementById('import-status');

                    if (pBar)     pBar.style.width = json.percentage + '%';
                    if (statusEl) statusEl.textContent = `Progress: ${json.percentage}% | Processed: ${json.processed}/${json.total}`;

                    if (json.status === 'completed' || json.percentage >= 100) {
                        clearInterval(pollInterval);
                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Sync Completed Successfully',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                            window.location.reload();
                        }, 500);
                    }

                    if (json.status === 'failed') {
                        clearInterval(pollInterval);
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Sync Failed',
                            text: 'Latest data unavailable. Displaying cached articles.',
                            confirmButtonColor: '#FF6B00',
                            background: '#1B2433',
                            color: '#F8FAFC'
                        });
                    }
                } catch (err) {
                    clearInterval(pollInterval);
                }
            }, 2000);
        }
    </script>

</x-app-layout>