<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Currency Exchange"
        description="Live exchange rates synced from open.er-api.com (no API key required)."
        icon="banknote"
        iconColor="text-amber-500"
    >

        <button onclick="startImport('currency')" class="sg-btn sg-btn-sm sg-btn-outline-orange">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            Sync Exchange API
        </button>
        <a href="{{ route('currency.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Record
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

    @if($showWarning ?? false)
        <div class="sg-flash sg-flash-error">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            Latest exchange rate data unavailable. Displaying cached data.
        </div>
    @endif


    <!-- Standardized Toolbar -->
    <x-crud-toolbar 
        searchPlaceholder="Search currency records..."
        searchValue="{{ request('search') }}"
        :showRefresh="true"
        :showExport="false"
        :showImport="false"
        :showAdd="false"
    >
        <select name="status" onchange="this.form.submit()">
            <option value="">Active</option>
            <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
        </select>
    </x-crud-toolbar>

    <!-- Glass Card with Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="banknote" class="w-5 h-5 text-amber-500"></i>
                <h2 class="sg-data-title">Exchange Rate Records
                    <span class="sg-count-badge">{{ $currency->total() }} entries</span>
                </h2>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-blue-400"></i>
                Source: ExchangeRate API
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="currency-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('currency.index', ['sort' => 'currency_code', 'order' => request('order') === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" style="color:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                                Currency
                                @if(request('sort') === 'currency_code')
                                    <i data-lucide="{{ request('order') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('currency.index', ['sort' => 'country', 'order' => request('order') === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" style="color:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                                Country
                                @if(request('sort') === 'country')
                                    <i data-lucide="{{ request('order') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"></i>
                                @endif
                            </a>
                        </th>
                        <th class="sg-td-right">Buy</th>
                        <th class="sg-td-right">Sell</th>
                        <th class="sg-td-right">
                            <a href="{{ route('currency.index', ['sort' => 'exchange_rate', 'order' => request('order') === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" style="color:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:4px;justify-content:flex-end;width:100%">
                                Rate
                                @if(request('sort') === 'exchange_rate')
                                    <i data-lucide="{{ request('order') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"></i>
                                @endif
                            </a>
                        </th>
                        <th class="sg-td-center">Change</th>
                        <th class="sg-td-center">Status</th>
                        <th class="sg-td-center">
                            <a href="{{ route('currency.index', ['sort' => 'last_updated', 'order' => request('order') === 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" style="color:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:4px;justify-content:center;width:100%">
                                Last Update
                                @if(request('sort') === 'last_updated' || !request('sort'))
                                    <i data-lucide="{{ request('order', 'desc') === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3"></i>
                                @endif
                            </a>
                        </th>
                        <th class="sg-td-center" style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currency as $item)
                        <tr>
                            <td class="sg-td-num">{{ $currency->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="sg-code-badge" title="{{ $item->currency_name ?? $item->currency_code }}">{{ $item->currency_code }}</span>
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
                                    <span class="text-slate-400 text-sm">→ 0.00%</span>
                                @elseif($chg > 0)
                                    <span class="text-green-400 font-bold text-sm flex items-center justify-center gap-1">
                                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                                        +{{ number_format($chg, 2) }}%
                                    </span>
                                @else
                                    <span class="text-red-400 font-bold text-sm flex items-center justify-center gap-1">
                                        <i data-lucide="trending-down" class="w-3 h-3"></i>
                                        {{ number_format($chg, 2) }}%
                                    </span>
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @if($item->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    <span class="sg-badge low">Active</span>
                                @endif
                            </td>
                            <td class="sg-td-center" style="font-size:12px;color:#94a3b8;white-space:nowrap">
                                {{ $item->last_updated ? \Carbon\Carbon::parse($item->last_updated)->format('M d, H:i') : '—' }}
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($item->trashed())
                                        <form action="{{ route('currency.restore', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Currency">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        @if($item->country)
                                            <a href="{{ route('currency.sync', $item->country->id) }}"
                                               class="sg-btn sg-btn-xs sg-btn-indigo" title="Sync from ExchangeRate API">
                                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                                Sync
                                            </a>
                                        @endif
                                        <a href="{{ route('currency.show', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-secondary">
                                            <i data-lucide="eye" class="w-3 h-3"></i> View
                                        </a>
                                        <a href="{{ route('currency.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                        <form action="{{ route('currency.destroy', $item->id) }}" method="POST" class="sg-delete-form" onsubmit="return confirm('Delete this record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger">Del</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="sg-empty-state">
                                    <div class="sg-empty-icon">
                                        <i data-lucide="banknote" class="w-8 h-8"></i>
                                    </div>
                                    <h3>No Currency Data</h3>
                                    <p>Go to Countries and click Sync Currency for each country, or add records manually.</p>
                                    <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                                        <i data-lucide="globe" class="w-4 h-4"></i>
                                        Go to Countries
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($currency, 'hasPages') && $currency->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $currency->firstItem() }}</strong>–<strong>{{ $currency->lastItem() }}</strong> of <strong>{{ $currency->total() }}</strong> entries
                </div>
                <div class="sg-pagination-nav">
                    {{ $currency->withQueryString()->links() }}
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
        function startImport(service) {
            Swal.fire({
                title: 'Importing ' + service + ' data...',
                html: '<div class="progress-container"><div class="progress-bar" id="import-progress-bar" style="width:0%"></div></div><p id="import-status">Initializing...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1B2433',
                color: '#F8FAFC',
                didOpen: () => {
                    fetch('/' + service + '/import/api', { method: 'GET' })
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
                                text: 'Failed to start import: ' + err.message,
                                confirmButtonColor: '#FF6B00',
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                        });
                }
            });
        }

        function pollProgress(service) {
            let timer = setInterval(() => {
                fetch('/sync/progress/' + service)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'completed' || data.status === 'success') {
                            clearInterval(timer);
                            document.getElementById('import-progress-bar').style.width = '100%';
                            document.getElementById('import-status').innerText = 'Finished successfully!';
                            setTimeout(() => {
                                Swal.close();
                                location.reload();
                            }, 1000);
                        } else if (data.status === 'failed' || data.status === 'error') {
                            clearInterval(timer);
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Import Failed',
                                text: data.message || 'Sync failed.',
                                confirmButtonColor: '#FF6B00',
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                        } else {
                            let pct = data.percentage || 0;
                            document.getElementById('import-progress-bar').style.width = pct + '%';
                            document.getElementById('import-status').innerText = 'Processing: ' + pct + '%';
                        }
                    })
                    .catch(() => {
                        // ignore and retry
                    });
            }, 1500);
        }
    </script>
</x-app-layout>