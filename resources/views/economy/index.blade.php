<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Economic Data"
        description="Macroeconomic indicators sourced from World Bank API."
        icon="trending-up"
        iconColor="text-green-400"
    >

        <button onclick="startImport('economy')" class="sg-btn sg-btn-sm sg-btn-outline-orange">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            Sync World Bank API
        </button>
        <a href="{{ route('economy.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add Data
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


    <!-- Standardized Toolbar -->
    <x-crud-toolbar 
        searchPlaceholder="Search economic data..."
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
                <i data-lucide="trending-up" class="w-5 h-5 text-green-400"></i>
                <h2 class="sg-data-title">Macroeconomic Indicators
                    <span class="sg-count-badge">{{ $economy->total() }} records</span>
                </h2>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-2">
                <i data-lucide="database" class="w-4 h-4 text-blue-400"></i>
                Source: World Bank API
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="economy-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>Country</th>
                        <th class="sg-td-right">GDP (USD)</th>
                        <th class="sg-td-center">GDP Growth</th>
                        <th class="sg-td-center">Inflation</th>
                        <th class="sg-td-right">Exports</th>
                        <th class="sg-td-right">Imports</th>
                        <th class="sg-td-center">Year</th>
                        <th class="sg-td-center">Status</th>
                        <th class="sg-td-center" style="width:170px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($economy as $item)
                        <tr>
                            <td class="sg-td-num">{{ $economy->firstItem() + $loop->index }}</td>
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
                                    <span class="font-bold {{ $growth >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                    </span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @php $inf = (float)($item->inflation ?? 0); @endphp
                                <span class="font-semibold {{ $inf > 10 ? 'text-red-400' : ($inf > 5 ? 'text-orange-400' : 'text-slate-400') }}">
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
                            <td class="sg-td-center">
                                @if($item->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    <span class="sg-badge low">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($item->trashed())
                                        <form action="{{ route('economy.restore', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Economy">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        @if($item->country_id)
                                            <a href="{{ route('economy.sync', $item->country_id) }}"
                                               onclick="return confirm('Sync latest data from World Bank API?')"
                                               class="sg-btn sg-btn-xs sg-btn-scorecard" title="Sync from World Bank API">
                                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                                Sync
                                            </a>
                                        @endif
                                        <a href="{{ route('economy.show', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-secondary">
                                            <i data-lucide="eye" class="w-3 h-3"></i> View
                                        </a>
                                        <a href="{{ route('economy.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                        <form action="{{ route('economy.destroy', $item->id) }}" method="POST" class="sg-delete-form" onsubmit="return confirm('Delete this record?')">
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
                                        <i data-lucide="trending-up" class="w-8 h-8"></i>
                                    </div>
                                    <h3>No Economic Data</h3>
                                    <p>Go to Countries and click Economy Sync for each country, or add records manually.</p>
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
        @if(method_exists($economy, 'hasPages') && $economy->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $economy->firstItem() }}</strong>–<strong>{{ $economy->lastItem() }}</strong> of <strong>{{ $economy->total() }}</strong> records
                </div>
                <div class="sg-pagination-nav">
                    {{ $economy->withQueryString()->links() }}
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