<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Weather Monitoring"
        description="Real-time weather data for all monitored countries via Open-Meteo API."
        icon="cloud-sun"
        iconColor="text-blue-400"
    >
        <a href="#import-section" onclick="document.getElementById('import-section').scrollIntoView({behavior: 'smooth'})" class="sg-btn sg-btn-sm sg-btn-teal">
            <i data-lucide="upload" class="w-4 h-4"></i>
            Import CSV
        </a>
        <a href="{{ route('weather.export-csv') }}" class="sg-btn sg-btn-sm sg-btn-teal">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export CSV
        </a>
        <a href="{{ route('weather.export-pdf') }}" class="sg-btn sg-btn-sm sg-btn-secondary" target="_blank">
            <i data-lucide="file-text" class="w-4 h-4"></i>
            Export PDF
        </a>
        <button onclick="startImport('weather')" class="sg-btn sg-btn-sm sg-btn-outline-orange">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            Sync Weather API
        </button>
        <a href="{{ route('weather.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
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

    <!-- CSV Import Card -->
    <div id="import-section" class="sg-data-card" style="margin-bottom:20px; padding:16px;">
        <h3 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 10px 0;">Import Weather from CSV</h3>
        <form action="{{ route('weather.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            @csrf
            <input type="file" name="file" accept=".csv" required class="sg-form-input" style="width:auto; flex:1; min-width:200px;">
            <button type="submit" class="sg-btn sg-btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import CSV
            </button>
        </form>
    </div>

    <!-- Weather Statistics Cards -->
    <div class="sg-weather-stats">
        <div class="sg-weather-stat-card">
            <div class="sg-weather-stat-icon">
                <i data-lucide="thermometer" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="sg-weather-stat-label">Avg Temperature</span>
                <div class="sg-weather-stat-value">
                    @php $avgTemp = $weather->avg('temperature'); @endphp
                    {{ number_format($avgTemp, 1) }}<span class="sg-weather-stat-unit">°C</span>
                </div>
            </div>
        </div>
        <div class="sg-weather-stat-card">
            <div class="sg-weather-stat-icon">
                <i data-lucide="droplets" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="sg-weather-stat-label">Avg Humidity</span>
                <div class="sg-weather-stat-value">
                    @php $avgHumidity = $weather->avg('humidity'); @endphp
                    {{ number_format($avgHumidity, 0) }}<span class="sg-weather-stat-unit">%</span>
                </div>
            </div>
        </div>
        <div class="sg-weather-stat-card">
            <div class="sg-weather-stat-icon">
                <i data-lucide="wind" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="sg-weather-stat-label">Avg Wind Speed</span>
                <div class="sg-weather-stat-value">
                    @php $avgWind = $weather->avg('wind_speed'); @endphp
                    {{ number_format($avgWind, 1) }}<span class="sg-weather-stat-unit">m/s</span>
                </div>
            </div>
        </div>
        <div class="sg-weather-stat-card">
            <div class="sg-weather-stat-icon">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="sg-weather-stat-label">Total Records</span>
                <div class="sg-weather-stat-value">
                    {{ $weather->total() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Standardized Toolbar -->
    <x-crud-toolbar 
        searchPlaceholder="Search weather records..."
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
                <i data-lucide="cloud" class="w-5 h-5 text-blue-400"></i>
                <h2 class="sg-data-title">Weather Records
                    <span class="sg-count-badge">{{ $weather->total() }} entries</span>
                </h2>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-teal-500"></i>
                Data synced from Open-Meteo API
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="weather-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>Country</th>
                        <th class="sg-td-center">Temperature</th>
                        <th class="sg-td-center">Humidity</th>
                        <th class="sg-td-center">Wind Speed</th>
                        <th class="sg-td-center">Rain</th>
                        <th class="sg-td-center">Cloud</th>
                        <th class="sg-td-center">Pressure</th>
                        <th>Weather Condition</th>
                        <th class="sg-td-center">Status</th>
                        <th class="sg-td-center">Update Time</th>
                        <th class="sg-td-center" style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weather as $item)
                        <tr>
                            <td class="sg-td-num">{{ $weather->firstItem() + $loop->index }}</td>
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
                            <td class="sg-td-center">
                                @php $temp = $item->temperature ?? 0; @endphp
                                <span class="font-bold text-lg {{ $temp > 35 ? 'sg-temp-hot' : ($temp < 5 ? 'sg-temp-cold' : ($temp > 25 ? 'sg-temp-warm' : 'sg-temp-cool')) }}">
                                    {{ $temp }}°C
                                </span>
                            </td>
                            <td class="sg-td-center" style="color:#64748b">
                                {{ $item->humidity ?? '—' }}<span style="font-size:11px;color:#94a3b8">%</span>
                            </td>
                            <td class="sg-td-center" style="color:#64748b">
                                {{ $item->wind_speed ?? '—' }}
                                <span style="font-size:11px;color:#94a3b8">m/s</span>
                            </td>
                            <td class="sg-td-center" style="color:#64748b">
                                {{ $item->rainfall ?? '—' }}
                                <span style="font-size:11px;color:#94a3b8">mm</span>
                            </td>
                            <td class="sg-td-center" style="color:#64748b">
                                {{ $item->cloud ?? '—' }}<span style="font-size:11px;color:#94a3b8">%</span>
                            </td>
                            <td class="sg-td-center" style="color:#64748b">
                                {{ $item->pressure ?? '—' }}<span style="font-size:11px;color:#94a3b8">hPa</span>
                            </td>
                            <td style="font-size:13px;color:#475569">
                                @php $condition = strtolower($item->weather_condition ?? ''); @endphp
                                @if(str_contains($condition, 'sunny') || str_contains($condition, 'clear'))
                                    <span class="sg-weather-badge sunny">
                                        <i data-lucide="sun" class="w-3 h-3"></i>
                                        {{ $item->weather_condition ?? '—' }}
                                    </span>
                                @elseif(str_contains($condition, 'cloud') || str_contains($condition, 'overcast'))
                                    <span class="sg-weather-badge cloudy">
                                        <i data-lucide="cloud" class="w-3 h-3"></i>
                                        {{ $item->weather_condition ?? '—' }}
                                    </span>
                                @elseif(str_contains($condition, 'rain') || str_contains($condition, 'drizzle'))
                                    <span class="sg-weather-badge rainy">
                                        <i data-lucide="cloud-rain" class="w-3 h-3"></i>
                                        {{ $item->weather_condition ?? '—' }}
                                    </span>
                                @elseif(str_contains($condition, 'storm') || str_contains($condition, 'thunder'))
                                    <span class="sg-weather-badge stormy">
                                        <i data-lucide="cloud-lightning" class="w-3 h-3"></i>
                                        {{ $item->weather_condition ?? '—' }}
                                    </span>
                                @else
                                    {{ $item->weather_condition ?? '—' }}
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @if($item->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    <span class="sg-badge low">Active</span>
                                @endif
                            </td>
                            <td class="sg-td-center" style="font-size:12px;color:#94a3b8">
                                {{ $item->updated_at ? $item->updated_at->format('M d, H:i') : '—' }}
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($item->trashed())
                                        <form action="{{ route('weather.restore', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Weather">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        @if($item->country)
                                            <a href="{{ route('weather.sync', $item->country->id) }}"
                                               class="sg-btn sg-btn-xs sg-btn-indigo" title="Sync from Open-Meteo API">
                                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                                Sync
                                            </a>
                                        @endif
                                        <a href="{{ route('weather.show', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-secondary">
                                            <i data-lucide="eye" class="w-3 h-3"></i> View
                                        </a>
                                        <a href="{{ route('weather.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                        <form action="{{ route('weather.destroy', $item->id) }}" method="POST" class="sg-delete-form" onsubmit="return confirm('Delete this record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger">Del</button>
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
                                        <i data-lucide="cloud" class="w-8 h-8"></i>
                                    </div>
                                    <h3>No Weather Data</h3>
                                    <p>Go to Countries and click Sync for each country, or add records manually.</p>
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
        @if(method_exists($weather, 'hasPages') && $weather->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $weather->firstItem() }}</strong>–<strong>{{ $weather->lastItem() }}</strong> of <strong>{{ $weather->total() }}</strong> records
                </div>
                <div class="sg-pagination-nav">
                    {{ $weather->withQueryString()->links() }}
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