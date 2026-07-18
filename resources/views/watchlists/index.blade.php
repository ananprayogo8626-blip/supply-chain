<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Watchlist"
        description="Manage entities and countries under active supply chain surveillance."
        icon="star"
        iconColor="text-yellow-400"
    >
        <a href="#import-section" onclick="document.getElementById('import-section').scrollIntoView({behavior: 'smooth'})" class="sg-btn sg-btn-sm sg-btn-teal">
            <i data-lucide="upload" class="w-4 h-4"></i>
            Import CSV
        </a>
        <a href="{{ route('watchlists.export-csv') }}" class="sg-btn sg-btn-sm sg-btn-teal">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export CSV
        </a>
        <a href="{{ route('watchlists.export-pdf') }}" class="sg-btn sg-btn-sm sg-btn-secondary" target="_blank">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Export PDF
        </a>
        <a href="{{ route('watchlists.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Watchlist
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

    <!-- CSV Import Card -->
    <div id="import-section" class="sg-data-card" style="margin-bottom:20px; padding:16px;">
        <h3 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 10px 0;">Import Watchlist from CSV</h3>
        <form action="{{ route('watchlists.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            @csrf
            <input type="file" name="file" accept=".csv" required class="sg-form-input" style="width:auto; flex:1; min-width:200px;">
            <button type="submit" class="sg-btn sg-btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import CSV
            </button>
        </form>
    </div>

    <!-- Glass Card with Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="star" class="w-5 h-5 text-yellow-400"></i>
                <h2 class="sg-data-title">Watchlist Registry
                    <span class="sg-count-badge">{{ $watchlists->total() }} watched</span>
                </h2>
            </div>
        </div>

        <!-- Standardized Toolbar -->
        <x-crud-toolbar 
            searchPlaceholder="Search entity, industry, country..."
            searchValue="{{ request('search') }}"
            :showRefresh="true"
            :showExport="false"
            :showImport="false"
            :showAdd="false"
        >
            <select name="priority" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>P1 — Critical Threat</option>
                <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>P2 — High Threat</option>
                <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>P3 — Medium Risk</option>
                <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>P4 — Low Risk</option>
                <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>P5 — Minimal Concern</option>
            </select>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Monitoring" {{ request('status') === 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                <option value="Critical" {{ request('status') === 'Critical' ? 'selected' : '' }}>Critical</option>
                <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
            </select>
        </x-crud-toolbar>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="watchlists-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>Country</th>
                        <th>Entity / Company</th>
                        <th class="sg-td-center">Priority</th>
                        <th class="sg-td-center">Risk Level</th>
                        <th>Last Weather</th>
                        <th>Last News Headline</th>
                        <th class="sg-td-center">Status</th>
                        <th class="sg-td-center" style="width:130px">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($watchlists as $watchlist)
                        <tr>
                            <td class="sg-td-num">{{ $watchlists->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="sg-flag-cell">
                                    @if($watchlist->country && $watchlist->country->flag)
                                        <img src="{{ $watchlist->country->flag }}" alt="" loading="lazy"
                                            onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                            style="width:28px;height:19px;object-fit:cover;border-radius:3px;border:1px solid #e2e8f0">
                                    @endif
                                    <span class="sg-country-name" style="font-weight:700">{{ $watchlist->country->country_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600;color:#0f172a">{{ $watchlist->company_name }}</div>
                                <div style="font-size:11px;color:#64748b">{{ $watchlist->industry ?? '—' }}</div>
                            </td>
                            <td class="sg-td-center">
                                @php $priority = $watchlist->priority; @endphp
                                <span class="sg-watchlist-priority sg-priority-{{ $priority }}">
                                    @if($priority === 1) P1 — Critical
                                    @elseif($priority === 2) P2 — High
                                    @elseif($priority === 3) P3 — Medium
                                    @elseif($priority === 4) P4 — Low
                                    @else P5 — Minimal
                                    @endif
                                </span>
                            </td>
                            <td class="sg-td-center">
                                @php
                                    $lvl = $watchlist->country->riskScore->risk_level ?? 'Low';
                                    $cls = strtolower($lvl);
                                @endphp
                                <span class="sg-badge {{ $cls }}">{{ $lvl }}</span>
                            </td>
                            <td>
                                @php
                                    $w = $watchlist->country->weatherData;
                                @endphp
                                @if($w)
                                    <span style="font-weight:600;color:#0f766e">{{ $w->temperature }}°C</span>,
                                    <span style="color:#64748b;font-size:12px">{{ $w->weather_condition }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $n = $watchlist->country->news->sortByDesc('published_at')->first();
                                @endphp
                                @if($n)
                                    <div style="font-size:12.5px;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#334155" title="{{ $n->title }}">
                                        {{ $n->title }}
                                    </div>
                                @else
                                    <span class="text-muted">No recent news</span>
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @if($watchlist->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    <span class="sg-badge low">{{ $watchlist->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($watchlist->trashed())
                                        <form action="{{ route('watchlists.restore', $watchlist->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Watchlist Item">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('watchlists.edit', $watchlist->id) }}" class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                        <form action="{{ route('watchlists.destroy', $watchlist->id) }}" method="POST" class="sg-delete-form" onsubmit="return confirm('Remove this entity from watchlist?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger">Del</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:40px;">
                                <div class="sg-empty-state">
                                    <div class="sg-empty-icon">
                                        <i data-lucide="star" class="w-8 h-8"></i>
                                    </div>
                                    <h3>Watchlist is Empty</h3>
                                    <p>Start monitoring high-risk entities by adding them to your watchlist for active surveillance.</p>
                                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                                        <a href="{{ route('watchlists.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            Add Entity
                                        </a>
                                        <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                                            <i data-lucide="globe" class="w-4 h-4"></i>
                                            Browse Countries
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($watchlists, 'hasPages') && $watchlists->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $watchlists->firstItem() }}</strong>–<strong>{{ $watchlists->lastItem() }}</strong> of <strong>{{ $watchlists->total() }}</strong> entries
                </div>
                <div class="sg-pagination-nav">
                    {{ $watchlists->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    <style>
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

    <script>
    // Live search with debounce
    (function(){
        const inp = document.querySelector('.sg-crud-search-input');
        if (!inp) return;
        let timer;
        inp.addEventListener('input', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                inp.closest('form').submit();
            }, 500);
        });
    })();
    </script>
</x-app-layout>