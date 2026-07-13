<x-app-layout>
    @php
        $country = $riskScore->country;
        $level = $riskScore->risk_level ?? 'Low';
        $color = match($level) {
            'Critical' => '#ef4444',
            'High' => '#f97316',
            'Medium' => '#eab308',
            default => '#10b981'
        };
        $bgLight = match($level) {
            'Critical' => '#fef2f2',
            'High' => '#fff7ed',
            'Medium' => '#fffbeb',
            default => '#f0fdf4'
        };
    @endphp

    <div class="sg-page-header" style="margin-bottom: 24px;">
        <div class="sg-page-header-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div style="display:flex; align-items:center; gap:16px;">
                @php $iso = strtolower($country->country_code ?? 'un'); @endphp
                <img src="{{ $country->flag ?: 'https://flagcdn.com/w80/'.$iso.'.png' }}"
                     alt="{{ $country->country_name }}"
                     onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                     style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border); box-shadow:var(--sg-shadow-sm);">
                <div>
                    <h1 class="sg-page-title" style="margin:0; font-size:28px;">{{ $country->country_name }} Scorecard</h1>
                    <p class="sg-page-desc" style="margin:4px 0 0 0;">Capital: <strong>{{ $country->capital ?? 'N/A' }}</strong> &bull; Region: <strong>{{ $country->region ?? 'N/A' }}</strong> &bull; Code: <strong>{{ $country->country_code }}</strong></p>
                </div>
            </div>
            <div class="sg-data-actions" style="display:flex; gap:8px;">
                <a href="{{ route('risk-scores.calculate', $country->id) }}" class="sg-btn sg-btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"/></svg>
                    Recalculate Scores
                </a>
                <a href="{{ route('countries.index') }}" class="sg-btn" style="background:#f1f5f9; color:#475569; border-color:#e2e8f0;">Back to List</a>
            </div>
        </div>
    </div>

    <!-- Main Scorecard Grid -->
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:24px; margin-bottom:24px;">
        
        <!-- Left Side: Summary and Sub-Scores -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            
            <!-- Overall Risk Card -->
            <div class="sg-data-card" style="text-align:center; padding:32px 24px; background:{{ $bgLight }}; border-color:{{ $color }}40;">
                <span style="font-size:12px; font-weight:700; text-transform:uppercase; color:#64748b; letter-spacing:0.05em;">Overall Risk Index</span>
                <div style="font-size:64px; font-weight:800; color:{{ $color }}; line-height:1; margin:16px 0 8px 0;">
                    {{ $riskScore->total_score }}<span style="font-size:24px; font-weight:500; color:#64748b;">/100</span>
                </div>
                <div style="display:inline-block; padding:4px 16px; border-radius:20px; font-size:14px; font-weight:700; text-transform:uppercase; background:{{ $color }}; color:#fff; margin-bottom:16px;">
                    {{ $level }}
                </div>
                <div style="border-top:1px solid var(--sg-border); padding-top:16px; text-align:left;">
                    <span style="font-size:12px; font-weight:700; text-transform:uppercase; color:#64748b; display:block; margin-bottom:4px;">Risk Analyst Recommendation:</span>
                    <p style="font-size:13px; color:var(--sg-text-primary); line-height:1.5; margin:0;">
                        {{ $riskScore->recommendation ?: 'No specific warnings issued. Monitor weather updates and port throughput regularly.' }}
                    </p>
                </div>
            </div>

            <!-- Weighted Breakdown Card -->
            <div class="sg-data-card">
                <h3 class="sg-data-title" style="margin-bottom:16px; font-size:16px;">Risk Factor Breakdown</h3>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    
                    <!-- Weather Score (25%) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                            <span style="font-weight:600; color:var(--sg-text-primary);">🌤️ Weather & Climate (25%)</span>
                            <span style="font-weight:700; color:#475569;">{{ $riskScore->weather_score }}/100</span>
                        </div>
                        <div style="width:100%; height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                            <div style="width:{{ $riskScore->weather_score }}%; height:100%; border-radius:4px; background:{{ $riskScore->weather_score >= 76 ? '#ef4444' : ($riskScore->weather_score >= 51 ? '#f97316' : ($riskScore->weather_score >= 26 ? '#eab308' : '#10b981')) }}"></div>
                        </div>
                    </div>

                    <!-- Economy Score (25%) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                            <span style="font-weight:600; color:var(--sg-text-primary);">📊 Macro-Economy (25%)</span>
                            <span style="font-weight:700; color:#475569;">{{ $riskScore->economic_score }}/100</span>
                        </div>
                        <div style="width:100%; height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                            <div style="width:{{ $riskScore->economic_score }}%; height:100%; border-radius:4px; background:{{ $riskScore->economic_score >= 76 ? '#ef4444' : ($riskScore->economic_score >= 51 ? '#f97316' : ($riskScore->economic_score >= 26 ? '#eab308' : '#10b981')) }}"></div>
                        </div>
                    </div>

                    <!-- News Score (25%) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                            <span style="font-weight:600; color:var(--sg-text-primary);">📰 Sentiment & Geopolitics (25%)</span>
                            <span style="font-weight:700; color:#475569;">{{ $riskScore->news_score }}/100</span>
                        </div>
                        <div style="width:100%; height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                            <div style="width:{{ $riskScore->news_score }}%; height:100%; border-radius:4px; background:{{ $riskScore->news_score >= 76 ? '#ef4444' : ($riskScore->news_score >= 51 ? '#f97316' : ($riskScore->news_score >= 26 ? '#eab308' : '#10b981')) }}"></div>
                        </div>
                    </div>

                    <!-- Currency Score (15%) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                            <span style="font-weight:600; color:var(--sg-text-primary);">💸 Sovereign Currency (15%)</span>
                            <span style="font-weight:700; color:#475569;">{{ $riskScore->currency_score }}/100</span>
                        </div>
                        <div style="width:100%; height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                            <div style="width:{{ $riskScore->currency_score }}%; height:100%; border-radius:4px; background:{{ $riskScore->currency_score >= 76 ? '#ef4444' : ($riskScore->currency_score >= 51 ? '#f97316' : ($riskScore->currency_score >= 26 ? '#eab308' : '#10b981')) }}"></div>
                        </div>
                    </div>

                    <!-- Port Score (10%) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:6px;">
                            <span style="font-weight:600; color:var(--sg-text-primary);">⚓ Logistics & Ports (10%)</span>
                            <span style="font-weight:700; color:#475569;">{{ $riskScore->port_score }}/100</span>
                        </div>
                        <div style="width:100%; height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                            <div style="width:{{ $riskScore->port_score }}%; height:100%; border-radius:4px; background:{{ $riskScore->port_score >= 76 ? '#ef4444' : ($riskScore->port_score >= 51 ? '#f97316' : ($riskScore->port_score >= 26 ? '#eab308' : '#10b981')) }}"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Right Side: Details Tabs/Sub-sections -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            
            <!-- Macroeconomics & Sovereign Currency -->
            <div class="sg-data-card">
                <h3 class="sg-data-title" style="margin-bottom:16px;">World Bank Macroeconomic Indicators</h3>
                @if($country->economicData)
                    @php
                        $gdp = $country->economicData->gdp;
                        $gdpStr = $gdp ? '$' . number_format($gdp / 1e9, 2) . ' B' : 'N/A';
                        $growth = $country->economicData->gdp_growth;
                        $growthColor = $growth >= 0 ? '#10b981' : '#ef4444';
                        
                        $balance = $country->economicData->trade_balance;
                        $balanceColor = $balance >= 0 ? '#10b981' : '#ef4444';
                        $balanceStr = $balance ? '$' . number_format($balance / 1e9, 2) . ' B' : 'N/A';
                    @endphp
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Gross Domestic Product (GDP)</div>
                            <div style="font-size:18px; font-weight:700; color:#1e293b; margin-top:4px;">{{ $gdpStr }}</div>
                        </div>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Annual GDP Growth</div>
                            <div style="font-size:18px; font-weight:700; color:{{ $growthColor }}; margin-top:4px;">{{ $growth ? number_format($growth, 2) . '%' : 'N/A' }}</div>
                        </div>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Inflation Rate</div>
                            <div style="font-size:18px; font-weight:700; color:#1e293b; margin-top:4px;">{{ $country->economicData->inflation ? number_format($country->economicData->inflation, 2) . '%' : 'N/A' }}</div>
                        </div>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Trade Balance (Surplus/Deficit)</div>
                            <div style="font-size:18px; font-weight:700; color:{{ $balanceColor }}; margin-top:4px;">{{ $balanceStr }}</div>
                        </div>
                    </div>
                @else
                    <p style="color:#64748b; font-size:13px; text-align:center; padding:24px;">No macroeconomic records found for this country.</p>
                @endif

                <div style="border-top:1px solid var(--sg-border); margin-top:20px; padding-top:20px;">
                    <h4 style="font-size:14px; font-weight:700; color:#1e293b; margin-bottom:12px;">Sovereign Currency Status</h4>
                    @if($country->currencyData)
                        <div style="display:flex; justify-content:space-between; align-items:center; background:#eff6ff; border:1px solid #dbeafe; padding:12px 16px; border-radius:8px;">
                            <div>
                                <span style="font-size:12px; color:#1e40af; font-weight:600;">Sovereign Currency</span>
                                <div style="font-size:18px; font-weight:700; color:#1e3a8a; margin-top:2px;">{{ $country->currencyData->currency_code }}</div>
                            </div>
                            <div style="text-align:right;">
                                <span style="font-size:12px; color:#1e40af; font-weight:600;">Exchange Rate (per USD)</span>
                                <div style="font-size:18px; font-weight:700; color:#1e3a8a; margin-top:2px;">{{ number_format($country->currencyData->exchange_rate, 4) }}</div>
                            </div>
                        </div>
                    @else
                        <p style="color:#64748b; font-size:13px; text-align:center; padding:10px;">No exchange rate data available.</p>
                    @endif
                </div>
            </div>

            <!-- Weather Conditions & Operational Warnings -->
            <div class="sg-data-card">
                <h3 class="sg-data-title" style="margin-bottom:16px;">Weather & Climate Risk</h3>
                @if($country->weatherData)
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:16px;">
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9; text-align:center;">
                            <div style="font-size:11px; color:#64748b; font-weight:600;">TEMPERATURE</div>
                            <div style="font-size:20px; font-weight:700; color:#1e293b; margin-top:4px;">{{ $country->weatherData->temperature }}°C</div>
                        </div>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9; text-align:center;">
                            <div style="font-size:11px; color:#64748b; font-weight:600;">WIND SPEED</div>
                            <div style="font-size:20px; font-weight:700; color:#1e293b; margin-top:4px;">{{ $country->weatherData->wind_speed }} km/h</div>
                        </div>
                        <div style="background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #f1f5f9; text-align:center;">
                            <div style="font-size:11px; color:#64748b; font-weight:600;">CONDITION</div>
                            <div style="font-size:15px; font-weight:700; color:#1e293b; margin-top:8px;">{{ $country->weatherData->weather_condition ?? 'Clear' }}</div>
                        </div>
                    </div>

                    <!-- Operational Weather Alert -->
                    @php
                        $temp = $country->weatherData->temperature;
                        $wind = $country->weatherData->wind_speed;
                        $cond = strtolower($country->weatherData->weather_condition ?? '');
                        
                        $isWarning = $temp < 5 || $temp > 38 || $wind > 50 || str_contains($cond, 'storm') || str_contains($cond, 'rain') || str_contains($cond, 'snow');
                    @endphp

                    @if($isWarning)
                        <div style="background:#fff7ed; border:1px solid #ffedd5; padding:14px; border-radius:8px; display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px;">⚠️</span>
                            <div>
                                <h4 style="font-size:13px; font-weight:700; color:#c2410c; margin:0 0 4px 0;">Extreme Weather Alert</h4>
                                <p style="font-size:12.5px; color:#9a3412; margin:0; line-height:1.4;">
                                    Operational disruption possible. High wind speeds or severe weather condition reported. Logistics delay risks are elevated.
                                </p>
                            </div>
                        </div>
                    @else
                        <div style="background:#f0fdf4; border:1px solid #d1fae5; padding:14px; border-radius:8px; display:flex; gap:10px; align-items:flex-start;">
                            <span style="font-size:18px; color:#16a34a;">✅</span>
                            <div>
                                <h4 style="font-size:13px; font-weight:700; color:#15803d; margin:0 0 4px 0;">Normal Climate Conditions</h4>
                                <p style="font-size:12.5px; color:#166534; margin:0; line-height:1.4;">
                                    Weather parameters indicate favorable environment for seaport throughput and general regional logistics.
                                </p>
                            </div>
                        </div>
                    @endif
                @else
                    <p style="color:#64748b; font-size:13px; text-align:center; padding:24px;">No weather indicators currently synchronized.</p>
                @endif
            </div>

        </div>

    </div>

    <!-- Seaport Logistics & Intelligence Feeds -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Port Log Registry -->
        <div class="sg-data-card">
            <h3 class="sg-data-title" style="margin-bottom:16px;">Maritime Port Congestion & Throughput</h3>
            <div style="overflow-x:auto;">
                <table class="sg-data-table">
                    <thead>
                        <tr>
                            <th>Port / Code</th>
                            <th>City</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($country->ports as $port)
                            @php
                                $portStatus = $port->status ?? 'Active';
                                $statusStyle = match($portStatus) {
                                    'Closed' => 'background:#fef2f2;color:#ef4444;border:1px solid #fee2e2',
                                    'Congested' => 'background:#fffbeb;color:#d97706;border:1px solid #fef3c7',
                                    default => 'background:#f0fdf4;color:#16a34a;border:1px solid #d1fae5'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong style="color:#1e293b;">⚓ {{ $port->port_name }}</strong>
                                    <div style="font-size:10px; color:#94a3b8; font-weight:600; margin-top:2px;">CODE: {{ $port->port_code }}</div>
                                </td>
                                <td style="color:#475569; font-size:13px;">{{ $port->city ?? 'N/A' }}</td>
                                <td>
                                    <span style="display:inline-flex; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700; text-transform:uppercase; {{ $statusStyle }}">{{ $portStatus }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:#64748b; font-size:13px; padding:24px;">
                                    No maritime ports mapped for this country.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- News & Geopolitical Sentiment Timeline -->
        <div class="sg-data-card">
            <h3 class="sg-data-title" style="margin-bottom:16px;">Geopolitical Intelligence Feed</h3>
            <div style="display:flex; flex-direction:column; gap:12px; max-height:400px; overflow-y:auto; padding-right:6px;">
                @forelse($country->news->sortByDesc('published_at') as $item)
                    @php
                        $sent = $item->sentiment ?? 'Neutral';
                        $sentBg = match($sent) {
                            'Negative' => '#fff1f2',
                            'Positive' => '#ecfdf5',
                            default => '#f1f5f9'
                        };
                        $sentText = match($sent) {
                            'Negative' => '#e11d48',
                            'Positive' => '#10b981',
                            default => '#475569'
                        };
                    @endphp
                    <div style="background:#f8fafc; border:1px solid #f1f5f9; padding:12px; border-radius:8px; display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                            <span style="font-size:11px; font-weight:700; color:#64748b;">{{ $item->source ?? 'Global Feed' }}</span>
                            <span style="padding:2px 6px; border-radius:4px; font-size:9px; font-weight:700; text-transform:uppercase; background:{{ $sentBg }}; color:{{ $sentText }};">{{ $sent }}</span>
                        </div>
                        <h4 style="font-size:13px; font-weight:600; color:#1e293b; margin:0; line-height:1.4;">{{ $item->title }}</h4>
                        <div style="font-size:10.5px; color:#94a3b8;">
                            {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : 'Just now' }}
                        </div>
                    </div>
                @empty
                    <p style="color:#64748b; font-size:13px; text-align:center; padding:24px; margin:0;">
                        No geopolitical events or news reports found.
                    </p>
                @endforelse
            </div>
        </div>

    </div>

</x-app-layout>
