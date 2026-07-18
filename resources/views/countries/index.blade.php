<x-app-layout>

    <div class="sg-countries-page">
        <!-- Standardized Header -->
        <x-crud-header 
            title="Countries"
            description="Monitor and manage all countries in the global supply chain network."
            icon="globe"
            iconColor="text-orange-500"
        >
            <a href="{{ route('countries.import-csv') }}" onclick="event.preventDefault(); document.getElementById('import-section').scrollIntoView({behavior: 'smooth'})" class="sg-btn sg-btn-sm sg-btn-teal">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import CSV
            </a>
            <a href="{{ route('countries.export-csv') }}" class="sg-btn sg-btn-sm sg-btn-teal">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export CSV
            </a>
            <a href="{{ route('countries.export-pdf') }}" class="sg-btn sg-btn-sm sg-btn-secondary" target="_blank">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Export PDF
            </a>
            <a href="{{ route('countries.import') }}"
               onclick="return confirm('Import all countries from REST Countries API? This may take a moment.')"
               class="sg-btn sg-btn-sm sg-btn-outline-orange">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Sync from API
            </a>
            <a href="{{ route('countries.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Country
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

        <!-- Import CSV Card -->
        <div id="import-section" class="sg-data-card" style="margin-bottom:20px; padding:16px;">
            <h3 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 10px 0;">Import Countries from CSV</h3>
            <form action="{{ route('countries.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                @csrf
                <input type="file" name="file" accept=".csv" required class="sg-form-input" style="width:auto; flex:1; min-width:200px;">
                <button type="submit" class="sg-btn sg-btn-secondary">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Import CSV
                </button>
            </form>
        </div>

        <!-- Standardized Toolbar -->
        <x-crud-toolbar 
            searchPlaceholder="Search countries..."
            searchValue="{{ request('search') }}"
            :showRefresh="true"
            :showExport="false"
            :showImport="false"
            :showAdd="false"
        >
            <select name="region" onchange="this.form.submit()">
                <option value="">All Regions</option>
                @foreach(['Africa','Americas','Asia','Europe','Oceania','Antarctic'] as $r)
                    <option value="{{ $r }}" {{ request('region') === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()">
                <option value="">Active</option>
                <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="country_name" {{ request('sort','country_name') === 'country_name' ? 'selected' : '' }}>Name A–Z</option>
                <option value="country_name_desc" {{ request('sort') === 'country_name_desc' ? 'selected' : '' }}>Name Z–A</option>
                <option value="population_desc" {{ request('sort') === 'population_desc' ? 'selected' : '' }}>Population ↓</option>
                <option value="population_asc" {{ request('sort') === 'population_asc' ? 'selected' : '' }}>Population ↑</option>
                <option value="region" {{ request('sort') === 'region' ? 'selected' : '' }}>Region</option>
            </select>
            <select name="per_page" onchange="this.form.submit()">
                <option value="25" {{ request('per_page','25') == 25 ? 'selected' : '' }}>25 per page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
            </select>
        </x-crud-toolbar>

        <!-- Glass Card with Table -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="database" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">Country Database
                        <span class="sg-count-badge">{{ method_exists($countries, 'total') ? $countries->total() : $countries->count() }} countries</span>
                    </h2>
                </div>
            </div>

            <div style="overflow-x:auto">
                <table class="sg-data-table" id="countries-table">
                    <thead>
                        <tr>
                            <th style="width:44px" class="sg-td-center">#</th>
                            <th style="width:56px">Flag</th>
                            <th>
                                <a href="{{ route('countries.index', array_merge(request()->query(), ['sort' => request('sort') === 'country_name' ? 'country_name_desc' : 'country_name'])) }}"
                                   class="flex items-center gap-2 hover:text-orange-500 transition-colors">
                                    Country
                                    <i data-lucide="arrow-up-down" class="w-3 h-3 opacity-50"></i>
                                </a>
                            </th>
                            <th>Capital</th>
                            <th>Region</th>
                            <th class="sg-td-right">
                                <a href="{{ route('countries.index', array_merge(request()->query(), ['sort' => request('sort') === 'population_desc' ? 'population_asc' : 'population_desc'])) }}"
                                   class="flex items-center gap-2 justify-end hover:text-orange-500 transition-colors">
                                    Population
                                    <i data-lucide="arrow-up-down" class="w-3 h-3 opacity-50"></i>
                                </a>
                            </th>
                            <th>GDP</th>
                            <th>Weather/Temp</th>
                            <th>Currency</th>
                            <th class="sg-td-center">Risk Score</th>
                            <th class="sg-td-center">Status</th>
                            <th style="width:220px" class="sg-td-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $country)
                            @php
                                $gdpVal = $country->economicData->gdp ?? null;
                                if ($gdpVal) {
                                    if ($gdpVal >= 1e12) $gdpStr = '$' . number_format($gdpVal / 1e12, 1) . 'T';
                                    elseif ($gdpVal >= 1e9) $gdpStr = '$' . number_format($gdpVal / 1e9, 1) . 'B';
                                    else $gdpStr = '$' . number_format($gdpVal / 1e6, 1) . 'M';
                                } else {
                                    $gdpStr = '—';
                                }

                                $weatherVal = $country->weatherData;
                                $weatherStr = $weatherVal ? $weatherVal->temperature . '°C' : '—';

                                $currencyVal = $country->currencyData;
                                $currencyStr = $currencyVal ? $currencyVal->currency_code . ' (' . number_format($currencyVal->exchange_rate, 2) . ')' : '—';

                                $scoreVal = $country->riskScore;
                                $scoreStr = $scoreVal ? $scoreVal->total_score . '/100' : '—';

                                $level = $scoreVal->risk_level ?? 'Low';
                                $badgeStyle = match($level) {
                                    'Critical' => 'background:#fff1f2;color:#e11d48;border:1px solid #fecdd3',
                                    'High' => 'background:#fff7ed;color:#ea580c;border:1px solid #ffedd5',
                                    'Medium' => 'background:#fffbeb;color:#d97706;border:1px solid #fef3c7',
                                    default => 'background:#ecfdf5;color:#10b981;border:1px solid #d1fae5'
                                };
                            @endphp
                            <tr>
                                <td class="sg-td-num">{{ $countries->firstItem() + $loop->index }}</td>
                                <td>
                                    @php $iso = strtolower($country->country_code ?? 'un'); @endphp
                                    <img src="{{ $country->flag ?: 'https://flagcdn.com/w40/'.$iso.'.png' }}"
                                         alt="{{ $country->country_name }}" loading="lazy"
                                         onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                         style="width:40px;height:27px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.1)">
                                </td>
                                <td class="sg-td-bold">{{ $country->country_name }}</td>
                                <td style="color:#64748b">{{ $country->capital ?? '—' }}</td>
                                <td style="color:#64748b">{{ $country->region ?? '—' }}</td>
                                <td class="sg-td-right" style="font-size:13px">
                                    {{ $country->population ? number_format($country->population) : '—' }}
                                </td>
                                <td style="font-size:13px;font-weight:500;color:#334155">{{ $gdpStr }}</td>
                                <td style="font-size:13px;color:#334155">{{ $weatherStr }}</td>
                                <td style="font-size:12px;color:#64748b">{{ $currencyStr }}</td>
                                <td class="sg-td-center" style="font-size:13px;font-weight:600;color:#1e293b">{{ $scoreStr }}</td>
                                <td class="sg-td-center">
                                    @if($country->trashed())
                                        <span class="sg-badge high">Deleted</span>
                                    @else
                                        <span style="display:inline-flex;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;{{ $badgeStyle }}">{{ $level }}</span>
                                    @endif
                                </td>
                                <td class="sg-td-center">
                                    <div class="sg-action-group">
                                        @if($country->trashed())
                                            <form action="{{ route('countries.restore', $country->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Country">
                                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            @if($scoreVal)
                                                <a href="{{ route('risk-scores.show', $scoreVal->id) }}"
                                                   class="sg-btn sg-btn-xs sg-btn-scorecard" title="View Scorecard">
                                                    Scorecard
                                                </a>
                                            @else
                                                <a href="{{ route('risk-scores.calculate', $country->id) }}"
                                                   class="sg-btn sg-btn-xs sg-btn-calc" title="Calculate Risk">
                                                    Calc
                                                </a>
                                            @endif
                                            <a href="{{ route('countries.show', $country->id) }}"
                                               class="sg-btn sg-btn-xs sg-btn-secondary">
                                                <i data-lucide="eye" class="w-3 h-3"></i> View
                                            </a>
                                            <a href="{{ route('countries.edit', $country->id) }}"
                                               class="sg-btn sg-btn-xs sg-btn-edit" id="edit-country-{{ $country->id }}">
                                                Edit
                                            </a>
                                            <form action="{{ route('countries.destroy', $country->id) }}" method="POST" class="sg-delete-form"
                                                  onsubmit="return confirm('Delete {{ addslashes($country->country_name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-country-{{ $country->id }}">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">
                                    <div class="sg-empty-state">
                                        <div class="sg-empty-icon">
                                            <i data-lucide="globe" class="w-8 h-8"></i>
                                        </div>
                                        <h3>No Countries Found</h3>
                                        <p>Import data from REST Countries API to get started.</p>
                                        <a href="{{ route('countries.import') }}"
                                           onclick="return confirm('Import all countries from REST Countries API?')"
                                           class="sg-btn sg-btn-sm sg-btn-gradient">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                            Import Now
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($countries, 'hasPages') && $countries->hasPages())
                <div class="sg-pagination sg-countries-pagination">
                    <div class="sg-pagination-info">
                        Showing <strong>{{ $countries->firstItem() }}</strong>–<strong>{{ $countries->lastItem() }}</strong> of <strong>{{ $countries->total() }}</strong> countries
                    </div>
                    <div class="sg-pagination-nav">
                        {{ $countries->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
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
</x-app-layout>
