<x-app-layout>

    <div class="sg-countries-page">
        <!-- Header -->
        <div class="sg-page-header">
            <div class="sg-page-header-row">
                <div>
                    <h1 class="sg-page-title">Countries</h1>
                    <p class="sg-page-desc">Monitor and manage all countries in the global supply chain network.</p>
                </div>
                <div class="sg-data-actions">
                    <a href="{{ route('countries.import') }}"
                       onclick="return confirm('Import all countries from REST Countries API? This may take a moment.')"
                       class="sg-btn sg-btn-outline-orange" id="btn-import-api">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import from API
                    </a>
                    <a href="{{ route('countries.create') }}" class="sg-btn sg-btn-gradient" id="btn-add-country">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Country
                    </a>
                </div>
            </div>
        </div>

    @if(session('success'))
        <div class="sg-flash sg-flash-success sg-flash-compact">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error sg-flash-compact">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

        <!-- Toolbar -->
        <div class="sg-countries-toolbar">
            <form method="GET" action="{{ route('countries.index') }}" id="country-filter-form" class="sg-toolbar-form">
                <div class="sg-filter-wrap">
                    <svg class="sg-filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" id="country-search" class="sg-filter-input" placeholder="Search countries..."
                        value="{{ request('search') }}" autocomplete="off">
                </div>
                <select name="region" id="country-region" class="sg-filter-select" onchange="this.form.submit()">
                    <option value="">All Regions</option>
                    @foreach(['Africa','Americas','Asia','Europe','Oceania','Antarctic'] as $r)
                        <option value="{{ $r }}" {{ request('region') === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                <select name="sort" id="country-sort" class="sg-filter-select" onchange="this.form.submit()">
                    <option value="country_name" {{ request('sort','country_name') === 'country_name' ? 'selected' : '' }}>Name A–Z</option>
                    <option value="country_name_desc" {{ request('sort') === 'country_name_desc' ? 'selected' : '' }}>Name Z–A</option>
                    <option value="population_desc" {{ request('sort') === 'population_desc' ? 'selected' : '' }}>Population ↓</option>
                    <option value="population_asc" {{ request('sort') === 'population_asc' ? 'selected' : '' }}>Population ↑</option>
                    <option value="region" {{ request('sort') === 'region' ? 'selected' : '' }}>Region</option>
                </select>
                <select name="per_page" id="country-perpage" class="sg-filter-select" onchange="this.form.submit()">
                    <option value="25" {{ request('per_page','25') == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
                <button type="submit" class="sg-btn sg-btn-outline sg-btn-sm">Apply</button>
                @if(request()->hasAny(['search','region','sort']))
                    <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-sm sg-btn-clear">Clear</a>
                @endif
            </form>
        </div>

        <!-- Table Container -->
        <div class="sg-data-card sg-countries-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <svg fill="none" stroke="#4f46e5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h2 class="sg-data-title">Country Database
                        <span class="sg-count-badge">{{ method_exists($countries, 'total') ? $countries->total() : $countries->count() }} countries</span>
                    </h2>
                </div>
                <div class="sg-data-head-actions">
                    <button onclick="exportTableToCSV('countries-export.csv')" class="sg-btn sg-btn-export">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </button>
                    <button onclick="window.print()" class="sg-btn sg-btn-print">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2z"/></svg>
                        Print
                    </button>
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
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th>Capital</th>
                        <th>Region</th>
                        <th class="sg-td-right">
                            <a href="{{ route('countries.index', array_merge(request()->query(), ['sort' => request('sort') === 'population_desc' ? 'population_asc' : 'population_desc'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:flex-end">
                                Population
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
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
                                <span style="display:inline-flex;padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;{{ $badgeStyle }}">{{ $level }}</span>
                            </td>
                            <td class="sg-td-center">
                                <div class="sg-action-group">
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
                                    <a href="{{ route('countries.edit', $country->id) }}"
                                       class="sg-btn sg-btn-xs sg-btn-edit" id="edit-country-{{ $country->id }}">
                                        Edit
                                    </a>
                                    <form action="{{ route('countries.destroy', $country->id) }}" method="POST" class="sg-delete-form"
                                          onsubmit="return confirm('Delete {{ addslashes($country->country_name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-delete" id="del-country-{{ $country->id }}">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p>No countries found. Import data from REST Countries API to get started.</p>
                                <a href="{{ route('countries.import') }}"
                                   onclick="return confirm('Import all countries from REST Countries API?')"
                                   class="sg-btn sg-btn-primary" id="btn-import-empty">Import Now</a>
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

    <script>
    // Live search with debounce
    (function(){
        const inp = document.getElementById('country-search');
        if (!inp) return;
        let timer;
        inp.addEventListener('input', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                document.getElementById('country-filter-form').submit();
            }, 500);
        });
    })();

    // Export Visible Table to CSV
    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll("#countries-table tr");
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (let j = 0; j < cols.length - 1; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/(\s\s+)/gm, " ").trim();
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        const downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
    </script>
</x-app-layout>
