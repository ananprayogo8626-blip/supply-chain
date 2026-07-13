<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">Currency Exchange</h1>
                <p class="sg-page-desc">Live exchange rates synced from open.er-api.com (no API key required).</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('currency.import') }}"
                   onclick="return confirm('Import exchange rate data for all countries from ExchangeRate API? This may take a moment.')"
                   class="sg-btn sg-btn-outline-orange" id="btn-import-currency">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Exchange Rate API
                </a>
                <a href="{{ route('currency.create') }}" class="sg-btn sg-btn-gradient" id="btn-add-currency">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Record
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
                <svg fill="none" stroke="#d97706" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="sg-data-title">Exchange Rate Records
                    <span class="sg-count-badge">{{ method_exists($currency,'total') ? $currency->total() : $currency->count() }} entries</span>
                </h2>
            </div>
            <div style="font-size:12px;color:#64748b">Source: ExchangeRate API (open.er-api.com)</div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="currency-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('currency.index', array_merge(request()->query(), ['sort' => request('sort') === 'currency_code' ? 'currency_code_desc' : 'currency_code'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Currency
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('currency.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-right">Buy</th>
                        <th class="sg-td-right">Sell</th>
                        <th class="sg-td-right">
                            <a href="{{ route('currency.index', array_merge(request()->query(), ['sort' => request('sort') === 'exchange_rate' ? 'exchange_rate_desc' : 'exchange_rate'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:flex-end">
                                Rate
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('currency.index', array_merge(request()->query(), ['sort' => request('sort') === 'change_percentage' ? 'change_percentage_desc' : 'change_percentage'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Change
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">Last Update</th>
                        <th class="sg-td-center" style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currency as $item)
                        <tr>
                            <td class="sg-td-num">{{ $loop->iteration }}</td>
                            <td>
                                <span class="sg-code-badge" title="{{ $item->currency_name ?? $item->currency_code }}">{{ $item->currency_code }} ({{ $item->symbol }})</span>
                                <span style="font-size:11px;color:#94a3b8;margin-left:4px">{{ $item->currency_name ?? '' }}</span>
                            </td>
                            <td>
                                <div class="sg-flag-cell">
                                    @if($item->country && $item->country->flag)
                                        <img src="{{ $item->country->flag }}" alt="" loading="lazy"
                                            onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                            style="width:28px;height:19px;object-fit:cover;border-radius:3px;border:1px solid #e2e8f0">
                                    @endif
                                    <span class="sg-country-name">{{ $item->country->country_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="sg-td-right sg-td-bold" style="color:#0f766e">
                                {{ number_format((float)$item->buy, 4) }}
                            </td>
                            <td class="sg-td-right sg-td-bold" style="color:#b91c1c">
                                {{ number_format((float)$item->sell, 4) }}
                            </td>
                            <td class="sg-td-right sg-td-bold" style="font-size:15px;color:#4f46e5">
                                {{ number_format((float)$item->exchange_rate, 4) }}
                            </td>
                            <td class="sg-td-center">
                                @php $chg = (float)($item->change_percentage ?? 0); @endphp
                                @if(abs($chg) < 0.001)
                                    <span style="color:#64748b;font-size:13px">→ 0.00%</span>
                                @elseif($chg > 0)
                                    <span style="color:#16a34a;font-weight:700;font-size:13px">↗ +{{ number_format($chg, 2) }}%</span>
                                @else
                                    <span style="color:#dc2626;font-weight:700;font-size:13px">↘ {{ number_format($chg, 2) }}%</span>
                                @endif
                            </td>
                            <td class="sg-td-center" style="font-size:12px;color:#94a3b8;white-space:nowrap">
                                {{ $item->last_updated ? \Carbon\Carbon::parse($item->last_updated)->format('M d, H:i') : '—' }}
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($item->country)
                                        <a href="{{ route('currency.sync', $item->country->id) }}"
                                           class="sg-btn sg-btn-xs sg-btn-indigo" id="sync-curr-{{ $item->id }}">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Sync
                                        </a>
                                    @endif
                                    <a href="{{ route('currency.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-curr-{{ $item->id }}">Edit</a>
                                    <form action="{{ route('currency.destroy', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this record?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-curr-{{ $item->id }}">Del</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p>No currency data. Go to Countries and click Sync Currency for each country.</p>
                                <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-primary">Go to Countries</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>