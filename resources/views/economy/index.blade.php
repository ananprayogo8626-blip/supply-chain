<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">Economic Data</h1>
                <p class="sg-page-desc">Macroeconomic indicators sourced from World Bank API.</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('economy.import') }}"
                   onclick="return confirm('Import economy data for all countries from World Bank API? This may take a moment.')"
                   class="sg-btn sg-btn-outline-orange" id="btn-import-economy">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import World Bank API
                </a>
                <a href="{{ route('economy.create') }}" class="sg-btn sg-btn-gradient" id="btn-add-economy">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Data
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
                <svg fill="none" stroke="#4f46e5" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <h2 class="sg-data-title">Macroeconomic Indicators
                    <span class="sg-count-badge">{{ $economy->count() }} records</span>
                </h2>
            </div>
            <div style="font-size:12px;color:#64748b">Source: World Bank API (api.worldbank.org)</div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="economy-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('economy.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-right">
                            <a href="{{ route('economy.index', array_merge(request()->query(), ['sort' => request('sort') === 'gdp' ? 'gdp_desc' : 'gdp'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:flex-end">
                                GDP (USD)
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('economy.index', array_merge(request()->query(), ['sort' => request('sort') === 'gdp_growth' ? 'gdp_growth_desc' : 'gdp_growth'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                GDP Growth
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('economy.index', array_merge(request()->query(), ['sort' => request('sort') === 'inflation' ? 'inflation_desc' : 'inflation'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Inflation
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-right">Exports</th>
                        <th class="sg-td-right">Imports</th>
                        <th class="sg-td-center">
                            <a href="{{ route('economy.index', array_merge(request()->query(), ['sort' => request('sort') === 'data_year' ? 'data_year_desc' : 'data_year'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Year
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center" style="width:170px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($economy as $item)
                        <tr>
                            <td class="sg-td-num">{{ $loop->iteration }}</td>
                            <td>
                                <div class="sg-flag-cell">
                                    @if($item->country && $item->country->flag)
                                        <img src="{{ $item->country->flag }}" alt="" loading="lazy"
                                            onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                            style="width:32px;height:22px;object-fit:cover;border-radius:3px;border:1px solid #e2e8f0">
                                    @endif
                                    <span class="sg-country-name">{{ $item->country->country_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="sg-td-right sg-td-bold">
                                @php
                                    $gdp = (float)($item->gdp ?? 0);
                                    if ($gdp >= 1e12) $gdpStr = '$' . number_format($gdp/1e12, 2) . 'T';
                                    elseif ($gdp >= 1e9) $gdpStr = '$' . number_format($gdp/1e9, 2) . 'B';
                                    elseif ($gdp >= 1e6) $gdpStr = '$' . number_format($gdp/1e6, 2) . 'M';
                                    else $gdpStr = '$' . number_format($gdp, 2);
                                @endphp
                                {{ $gdpStr }}
                            </td>
                            <td class="sg-td-center">
                                @php $growth = (float)($item->gdp_growth ?? 0); @endphp
                                @if($item->gdp_growth !== null)
                                    <span style="font-weight:700;color:{{ $growth >= 0 ? '#16a34a' : '#dc2626' }}">
                                        {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @php $inf = (float)($item->inflation ?? 0); @endphp
                                <span style="font-weight:600;color:{{ $inf > 10 ? '#dc2626' : ($inf > 5 ? '#ea580c' : '#64748b') }}">
                                    {{ $item->inflation !== null ? number_format($inf, 1).'%' : '—' }}
                                </span>
                            </td>
                            <td class="sg-td-right" style="font-size:12px;color:#64748b">
                                @php $exp = (float)($item->exports ?? 0); @endphp
                                {{ $exp >= 1e9 ? '$'.number_format($exp/1e9,1).'B' : '$'.number_format($exp) }}
                            </td>
                            <td class="sg-td-right" style="font-size:12px;color:#64748b">
                                @php $imp = (float)($item->imports ?? 0); @endphp
                                {{ $imp >= 1e9 ? '$'.number_format($imp/1e9,1).'B' : '$'.number_format($imp) }}
                            </td>
                            <td class="sg-td-center">
                                <span class="sg-code-badge">{{ $item->data_year ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    <a href="{{ route('economy.sync', $item->country_id) }}"
                                       onclick="return confirm('Sync latest data from World Bank API?')"
                                       class="sg-btn sg-btn-xs sg-btn-green" id="sync-eco-{{ $item->id }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Sync
                                    </a>
                                    <a href="{{ route('economy.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-eco-{{ $item->id }}">Edit</a>
                                    <form action="{{ route('economy.destroy', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this record?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-eco-{{ $item->id }}">Del</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <p>No economic data. Go to Countries and click Economy Sync for each country.</p>
                                <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-primary">Go to Countries</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>