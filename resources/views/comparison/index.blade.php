<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 mb-6 border-b border-white/5">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i data-lucide="git-compare" class="w-7 h-7 text-violet-400"></i>
                Global Supply Chain Comparison
            </h1>
            <p class="text-sm text-slate-400 mt-2">Compare macroeconomic indicators, weather risks, currency exchange, and news sentiment for 2 to 5 countries.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="sg-flash sg-flash-error">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            Please select between 2 and 5 countries.
        </div>
    @endif

    {{-- Country Selection Panel --}}
    <div class="sg-data-card" x-data="{
        selected: @json($selectedCountries->pluck('id')->toArray()),
        showLimitError: false,
        toggleCountry(id) {
            id = parseInt(id);
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id);
            } else {
                if (this.selected.length < 5) {
                    this.selected.push(id);
                } else {
                    this.showLimitError = true;
                    setTimeout(() => this.showLimitError = false, 3000);
                }
            }
        }
    }">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="globe" class="w-5 h-5 text-violet-400"></i>
                <h2 class="sg-data-title">Select Countries to Compare
                    <span class="sg-count-badge">Choose 2–5 countries</span>
                </h2>
            </div>
            <div x-show="showLimitError" x-transition class="text-xs text-red-400 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                Maximum of 5 countries allowed for comparison
            </div>
        </div>

        <form method="GET" action="{{ route('comparison') }}" id="compare-form">
            <div class="sg-country-selector-grid">
                @foreach($countries as $c)
                    <label class="sg-country-checkbox" :class="selected.includes({{ $c->id }}) ? 'sg-country-checkbox-selected' : ''">
                        <input type="checkbox" name="countries[]" value="{{ $c->id }}"
                            :checked="selected.includes({{ $c->id }})"
                            @change="toggleCountry({{ $c->id }})">
                        @if($c->flag)
                            <img src="{{ $c->flag }}" loading="lazy">
                        @endif
                        <span>{{ $c->country_name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-400" x-text="`Selected: ${selected.length} / 5 countries`"></span>
                <button type="submit" class="sg-btn sg-btn-gradient" :disabled="selected.length < 2">
                    <i data-lucide="git-compare" class="w-4 h-4"></i>
                    Compare Selected
                </button>
            </div>
        </form>
    </div>

    @if($selectedCountries->count() >= 2)
        {{-- Side by Side Comparative Table --}}
        <div class="sg-data-card mb-6">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="table" class="w-5 h-5 text-violet-400"></i>
                    <h2 class="sg-data-title">Indicator Comparison Matrix</h2>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="sg-table" style="width:100%;border-collapse:collapse;min-width:700px">
                    <thead>
                        <tr style="background:rgba(0,0,0,0.2)">
                            <th style="width:160px;padding:12px 20px;font-size:11px;font-weight:700;color:var(--sg-text-secondary);text-transform:uppercase;border-bottom:1px solid var(--sg-border)">Indicator</th>
                            @foreach($selectedCountries as $sc)
                                <th style="padding:12px 20px;text-align:center;border-bottom:1px solid var(--sg-border)">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                                        @if($sc->flag)
                                            <img src="{{ $sc->flag }}" style="width:36px;height:24px;object-fit:cover;border-radius:3px;border:1px solid var(--sg-border);box-shadow:0 1px 2px rgba(0,0,0,0.4)">
                                        @endif
                                        <span style="font-weight:700;color:var(--text-white);font-size:13.5px">{{ $sc->country_name }}</span>
                                        <span style="font-size:11px;color:var(--sg-text-secondary);font-weight:500">{{ $sc->region }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 1. Risk Score & Level --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Risk Score</td>
                            @foreach($selectedCountries as $sc)
                                @php
                                    $score = $sc->riskScore->total_score ?? 0;
                                    $level = $sc->riskScore->risk_level ?? 'Low';
                                    $color = '#10B981';
                                    if ($score >= 76) $color = '#EF4444';
                                    elseif ($score >= 51) $color = '#F97316';
                                    elseif ($score >= 26) $color = '#EAB308';
                                @endphp
                                <td style="text-align:center;padding:16px 20px">
                                    <div style="font-size:22px;font-weight:800;color:{{ $color }}">{{ $score }}<span style="font-size:12px;color:var(--sg-text-secondary);font-weight:400">/100</span></div>
                                    <span class="sg-badge {{ strtolower($level) }}" style="margin-top:4px">{{ $level }} Risk</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- 2. GDP --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">GDP (USD)</td>
                            @foreach($selectedCountries as $sc)
                                @php
                                    $gdpVal = $sc->economicData->gdp ?? null;
                                    if ($gdpVal) {
                                        if ($gdpVal >= 1e12) $gdpStr = '$' . number_format($gdpVal / 1e12, 2) . 'T';
                                        elseif ($gdpVal >= 1e9) $gdpStr = '$' . number_format($gdpVal / 1e9, 2) . 'B';
                                        else $gdpStr = '$' . number_format($gdpVal / 1e6, 2) . 'M';
                                    } else {
                                        $gdpStr = 'N/A';
                                    }
                                @endphp
                                <td style="text-align:center;font-weight:700;color:var(--text-white);font-size:14px">{{ $gdpStr }}</td>
                            @endforeach
                        </tr>

                        {{-- 3. Inflation --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Inflation Rate</td>
                            @foreach($selectedCountries as $sc)
                                @php
                                    $inflation = $sc->economicData->inflation ?? null;
                                    $infStr = $inflation !== null ? number_format($sc->economicData->inflation, 1) . '%' : 'N/A';
                                    $infColor = 'var(--text-white)';
                                    if ($inflation > 10) $infColor = '#EF4444';
                                    elseif ($inflation > 5) $infColor = '#F97316';
                                @endphp
                                <td style="text-align:center;font-weight:700;color:{{ $infColor }}">{{ $infStr }}</td>
                            @endforeach
                        </tr>

                        {{-- 4. Currency exchange --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Currency (vs USD)</td>
                            @foreach($selectedCountries as $sc)
                                @php $cur = $sc->currencyData; @endphp
                                <td style="text-align:center">
                                    @if($cur)
                                        <div style="font-weight:700;color:var(--text-white)">{{ $cur->currency_code }}</div>
                                        <div style="font-size:12px;color:var(--sg-text-secondary);margin-top:2px">Rate: {{ number_format($cur->exchange_rate, 4) }}</div>
                                        <div style="font-size:11px;color:var(--sg-text-muted);margin-top:2px">Buy: {{ number_format($cur->buy, 4) }} · Sell: {{ number_format($cur->sell, 4) }}</div>
                                    @else
                                        <span style="color:var(--sg-text-muted)">N/A</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- 5. Weather --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Weather Details</td>
                            @foreach($selectedCountries as $sc)
                                @php $w = $sc->weatherData; @endphp
                                <td style="text-align:center">
                                    @if($w)
                                        <div style="font-weight:700;color:#2dd4bf;font-size:14px">{{ $w->temperature }}°C</div>
                                        <div style="font-size:12px;color:var(--sg-text-secondary);margin-top:2px">{{ $w->weather_condition }}</div>
                                        <div style="font-size:11px;color:var(--sg-text-secondary);margin-top:2px">Wind: {{ $w->wind_speed }} m/s · Rain: {{ $w->rainfall }} mm</div>
                                        <div style="font-size:11px;color:var(--sg-text-muted);margin-top:2px">Cloud: {{ $w->cloud ?? '—' }}% · Press: {{ $w->pressure ?? '—' }} hPa</div>
                                    @else
                                        <span style="color:var(--sg-text-muted)">N/A</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- 6. Ports count --}}
                        <tr style="border-bottom:1px solid var(--sg-border)">
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Major Ports</td>
                            @foreach($selectedCountries as $sc)
                                <td style="text-align:center">
                                    <div style="font-size:16px;font-weight:700;color:#38bdf8">⚓ {{ $sc->ports_count }}</div>
                                    <span style="font-size:11px;color:var(--sg-text-muted)">Maritime Hubs</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- 7. News Latest --}}
                        <tr>
                            <td style="font-weight:700;color:var(--sg-text-secondary);background:rgba(255,255,255,0.01);border-right:1px solid var(--sg-border);padding:12px 20px">Latest News Event</td>
                            @foreach($selectedCountries as $sc)
                                @php $latest = $sc->news->sortByDesc('published_at')->first(); @endphp
                                <td style="padding:12px 14px;max-width:220px;vertical-align:top">
                                    @if($latest)
                                        <div style="font-size:12px;font-weight:600;color:var(--sg-text-primary);line-height:1.4;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">
                                            {{ $latest->title }}
                                        </div>
                                        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px">
                                            <span style="font-size:10px;color:var(--sg-text-muted)">{{ \Carbon\Carbon::parse($latest->published_at)->format('M d, Y') }}</span>
                                            @if($latest->sentiment === 'Positive')
                                                <span class="sg-sentiment positive">POS</span>
                                            @elseif($latest->sentiment === 'Negative')
                                                <span class="sg-sentiment negative">NEG</span>
                                            @else
                                                <span class="sg-sentiment neutral">NEU</span>
                                            @endif
                                        </div>
                                    @else
                                        <div style="text-align:center;color:var(--sg-text-muted);font-size:12px">No recent events</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Radar Chart Comparison --}}
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="radar" class="w-5 h-5 text-violet-400"></i>
                    <h2 class="sg-data-title">Geopolitical & Operational Risk Profile</h2>
                </div>
            </div>
            <div class="flex justify-center p-6">
                <div class="relative w-full max-w-2xl" style="height:360px">
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const colors = [
                    { border: '#FF6B00', fill: 'rgba(255, 107, 0, 0.15)' },
                    { border: '#0d9488', fill: 'rgba(13, 148, 136, 0.15)' },
                    { border: '#2563EB', fill: 'rgba(37, 99, 235, 0.15)' },
                    { border: '#9333ea', fill: 'rgba(147, 51, 234, 0.15)' },
                    { border: '#e11d48', fill: 'rgba(225, 29, 72, 0.15)' }
                ];

                const ctx = document.getElementById('radarChart').getContext('2d');
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: ['Weather', 'Economy', 'Currency', 'News Sentiment', 'Port Status'],
                        datasets: [
                            @foreach($selectedCountries as $index => $sc)
                            {
                                label: '{{ addslashes($sc->country_name) }}',
                                data: [
                                    {{ $sc->riskScore->weather_score ?? 0 }},
                                    {{ $sc->riskScore->economic_score ?? 0 }},
                                    {{ $sc->riskScore->currency_score ?? 0 }},
                                    {{ $sc->riskScore->news_score ?? 0 }},
                                    {{ $sc->riskScore->port_score ?? 0 }}
                                ],
                                borderColor: colors[{{ $index }} % colors.length].border,
                                backgroundColor: colors[{{ $index }} % colors.length].fill,
                                borderWidth: 2,
                                pointBackgroundColor: colors[{{ $index }} % colors.length].border,
                                pointRadius: 4
                            },
                            @endforeach
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(255,255,255,0.08)' },
                                angleLines: { color: 'rgba(255,255,255,0.12)' },
                                ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: 25, backdropColor: 'transparent' },
                                pointLabels: { font: { size: 12, weight: '600' }, color: '#cbd5e1' }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { font: { size: 12 }, color: '#F8FAFC', usePointStyle: true, pointStyleWidth: 10 }
                            }
                        }
                    }
                });
            });
        </script>

        // Refresh function for post-sync updates
        <script>
        function refreshCompare() {
            window.location.reload();
        }
        </script>

    @else
        {{-- Empty State --}}
        <div class="sg-data-card text-center" style="padding:80px 16px">
            <div class="sg-empty-icon" style="margin-bottom:16px">
                <i data-lucide="git-compare" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Select Countries to Compare</h3>
            <p class="text-slate-400 max-w-lg mx-auto">Choose between 2 and 5 countries from the panel above and click "Compare Selected" to render side-by-side risk matrices and dynamic radar charts.</p>
        </div>
    @endif
</x-app-layout>