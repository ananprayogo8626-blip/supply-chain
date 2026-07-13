<x-app-layout>
    @push('head')
        <!-- Leaflet Map CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endpush

    <!-- Header -->
    <div class="flex items-center justify-between pb-4 mb-5 border-b border-white/5">
        <div>
            <h1 class="sg-page-title flex items-center gap-2">
                <i data-lucide="map" class="text-sky-400 w-6 h-6"></i>
                Global Risk Visualizer Map
            </h1>
            <p class="text-xs text-slate-400 mt-1">Interactive visualization of country risk indexes, weather statuses, and port logistics channels.</p>
        </div>
    </div>

    <!-- Map Info Cards -->
    <div class="sg-grid-stats mb-5">
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">Countries Mapped</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value">{{ $countries->count() }}</span>
                    <span class="sg-stat-trend up" style="color:var(--accent-blue)">
                        <i data-lucide="globe" class="w-3 h-3"></i> States
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Operational coordinates</p>
        </div>

        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">Ports Mapped</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value">{{ $ports->count() }}</span>
                    <span class="sg-stat-trend up" style="color:var(--accent-blue)">
                        <i data-lucide="anchor" class="w-3 h-3"></i> Nodes
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Maritime shipping lanes</p>
        </div>

        <div class="sg-stat-card" style="border-color:rgba(239,68,68,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-danger)">Critical/High Risk</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--sg-danger)">
                        {{ $countries->filter(fn($c) => optional($c->riskScore)->total_score >= 51)->count() }}
                    </span>
                    <span class="sg-stat-trend down" style="color:var(--sg-danger)">
                        <i data-lucide="alert-octagon" class="w-3 h-3"></i> Warning
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-red-400 mt-1">Score &ge; 51 units</p>
        </div>

        <div class="sg-stat-card" style="border-color:rgba(245,158,11,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-warning)">Medium Risk</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--sg-warning)">
                        {{ $countries->filter(fn($c) => optional($c->riskScore)->total_score >= 26 && optional($c->riskScore)->total_score < 51)->count() }}
                    </span>
                    <span class="sg-stat-trend up" style="color:var(--sg-warning)">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> Watch
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-amber-400 mt-1">Score 26 - 50 units</p>
        </div>
    </div>

    <!-- Map Container -->
    <div class="sg-panel sg-glass">
        <div class="sg-panel-head">
            <div>
                <h3 class="sg-panel-title">Interactive Risk Terrain Map</h3>
                <p class="text-[11px] text-slate-400 mt-1">Circles represent weighted state risk matrices. Anchor points depict commercial maritime gateways.</p>
            </div>
        </div>
        <div id="global-map-canvas" style="height: 520px; width: 100%; border-radius: 8px; border: 1px solid var(--sg-border);"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map with a default view (global)
            const map = L.map('global-map-canvas').setView([10.0, 15.0], 2);

            // Use standard OSM tiles for "warna dunia"
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const countries = [
                @foreach($countries as $c)
                @php
                    $gdpVal = $c->economicData->gdp ?? null;
                    if ($gdpVal) {
                        if ($gdpVal >= 1e12) $gdpStr = '$' . number_format($gdpVal / 1e12, 1) . 'T';
                        elseif ($gdpVal >= 1e9) $gdpStr = '$' . number_format($gdpVal / 1e9, 1) . 'B';
                        else $gdpStr = '$' . number_format($gdpVal / 1e6, 1) . 'M';
                    } else {
                        $gdpStr = 'N/A';
                    }

                    $weatherVal = $c->weatherData;
                    $weatherStr = $weatherVal ? $weatherVal->temperature . '°C, ' . ($weatherVal->weather_condition ?? '') : 'N/A';

                    $currencyVal = $c->currencyData;
                    $currencyStr = $currencyVal ? $currencyVal->currency_code . ' (' . number_format($currencyVal->exchange_rate, 2) . ')' : 'N/A';

                    $latestNews = $c->news->sortByDesc('published_at')->first();
                    $newsStr = $latestNews ? \Illuminate\Support\Str::limit($latestNews->title, 55) : 'No recent news';
                @endphp
                {
                    name: "{{ addslashes($c->country_name) }}",
                    lat: {{ $c->latitude ?? 0 }},
                    lng: {{ $c->longitude ?? 0 }},
                    score: {{ $c->riskScore->total_score ?? 0 }},
                    level: "{{ $c->riskScore->risk_level ?? 'Low' }}",
                    flag: "{{ $c->flag ?? '' }}",
                    gdp: "{{ addslashes($gdpStr) }}",
                    weather: "{{ addslashes($weatherStr) }}",
                    currency: "{{ addslashes($currencyStr) }}",
                    news: "{{ addslashes($newsStr) }}"
                },
                @endforeach
            ];

            countries.forEach(function(c) {
                if (!c.lat || !c.lng) return;
                
                let color = '#10B981';
                if (c.score >= 76) color = '#EF4444';
                else if (c.score >= 51) color = '#F97316';
                else if (c.score >= 26) color = '#EAB308';

                const circle = L.circle([c.lat, c.lng], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.18,
                    weight: 1.5,
                    radius: 350000
                }).addTo(map);

                circle.bindPopup(`
                    <div style="font-family: Inter, sans-serif; width: 230px; color:#f8fafc">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            ${c.flag ? `<img src="${c.flag}" style="width:24px;height:14px;object-fit:cover;border-radius:2px;border:1px solid rgba(255,255,255,0.1);" />` : ''}
                            <strong style="font-size: 13px; color: #fff;">${c.name}</strong>
                        </div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">GDP: <strong style="color: #fff;">${c.gdp}</strong></div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Weather: <strong style="color: #fff;">${c.weather}</strong></div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Currency: <strong style="color: #fff;">${c.currency}</strong></div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 8px;">Latest Event: <strong style="color: #cbd5e1;">${c.news}</strong></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <span style="font-size: 12px; font-weight: bold; color: #fff;">Score: ${c.score}/100</span>
                            <span style="font-size: 9px; font-weight: bold; background: ${color}20; color: ${color}; border: 1px solid ${color}; border-radius: 4px; padding: 1.5px 6px; text-transform: uppercase;">${c.level}</span>
                        </div>
                    </div>
                `);
            });

            const ports = [
                @foreach($ports as $p)
                {
                    name: "{{ addslashes($p->port_name) }}",
                    code: "{{ $p->port_code }}",
                    city: "{{ addslashes($p->city ?? '') }}",
                    lat: {{ $p->latitude }},
                    lng: {{ $p->longitude }},
                    type: "{{ $p->port_type ?? '' }}",
                    status: "{{ $p->status ?? 'Active' }}",
                    country: "{{ addslashes($p->country->country_name ?? '') }}"
                },
                @endforeach
            ];

            const portIcon = L.divIcon({
                className: '',
                html: '<div style="font-size: 16px; text-shadow: 0 0 4px rgba(56,189,248,0.6);">⚓</div>',
                iconSize: [20, 20],
                iconAnchor: [10, 20],
                popupAnchor: [0, -20]
            });

            ports.forEach(function(p) {
                if (!p.lat || !p.lng) return;
                const statusColor = p.status === 'Active' ? '#10B981' : (p.status === 'Closed' ? '#EF4444' : '#F59E0B');
                const marker = L.marker([p.lat, p.lng], { icon: portIcon }).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: Inter, sans-serif; width: 180px; color:#f8fafc">
                        <div style="font-size: 13px; font-weight: bold; color: #38bdf8; margin-bottom: 5px;">⚓ ${p.name}</div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">City: <strong>${p.city || 'N/A'}</strong></div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Country: <strong>${p.country}</strong></div>
                        <div style="font-size: 10px; color: #94a3b8; margin-bottom: 6px;">Code: <strong>${p.code}</strong></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.08);">
                            <span style="font-size: 9px; font-weight: bold; background: rgba(56,189,248,0.1); color: #38bdf8; border-radius: 4px; padding: 1.5px 5px;">${p.type}</span>
                            <span style="font-size: 9px; font-weight: bold; color: ${statusColor};">${p.status}</span>
                        </div>
                    </div>
                `);
            });
        });
    </script>
</x-app-layout>
