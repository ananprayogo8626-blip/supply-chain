<x-app-layout>
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <style>
            .detail-grid {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 24px;
                margin-bottom: 24px;
            }
            @media (max-width: 1024px) {
                .detail-grid {
                    grid-template-columns: 1fr;
                }
            }
            
            /* Port Table styles */
            .port-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                font-size: 13px;
            }
            .port-table th {
                padding: 10px 8px;
                border-bottom: 1px solid var(--sg-border);
                color: var(--sg-text-secondary);
                font-weight: 600;
            }
            .port-table td {
                padding: 12px 8px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .port-table tr:hover td {
                background: rgba(255, 255, 255, 0.01);
            }
        </style>
    @endpush

    @php
        $level = $country->riskScore->risk_level ?? 'Low';
        $color = match($level) {
            'Critical' => 'var(--sg-danger)',
            'High' => 'var(--accent-orange)',
            'Medium' => 'var(--sg-warning)',
            default => 'var(--sg-success)'
        };
    @endphp

    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($country->flag)
                        <img src="{{ $country->flag }}" alt="{{ $country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">{{ $country->country_name }}</h1>
                        <p class="sg-crud-description">
                            {{ $country->capital ?? 'N/A' }} • {{ $country->region ?? 'N/A' }} • {{ $country->country_code }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('countries.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('countries.edit', $country->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="detail-grid">
        
        <!-- Left Column: Basic Info -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            
            <!-- Country Info Card -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="globe" class="w-5 h-5 text-orange-500"></i>
                        <h2 class="sg-data-title">Country Information</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">ISO Code</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->country_code ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Capital</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->capital ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Region</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->region ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Subregion</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->subregion ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Population</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->population ? number_format($country->population) : '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Area</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->area ? number_format($country->area) . ' km²' : '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Timezone</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->timezone ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Currency</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->currency ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Score Card -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="shield-alert" class="w-5 h-5" style="color:{{ $color }}"></i>
                        <h2 class="sg-data-title">Risk Assessment</h2>
                    </div>
                </div>
                <div style="padding:20px; text-align:center;">
                    @if($country->riskScore)
                        <div style="font-size:48px; font-weight:800; color:{{ $color }}; line-height:1;">
                            {{ $country->riskScore->total_score ?? 0 }}
                        </div>
                        <span class="sg-badge {{ strtolower($level) }}" style="margin-top:8px;">{{ $level }}</span>
                    @else
                        <p style="color:var(--sg-text-secondary);">No risk data available</p>
                    @endif
                </div>
            </div>

            <!-- Coordinates Card -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i>
                        <h2 class="sg-data-title">Location</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Latitude</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->latitude ?? '—' }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--sg-text-secondary); font-size:13px;">Longitude</span>
                            <span style="color:var(--sg-text-primary); font-weight:600;">{{ $country->longitude ?? '—' }}</span>
                        </div>
                        @if($country->latitude && $country->longitude)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $country->latitude }},{{ $country->longitude }}"
                           target="_blank" rel="noopener"
                           class="sg-btn sg-btn-sm sg-btn-secondary" style="margin-top:8px; width:100%; justify-content:center;">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            Open in Google Maps
                        </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Detailed Data -->
        <div style="display:flex; flex-direction:column; gap:20px;">
            
            <!-- Interactive Map -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="map" class="w-5 h-5 text-cyan-400"></i>
                        <h2 class="sg-data-title">Map View</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->latitude && $country->longitude)
                    <div id="country-map" style="height:250px; border-radius:12px; overflow:hidden;"></div>
                    @else
                    <div style="height:250px; display:flex; align-items:center; justify-content:center; color:var(--sg-text-secondary);">
                        <p>No location data available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Weather Summary -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="cloud-sun" class="w-5 h-5 text-blue-400"></i>
                        <h2 class="sg-data-title">Weather Summary</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->weatherData)
                    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px;">
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                            <i data-lucide="thermometer" class="w-6 h-6 text-orange-400 mx-auto mb-2"></i>
                            <div style="font-size:24px; font-weight:700; color:var(--sg-text-primary);">{{ $country->weatherData->temperature }}°C</div>
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Temperature</div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                            <i data-lucide="droplets" class="w-6 h-6 text-blue-400 mx-auto mb-2"></i>
                            <div style="font-size:24px; font-weight:700; color:var(--sg-text-primary);">{{ $country->weatherData->humidity }}%</div>
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Humidity</div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                            <i data-lucide="wind" class="w-6 h-6 text-cyan-400 mx-auto mb-2"></i>
                            <div style="font-size:24px; font-weight:700; color:var(--sg-text-primary);">{{ $country->weatherData->wind_speed }}</div>
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Wind km/h</div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                            <i data-lucide="cloud" class="w-6 h-6 text-slate-400 mx-auto mb-2"></i>
                            <div style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin-top:8px;">{{ $country->weatherData->weather_condition ?? '—' }}</div>
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Condition</div>
                        </div>
                    </div>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary);">
                        <p>No weather data available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Economic Data -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="trending-up" class="w-5 h-5 text-green-400"></i>
                        <h2 class="sg-data-title">Economic Indicators</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->economicData)
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">GDP (Nominal)</div>
                            <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">
                                {{ $country->economicData->gdp ? '$' . number_format($country->economicData->gdp / 1e9, 2) . 'B' : '—' }}
                            </div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">GDP Growth</div>
                            <div style="font-size:20px; font-weight:700; color:{{ $country->economicData->gdp_growth >= 0 ? 'var(--sg-success)' : 'var(--sg-danger)' }};">
                                {{ $country->economicData->gdp_growth ? number_format($country->economicData->gdp_growth, 2) . '%' : '—' }}
                            </div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">Inflation Rate</div>
                            <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">
                                {{ $country->economicData->inflation ? number_format($country->economicData->inflation, 2) . '%' : '—' }}
                            </div>
                        </div>
                    </div>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary);">
                        <p>No economic data available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Exchange Rate -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="coins" class="w-5 h-5 text-amber-400"></i>
                        <h2 class="sg-data-title">Exchange Rate Details</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->currencyData)
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">Currency Code</div>
                            <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">
                                {{ $country->currencyData->currency_code }}
                            </div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">Exchange Rate (per USD)</div>
                            <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">
                                {{ number_format($country->currencyData->exchange_rate, 4) }}
                            </div>
                        </div>
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px;">
                            <div style="font-size:12px; color:var(--sg-text-secondary); margin-bottom:8px;">Volatility (Change)</div>
                            <div style="font-size:20px; font-weight:700; color:{{ $country->currencyData->change_percentage >= 0 ? 'var(--sg-success)' : 'var(--sg-danger)' }};">
                                {{ number_format($country->currencyData->change_percentage, 2) }}%
                            </div>
                        </div>
                    </div>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary);">
                        <p>No exchange rate data available</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Port Terminal Infrastructure -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="anchor" class="w-5 h-5 text-indigo-400"></i>
                        <h2 class="sg-data-title">Port Infrastructure terminals</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->ports && $country->ports->count() > 0)
                    <table class="port-table">
                        <thead>
                            <tr>
                                <th>Port Name</th>
                                <th>City</th>
                                <th>Type</th>
                                <th style="text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($country->ports as $port)
                            <tr>
                                <td style="font-weight:600; color:var(--sg-text-primary);">{{ $port->port_name }}</td>
                                <td style="color:var(--sg-text-secondary);">{{ $port->city }}</td>
                                <td style="color:var(--sg-text-muted);">{{ $port->port_type }}</td>
                                <td style="text-align:center;">
                                    <span class="sg-badge {{ $port->status === 'Closed' ? 'critical' : ($port->status === 'Congested' ? 'medium' : 'low') }}">
                                        {{ $port->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary); padding:10px 0;">
                        No maritime terminals recorded for this country.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Risk Score History Chart -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="line-chart" class="w-5 h-5 text-violet-400"></i>
                        <h2 class="sg-data-title">Risk History Trend</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($riskHistory && $riskHistory->count() > 0)
                    <div style="height:250px; position:relative;">
                        <canvas id="riskHistoryChart"></canvas>
                    </div>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary); padding:20px 0;">
                        No historical risk change logs recorded.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Latest News -->
            <div class="sg-data-card">
                <div class="sg-data-head">
                    <div class="sg-data-head-left">
                        <i data-lucide="newspaper" class="w-5 h-5 text-purple-400"></i>
                        <h2 class="sg-data-title">Latest Intelligence Event News</h2>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if($country->news && $country->news->count() > 0)
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($country->news->sortByDesc('published_at')->take(3) as $news)
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; display:flex; gap:12px;">
                            @if($news->image)
                            <img src="{{ $news->image }}" style="width:80px; height:60px; object-fit:cover; border-radius:6px;" loading="lazy">
                            @endif
                            <div style="flex:1;">
                                <h4 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 6px 0; line-height:1.4;">
                                    {{ $news->title }}
                                </h4>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <span class="sg-badge {{ $news->sentiment === 'Positive' ? 'low' : ($news->sentiment === 'Negative' ? 'critical' : 'medium') }}" style="font-size:10px;">{{ $news->sentiment ?? 'Neutral' }} (Imp: {{ $news->impact_score }})</span>
                                    <span style="font-size:11px; color:var(--sg-text-muted);">
                                        {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->diffForHumans() : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center; color:var(--sg-text-secondary);">
                        <p>No news articles available</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @if($country->latitude && $country->longitude)
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('country-map').setView([{{ $country->latitude }}, {{ $country->longitude }}], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $country->latitude }}, {{ $country->longitude }}]).addTo(map)
            .bindPopup('{{ $country->country_name }}')
            .openPopup();
    });
    </script>
    @endif

    @if($riskHistory && $riskHistory->count() > 0)
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const riskCtx = document.getElementById('riskHistoryChart').getContext('2d');
        const historyData = @json($riskHistory->map(fn($h) => ['date' => $h->calculated_at->format('M d H:i'), 'score' => $h->total_score]));
        
        new Chart(riskCtx, {
            type: 'line',
            data: {
                labels: historyData.map(d => d.date),
                datasets: [{
                    label: 'Risk Score',
                    data: historyData.map(d => d.score),
                    borderColor: '{{ $color }}',
                    backgroundColor: 'rgba(255, 255, 255, 0.02)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
    </script>
    @endif
</x-app-layout>
