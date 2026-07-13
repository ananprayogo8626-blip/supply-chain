<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">Weather Monitoring</h1>
                <p class="sg-page-desc">Real-time weather data for all monitored countries via Open-Meteo API.</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('weather.import') }}"
                   onclick="return confirm('Import weather data for all countries from Open-Meteo API? This may take a moment.')"
                   class="sg-btn sg-btn-outline-orange" id="btn-import-weather">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Weather API
                </a>
                <a href="{{ route('weather.create') }}" class="sg-btn sg-btn-gradient" id="btn-add-weather">
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
                <svg fill="none" stroke="#0d9488" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                <h2 class="sg-data-title">Weather Records
                    <span class="sg-count-badge">{{ $weather->count() }} entries</span>
                </h2>
            </div>
            <div style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px">
                <svg fill="none" stroke="#0d9488" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                Data synced from Open-Meteo API (free, no key required)
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="weather-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('weather.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('weather.index', array_merge(request()->query(), ['sort' => request('sort') === 'temperature' ? 'temperature_desc' : 'temperature'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Temperature
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('weather.index', array_merge(request()->query(), ['sort' => request('sort') === 'humidity' ? 'humidity_desc' : 'humidity'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Humidity
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('weather.index', array_merge(request()->query(), ['sort' => request('sort') === 'wind_speed' ? 'wind_speed_desc' : 'wind_speed'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Wind Speed
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">Rain</th>
                        <th class="sg-td-center">Cloud</th>
                        <th class="sg-td-center">Pressure</th>
                        <th>Weather Condition</th>
                        <th class="sg-td-center">Update Time</th>
                        <th class="sg-td-center" style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weather as $item)
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
                            <td class="sg-td-center">
                                @php $temp = $item->temperature ?? 0; @endphp
                                <span style="font-weight:700;font-size:15px;color:{{ $temp > 35 ? '#dc2626' : ($temp < 5 ? '#2563eb' : '#0d9488') }}">
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
                            <td style="font-size:13px;color:#475569">{{ $item->weather_condition ?? '—' }}</td>
                            <td class="sg-td-center" style="font-size:12px;color:#94a3b8">
                                {{ $item->updated_at ? $item->updated_at->format('M d, H:i') : '—' }}
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($item->country)
                                        <a href="{{ route('weather.sync', $item->country->id) }}"
                                           class="sg-btn sg-btn-xs sg-btn-indigo" id="sync-weather-{{ $item->id }}"
                                           title="Sync from Open-Meteo API">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Sync
                                        </a>
                                    @endif
                                    <a href="{{ route('weather.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-weather-{{ $item->id }}">Edit</a>
                                    <form action="{{ route('weather.destroy', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this record?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-weather-{{ $item->id }}">Del</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                                </div>
                                <p>No weather data yet. Go to Countries and click Sync for each country, or add records manually.</p>
                                <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-teal">Go to Countries</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>