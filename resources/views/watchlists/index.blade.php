<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">Watchlist</h1>
                <p class="sg-page-desc">Manage entities and countries under active supply chain surveillance.</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('watchlists.create') }}" class="sg-btn sg-btn-primary" id="btn-add-watchlist">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Watchlist
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="sg-flash sg-flash-success mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <svg fill="none" stroke="#4f46e5" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <h2 class="sg-data-title">Watchlist Registry
                    <span class="sg-count-badge">{{ $watchlists->count() }} watched</span>
                </h2>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('watchlists.index') }}" id="watchlist-filter-form">
            <div class="sg-filter-bar" style="margin-bottom:15px;padding: 0 20px 15px 20px;border-bottom:1px solid #f1f5f9">
                <div class="sg-filter-wrap">
                    <svg class="sg-filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" id="watchlist-search" class="sg-filter-input" placeholder="Search entity, industry, country..."
                        value="{{ request('search') }}" autocomplete="off">
                </div>
                <select name="priority" id="watchlist-priority" class="sg-filter-select" onchange="this.form.submit()">
                    <option value="">All Priorities</option>
                    <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>P1 — Critical Threat</option>
                    <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>P2 — High Threat</option>
                    <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>P3 — Medium Risk</option>
                    <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>P4 — Low Risk</option>
                    <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>P5 — Minimal Concern</option>
                </select>
                <select name="status" id="watchlist-status" class="sg-filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Monitoring" {{ request('status') === 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                    <option value="Critical" {{ request('status') === 'Critical' ? 'selected' : '' }}>Critical</option>
                    <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
                <button type="submit" class="sg-btn sg-btn-outline sg-btn-sm">Apply</button>
                @if(request()->hasAny(['search','priority','status']))
                    <a href="{{ route('watchlists.index') }}" class="sg-btn sg-btn-sm" style="background:#f1f5f9;color:#475569;border-color:#e2e8f0;margin-left:5px">Clear</a>
                @endif
            </div>
        </form>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="watchlists-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('watchlists.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('watchlists.index', array_merge(request()->query(), ['sort' => request('sort') === 'company' ? 'company_desc' : 'company'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Entity / Company
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('watchlists.index', array_merge(request()->query(), ['sort' => request('sort') === 'priority' ? 'priority_desc' : 'priority'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Priority
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">Risk Level</th>
                        <th>Last Weather</th>
                        <th>Last News Headline</th>
                        <th class="sg-td-center" style="width:130px">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($watchlists as $watchlist)
                        <tr>
                            <td class="sg-td-num">{{ $loop->iteration }}</td>
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
                                <div class="sg-action-group">
                                    <a href="{{ route('watchlists.edit', $watchlist->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-watchlist-{{ $watchlist->id }}">Edit</a>
                                    <form action="{{ route('watchlists.destroy', $watchlist->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Remove this entity from watchlist?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-watchlist-{{ $watchlist->id }}">Del</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="sg-empty" style="padding: 60px 20px;">
                                <div class="sg-empty-icon" style="margin-bottom: 16px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;color:var(--sg-text-muted);margin:0 auto"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                </div>
                                <h3 style="font-size:16px;font-weight:700;color:var(--sg-text-primary);margin:0 0 8px 0">Watchlist is Empty</h3>
                                <p style="font-size:13px;color:var(--sg-text-secondary);margin:0 0 16px 0;max-width:320px;margin-left:auto;margin-right:auto;">Start monitoring high-risk entities by adding them to your watchlist for active surveillance.</p>
                                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                                    <a href="{{ route('watchlists.create') }}" class="sg-btn sg-btn-primary" style="display:inline-flex;align-items:center;gap:6px;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add Entity
                                    </a>
                                    <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-outline" style="display:inline-flex;align-items:center;gap:6px;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Browse Countries
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
    (function(){
        const inp = document.getElementById('watchlist-search');
        if (!inp) return;
        let timer;
        inp.addEventListener('input', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                document.getElementById('watchlist-filter-form').submit();
            }, 500);
        });
    })();
    </script>

</x-app-layout>