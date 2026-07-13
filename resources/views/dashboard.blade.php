<x-app-layout>
    @push('head')
        <!-- Leaflet Map CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush

    <!-- Header & Platform Status Grid -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-4 mb-5 border-b border-white/5">
        <div>
            <h1 class="sg-page-title flex items-center gap-2">
                <i data-lucide="layout-dashboard" class="text-orange-500 w-6 h-6"></i>
                Risk Intelligence Control Center
            </h1>
            <p class="text-xs text-slate-400 mt-1" id="dashboard-date">{{ now()->format('l, F j, Y') }} — Enterprise Live Security Grid</p>
        </div>
        <div class="mt-3 lg:mt-0 flex flex-wrap items-center gap-3">
            <div class="px-2.5 py-1 bg-green-500/10 border border-green-500/25 text-green-400 text-xs font-semibold rounded-md flex items-center gap-1.5">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                System Health: Good
            </div>
            <div class="px-2.5 py-1 bg-blue-500/10 border border-blue-500/25 text-blue-400 text-xs font-semibold rounded-md flex items-center gap-1.5">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                Active APIs: 4/4
            </div>
            <a href="{{ route('sync.all') }}" class="sg-sync-btn">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin-slow"></i>
                Sync All APIs
            </a>
        </div>
    </div>

    <!-- Error State -->
    <div id="api-error" class="hidden mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-3 text-red-400">
        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <div>
            <h4 class="text-sm font-bold">API Connection Error</h4>
            <p id="api-error-msg" class="text-xs mt-0.5 opacity-80">Failed to connect to backend services.</p>
        </div>
    </div>

    <!-- KPI Statistics Row (6 Cards) -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
        <!-- 1. Total Countries -->
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">Monitored Countries</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-countries">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend up">
                        <i data-lucide="check" class="w-3 h-3"></i> Stable
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                <i data-lucide="database" class="w-3 h-3"></i> 100% DB Coverage
            </p>
        </div>

        <!-- 2. Total Ports -->
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">World Ports</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-ports">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend up">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Active
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                <i data-lucide="anchor" class="w-3 h-3"></i> Core maritime hubs
            </p>
        </div>

        <!-- 3. News Articles -->
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">News Indices</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-news">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend down" style="color:var(--accent-orange)">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> Alerts
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                <i data-lucide="newspaper" class="w-3 h-3"></i> Sentiments evaluated
            </p>
        </div>

        <!-- 4. Weather Metrics -->
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">Weather Stations</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-weather">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend up" style="color:var(--accent-blue)">
                        <i data-lucide="cloud-rain" class="w-3 h-3"></i> Synced
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                <i data-lucide="navigation" class="w-3 h-3"></i> Coordinates monitored
            </p>
        </div>

        <!-- 5. Watchlist Tracker -->
        <div class="sg-stat-card">
            <div>
                <span class="sg-stat-label">Watchlists</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-watchlists">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend up" style="color:var(--sg-success)">
                        <i data-lucide="eye" class="w-3 h-3"></i> Tracking
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                <i data-lucide="crosshair" class="w-3 h-3"></i> Priority targets
            </p>
        </div>

        <!-- 6. High Risk Entities -->
        <div class="sg-stat-card" style="border-color:rgba(239,68,68,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-danger)">High Risk Targets</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" id="stat-highrisk" style="color:var(--sg-danger)">
                        <span class="sg-skeleton w-16 h-8"></span>
                    </span>
                    <span class="sg-stat-trend down" style="color:var(--sg-danger)">
                        <i data-lucide="alert-octagon" class="w-3 h-3"></i> Warnings
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-red-400 mt-1 flex items-center gap-1">
                <i data-lucide="shield-alert" class="w-3 h-3"></i> Risk Score &ge; 51
            </p>
        </div>
    </div>

    <!-- Map & Risk Status Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">
        <!-- Interactive Map -->
        <div class="xl:col-span-2 sg-panel sg-glass">
            <div class="sg-panel-head">
                <div>
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="map" class="w-4 h-4 text-accent-blue" style="color:var(--accent-blue)"></i>
                        Interactive Global Risk Map
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1">Geolocating critical nodes, maritime port status, and sovereign risk tiers.</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-400">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444] inline-block"></span>Crit</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#F97316] inline-block"></span>High</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EAB308] inline-block"></span>Med</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span>Low</span>
                    <span class="flex items-center gap-1">⚓ Ports</span>
                </div>
            </div>
            <div id="map-dashboard" style="height: 380px; width: 100%; border-radius: 8px; border: 1px solid var(--sg-border);"></div>
        </div>

        <!-- System Health & Live API Status -->
        <div class="sg-panel sg-glass flex flex-col justify-between">
            <div>
                <div class="sg-panel-head">
                    <h3 class="sg-panel-title">System Health & Live API Status</h3>
                </div>
                <div class="space-y-2.5">
                    <div class="p-2.5 bg-white/5 border border-white/5 rounded-lg flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-300">REST Countries API</span>
                        <span class="px-2 py-0.5 bg-green-500/10 border border-green-500/25 text-green-400 font-bold rounded text-[9px]">ACTIVE</span>
                    </div>
                    <div class="p-2.5 bg-white/5 border border-white/5 rounded-lg flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-300">Open-Meteo Weather API</span>
                        <span class="px-2 py-0.5 bg-green-500/10 border border-green-500/25 text-green-400 font-bold rounded text-[9px]">ACTIVE</span>
                    </div>
                    <div class="p-2.5 bg-white/5 border border-white/5 rounded-lg flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-300">World Bank Indicators API</span>
                        <span class="px-2 py-0.5 bg-green-500/10 border border-green-500/25 text-green-400 font-bold rounded text-[9px]">ACTIVE</span>
                    </div>
                    <div class="p-2.5 bg-white/5 border border-white/5 rounded-lg flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-300">ExchangeRate FX Gateway</span>
                        <span class="px-2 py-0.5 bg-green-500/10 border border-green-500/25 text-green-400 font-bold rounded text-[9px]">ACTIVE</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/5 mt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Sync Log Time</span>
                        <span class="text-sm font-extrabold text-white block mt-0.5" id="last-sync-time">
                            <span class="sg-skeleton w-24 h-4"></span>
                        </span>
                    </div>
                    <div class="w-10 h-10 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 animate-pulse"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Tabbed Grid -->
    <div class="bg-white/5 border border-white/5 rounded-2xl overflow-hidden mb-5" x-data="{ chartTab: 'risk' }">
        <!-- Tabs bar -->
        <div class="flex flex-wrap border-b border-white/5 bg-black/20 px-3 pt-2">
            <button @click="chartTab = 'risk'; $nextTick(() => triggerChartResize());" :class="{ 'border-orange-500 text-orange-500 bg-white/5 font-bold': chartTab === 'risk', 'border-transparent text-slate-400 hover:text-white': chartTab !== 'risk' }" class="px-4 py-2.5 border-b-2 font-semibold text-[11px] uppercase tracking-wider transition-all focus:outline-none flex items-center gap-1.5 rounded-t-lg">
                <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                Risk & Sentiment
            </button>
            <button @click="chartTab = 'macro'; $nextTick(() => triggerChartResize());" :class="{ 'border-orange-500 text-orange-500 bg-white/5 font-bold': chartTab === 'macro', 'border-transparent text-slate-400 hover:text-white': chartTab !== 'macro' }" class="px-4 py-2.5 border-b-2 font-semibold text-[11px] uppercase tracking-wider transition-all focus:outline-none flex items-center gap-1.5 rounded-t-lg">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                Macroeconomics
            </button>
            <button @click="chartTab = 'financial'; $nextTick(() => triggerChartResize());" :class="{ 'border-orange-500 text-orange-500 bg-white/5 font-bold': chartTab === 'financial', 'border-transparent text-slate-400 hover:text-white': chartTab !== 'financial' }" class="px-4 py-2.5 border-b-2 font-semibold text-[11px] uppercase tracking-wider transition-all focus:outline-none flex items-center gap-1.5 rounded-t-lg">
                <i data-lucide="dollar-sign" class="w-3.5 h-3.5"></i>
                Financial Markets
            </button>
            <button @click="chartTab = 'weather'; $nextTick(() => triggerChartResize());" :class="{ 'border-orange-500 text-orange-500 bg-white/5 font-bold': chartTab === 'weather', 'border-transparent text-slate-400 hover:text-white': chartTab !== 'weather' }" class="px-4 py-2.5 border-b-2 font-semibold text-[11px] uppercase tracking-wider transition-all focus:outline-none flex items-center gap-1.5 rounded-t-lg">
                <i data-lucide="cloud-sun" class="w-3.5 h-3.5"></i>
                Weather & Logistics
            </button>
        </div>

        <!-- Tab contents -->
        <div class="p-4">
            <!-- 1. Risk & Sentiment Tab -->
            <div x-show="chartTab === 'risk'" class="grid grid-cols-1 lg:grid-cols-3 gap-4 animate-fade-in">
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3 flex justify-between items-center">
                        Risk Distribution
                        <span class="text-[10px] text-slate-500">World risk segments</span>
                    </h4>
                    <div class="h-[260px] relative">
                        <canvas id="riskChart"></canvas>
                    </div>
                    <div class="flex justify-around mt-3 flex-wrap text-[10px] text-slate-400">
                        <div><span class="inline-block w-2.5 h-2.5 bg-green-500 rounded-full mr-1"></span><span id="leg-low">Low: —</span></div>
                        <div><span class="inline-block w-2.5 h-2.5 bg-amber-500 rounded-full mr-1"></span><span id="leg-med">Med: —</span></div>
                        <div><span class="inline-block w-2.5 h-2.5 bg-orange-500 rounded-full mr-1"></span><span id="leg-high">High: —</span></div>
                        <div><span class="inline-block w-2.5 h-2.5 bg-red-500 rounded-full mr-1"></span><span id="leg-crit">Crit: —</span></div>
                    </div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Top 10 Volatile Risk Scores</h4>
                    <div class="h-[290px]">
                        <canvas id="topRiskChart"></canvas>
                    </div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Global News Sentiment Ratio</h4>
                    <div class="h-[290px]">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 2. Macroeconomics Tab -->
            <div x-show="chartTab === 'macro'" class="grid grid-cols-1 lg:grid-cols-3 gap-4 animate-fade-in" style="display:none">
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Top 10 GDP Growth Countries (%)</h4>
                    <div class="h-[290px]"><canvas id="gdpChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Top 10 Sovereign Inflation Rates (%)</h4>
                    <div class="h-[290px]"><canvas id="inflationChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Top 10 Sovereign GDP Values ($ Billions)</h4>
                    <div class="h-[290px]"><canvas id="topGdpChart"></canvas></div>
                </div>
            </div>

            <!-- 3. Financial Markets Tab -->
            <div x-show="chartTab === 'financial'" class="grid grid-cols-1 lg:grid-cols-3 gap-4 animate-fade-in" style="display:none">
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Key Currency Rates (vs USD)</h4>
                    <div class="h-[290px]"><canvas id="currencyChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Exchange Rate Market Overview</h4>
                    <div class="h-[290px]"><canvas id="exchangeRateChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3.5 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Exchange Volatility Rank (Change %)</h4>
                    <div class="h-[290px]"><canvas id="topCurrencyChart"></canvas></div>
                </div>
            </div>

            <!-- 4. Weather & Logistics Tab -->
            <div x-show="chartTab === 'weather'" class="grid grid-cols-1 lg:grid-cols-4 gap-4 animate-fade-in" style="display:none">
                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Temperature Peaks (°C)</h4>
                    <div class="h-[260px]"><canvas id="weatherChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Relative Humidity (%)</h4>
                    <div class="h-[260px]"><canvas id="humidityChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Sovereign Storm Risk Indices</h4>
                    <div class="h-[260px]"><canvas id="weatherTrendChart"></canvas></div>
                </div>
                <div class="bg-white/5 p-3 rounded-xl border border-white/5">
                    <h4 class="text-xs font-bold text-slate-300 mb-3">Wind Speed Extremes (m/s)</h4>
                    <div class="h-[260px]"><canvas id="topWeatherChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Intelligence Registry Grid (10 List Sections) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">
        <!-- Left Column -->
        <div class="space-y-5">
            <!-- 1. Top High Risk Countries -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[400px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-red-600 rounded-full animate-ping"></span>
                        Top High Risk Sovereign Targets
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Country</th>
                                <th class="px-3 py-2 text-center">Score</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="top-risks-body" class="divide-y divide-white/5">
                            <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading risk entities...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Top Safe Countries -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[320px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Top Safe Sovereign Targets
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Country</th>
                                <th class="px-3 py-2 text-center">Risk Score</th>
                                <th class="px-3 py-2 text-center">Level</th>
                            </tr>
                        </thead>
                        <tbody id="top-safe-body" class="divide-y divide-white/5">
                            <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading safe entities...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 8. API Status Gateway -->
            <div class="sg-panel sg-glass p-5">
                <h3 class="sg-panel-title flex items-center gap-1.5 mb-4">
                    <i data-lucide="zap" class="w-4 h-4 text-orange-500"></i>
                    API Integrations Gateway Status
                </h3>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-white/5 border border-white/5 rounded-xl flex items-center justify-between">
                        <span class="font-medium text-slate-300">REST Countries</span>
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 font-bold rounded text-[10px]">ACTIVE</span>
                    </div>
                    <div class="p-3 bg-white/5 border border-white/5 rounded-xl flex items-center justify-between">
                        <span class="font-medium text-slate-300">Open-Meteo</span>
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 font-bold rounded text-[10px]">ACTIVE</span>
                    </div>
                    <div class="p-3 bg-white/5 border border-white/5 rounded-xl flex items-center justify-between">
                        <span class="font-medium text-slate-300">World Bank</span>
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 font-bold rounded text-[10px]">ACTIVE</span>
                    </div>
                    <div class="p-3 bg-white/5 border border-white/5 rounded-xl flex items-center justify-between">
                        <span class="font-medium text-slate-300">ExchangeRate</span>
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 font-bold rounded text-[10px]">ACTIVE</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column -->
        <div class="space-y-5">
            <!-- 4. Latest News Feed -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[400px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="newspaper" class="w-4 h-4 text-orange-500"></i>
                        Supply Intelligence Event Feed
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1 p-4 space-y-4" id="news-feed-container">
                    <p class="text-center text-slate-500 py-20 text-xs">Loading supply chain event feed...</p>
                </div>
            </div>

            <!-- 3. Recent Weather Alerts -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[320px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="cloud-lightning" class="w-4 h-4 text-cyan-400"></i>
                        Critical Weather Alerts
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Location</th>
                                <th class="px-3 py-2">Condition</th>
                                <th class="px-3 py-2 text-center">Storm Risk</th>
                            </tr>
                        </thead>
                        <tbody id="weather-alerts-body" class="divide-y divide-white/5">
                            <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading weather registries...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 9. Last Synchronization -->
            <div class="sg-panel sg-glass p-5 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Database Last Synchronization</h4>
                    <span class="text-sm font-extrabold text-white block mt-1" id="last-sync-time-2">Just now</span>
                </div>
                <div class="w-10 h-10 bg-orange-500/10 text-orange-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-5">
            <!-- 6. Most Active Maritime Ports -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[400px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="anchor" class="w-4 h-4 text-orange-500"></i>
                        Global Maritime Operations Status
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Port Name</th>
                                <th class="px-3 py-2">City</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="active-ports-body" class="divide-y divide-white/5">
                            <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading ports status...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. Latest Currency Updates -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[320px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="dollar-sign" class="w-4 h-4 text-orange-500"></i>
                        Forex Fluctuations (vs USD)
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Currency</th>
                                <th class="px-3 py-2">Symbol</th>
                                <th class="px-3 py-2">Rate</th>
                                <th class="px-3 py-2 text-center">Change %</th>
                            </tr>
                        </thead>
                        <tbody id="currency-updates-body" class="divide-y divide-white/5">
                            <tr><td colspan="4" class="text-center py-20 text-slate-500">Loading forex values...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 7. World Economic Overview -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[400px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="trending-up" class="w-4 h-4 text-green-400"></i>
                        World Economic Panorama
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Country</th>
                                <th class="px-3 py-2">GDP</th>
                                <th class="px-3 py-2 text-center">Growth</th>
                                <th class="px-3 py-2 text-center">Inflation</th>
                                <th class="px-3 py-2 text-center">Year</th>
                            </tr>
                        </thead>
                        <tbody id="economic-overview-body" class="divide-y divide-white/5">
                            <tr><td colspan="5" class="text-center py-20 text-slate-500">Loading economic data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 10. Recent Watchlist -->
            <div class="sg-panel sg-glass overflow-hidden flex flex-col h-[320px]">
                <div class="sg-panel-head bg-black/20">
                    <h3 class="sg-panel-title flex items-center gap-1.5">
                        <i data-lucide="crosshair" class="w-4 h-4 text-orange-500"></i>
                        Watchlist Intelligence
                    </h3>
                </div>
                <div class="overflow-y-auto flex-1">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-black/30 text-slate-500 font-bold sticky top-0 z-10 border-b border-white/5">
                                <th class="px-4 py-2">Entity</th>
                                <th class="px-3 py-2">Country</th>
                                <th class="px-3 py-2 text-center">Priority</th>
                                <th class="px-3 py-2 text-center">Risk</th>
                            </tr>
                        </thead>
                        <tbody id="recent-watchlist-body" class="divide-y divide-white/5">
                            <tr><td colspan="4" class="text-center py-16 text-slate-500">Querying watchlists...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Script -->
    <script>
        let riskChartInst = null;
        let topRiskChartInst = null;
        let sentimentChartInst = null;
        let gdpChartInst = null;
        let inflationChartInst = null;
        let topGdpChartInst = null;
        let currencyChartInst = null;
        let exchangeRateChartInst = null;
        let topCurrencyChartInst = null;
        let weatherChartInst = null;
        let humidityChartInst = null;
        let weatherTrendChartInst = null;
        let topWeatherChartInst = null;
        let mapInstance = null;

        function triggerChartResize() {
            setTimeout(() => {
                [riskChartInst, topRiskChartInst, sentimentChartInst, gdpChartInst, inflationChartInst, topGdpChartInst, currencyChartInst, exchangeRateChartInst, topCurrencyChartInst, weatherChartInst, humidityChartInst, weatherTrendChartInst, topWeatherChartInst].forEach(c => { if (c) c.resize(); });
            }, 80);
        }
        window.addEventListener('resize', triggerChartResize);

        (function () {
            const API_URL = '{{ url("/api/dashboard") }}';

            function formatNum(n) {
                n = Number(n ?? 0);
                if (n >= 1000) return (n / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
                return n.toLocaleString();
            }

            function riskBadge(level) {
                const map = {
                    Critical: 'sg-badge critical',
                    High: 'sg-badge high',
                    Medium: 'sg-badge medium',
                    Low: 'sg-badge low'
                };
                return `<span class="${map[level] || 'sg-badge'}">${level || 'N/A'}</span>`;
            }

            function getWeatherIcon(condition) {
                condition = (condition || '').toLowerCase();
                if (condition.includes('rain') || condition.includes('drizzle') || condition.includes('shower'))
                    return '<i data-lucide="cloud-drizzle" class="w-4 h-4 text-blue-400 inline-block align-middle mr-1"></i>';
                if (condition.includes('storm') || condition.includes('thunder'))
                    return '<i data-lucide="cloud-lightning" class="w-4 h-4 text-yellow-400 inline-block align-middle mr-1"></i>';
                if (condition.includes('snow') || condition.includes('sleet') || condition.includes('ice'))
                    return '<i data-lucide="cloud-snow" class="w-4 h-4 text-blue-200 inline-block align-middle mr-1"></i>';
                if (condition.includes('cloud') || condition.includes('overcast') || condition.includes('fog') || condition.includes('mist'))
                    return '<i data-lucide="cloud" class="w-4 h-4 text-slate-400 inline-block align-middle mr-1"></i>';
                return '<i data-lucide="sun" class="w-4 h-4 text-amber-400 inline-block align-middle mr-1"></i>';
            }

            function getTempClass(temp) {
                temp = parseFloat(temp || 0);
                if (temp >= 32) return 'text-red-400 font-bold';
                if (temp >= 25) return 'text-orange-400 font-semibold';
                if (temp >= 15) return 'text-emerald-400';
                return 'text-sky-300';
            }

            function getSparkline(change) {
                const up = parseFloat(change || 0) >= 0;
                let sparkHtml = '<span class="sg-sparkline">';
                for (let i = 1; i <= 6; i++) {
                    let h = 4 + (i * 2) + (Math.random() * 4);
                    if (!up) h = 18 - (i * 2) - (Math.random() * 4);
                    sparkHtml += `<span class="sg-sparkline-bar" style="height:${Math.max(4, Math.min(16, h))}px; background:${up ? 'var(--sg-success)' : 'var(--sg-danger)'}"></span>`;
                }
                sparkHtml += '</span>';
                return sparkHtml;
            }

            function getRiskBarClass(score) {
                if (score >= 76) return 'risk-critical';
                if (score >= 51) return 'risk-high';
                if (score >= 26) return 'risk-medium';
                return 'risk-low';
            }

            function renderStats(s) {
                if (!s) return;
                document.getElementById('stat-countries').textContent = formatNum(s.totalCountries);
                document.getElementById('stat-ports').textContent = formatNum(s.totalPorts);
                document.getElementById('stat-news').textContent = formatNum(s.totalArticles);
                document.getElementById('stat-weather').textContent = formatNum(s.totalWeather);
                document.getElementById('stat-watchlists').textContent = formatNum(s.totalWatchlists);
                document.getElementById('stat-highrisk').textContent = formatNum(s.highRiskEntities);
            }

            function createChartGradient(ctx, colorStart, colorEnd) {
                let gradient = ctx.createLinearGradient(0, 0, 0, 220);
                gradient.addColorStop(0, colorStart);
                gradient.addColorStop(1, colorEnd);
                return gradient;
            }

            // Chart defaults for dark theme
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = 'Inter, sans-serif';

            function drawRiskCharts(p, topRisks, sentiment) {
                p = p || { low: 0, medium: 0, high: 0, critical: 0 };
                sentiment = sentiment || { positive: 0, neutral: 0, negative: 0 };
                topRisks = topRisks || [];

                document.getElementById('leg-low').textContent = `Low: ${p.low}%`;
                document.getElementById('leg-med').textContent = `Med: ${p.medium}%`;
                document.getElementById('leg-high').textContent = `High: ${p.high}%`;
                document.getElementById('leg-crit').textContent = `Crit: ${p.critical}%`;

                // Risk Doughnut
                const ctxRisk = document.getElementById('riskChart').getContext('2d');
                if (riskChartInst) riskChartInst.destroy();
                riskChartInst = new Chart(ctxRisk, {
                    type: 'doughnut',
                    data: {
                        labels: ['Low', 'Medium', 'High', 'Critical'],
                        datasets: [{ data: [p.low, p.medium, p.high, p.critical], backgroundColor: ['#10B981', '#F59E0B', '#F97316', '#EF4444'], borderWidth: 0 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
                });

                // Top Risk Bar
                const labelsTR = topRisks.length > 0 ? topRisks.map(x => x.country_name) : ['No Data'];
                const valuesTR = topRisks.length > 0 ? topRisks.map(x => x.score) : [0];
                const ctxTR = document.getElementById('topRiskChart').getContext('2d');
                if (topRiskChartInst) topRiskChartInst.destroy();
                let gradTR = createChartGradient(ctxTR, 'rgba(239, 68, 68, 0.8)', 'rgba(239, 68, 68, 0.1)');
                topRiskChartInst = new Chart(ctxTR, {
                    type: 'bar',
                    data: { labels: labelsTR, datasets: [{ label: 'Risk Score', data: valuesTR, backgroundColor: gradTR, borderColor: '#EF4444', borderWidth: 1, borderRadius: 4 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { ticks: { font: { size: 9 } }, grid: { display: false } } }
                    }
                });

                // Sentiment Doughnut
                const ctxSent = document.getElementById('sentimentChart').getContext('2d');
                if (sentimentChartInst) sentimentChartInst.destroy();
                sentimentChartInst = new Chart(ctxSent, {
                    type: 'doughnut',
                    data: { labels: ['Positive', 'Neutral', 'Negative'], datasets: [{ data: [sentiment.positive, sentiment.neutral, sentiment.negative], backgroundColor: ['#10B981', '#94A3B8', '#EF4444'], borderWidth: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: true, position: 'bottom' } } }
                });
            }

            function drawMacroCharts(gdpGrowth, inflationData, topGdpData) {
                gdpGrowth = gdpGrowth || []; inflationData = inflationData || []; topGdpData = topGdpData || [];
                const makeSafe = (arr) => arr.length > 0 ? arr : [{ label: 'No Data', value: 0 }];
                gdpGrowth = makeSafe(gdpGrowth); inflationData = makeSafe(inflationData); topGdpData = makeSafe(topGdpData);

                const ctxGDP = document.getElementById('gdpChart').getContext('2d');
                if (gdpChartInst) gdpChartInst.destroy();
                let gradGDP = createChartGradient(ctxGDP, 'rgba(37, 99, 235, 0.8)', 'rgba(37, 99, 235, 0.1)');
                gdpChartInst = new Chart(ctxGDP, { type: 'bar', data: { labels: gdpGrowth.map(d => d.label), datasets: [{ label: 'Growth %', data: gdpGrowth.map(d => d.value), backgroundColor: gradGDP, borderColor: '#2563EB', borderWidth: 1, borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxInf = document.getElementById('inflationChart').getContext('2d');
                if (inflationChartInst) inflationChartInst.destroy();
                let gradInf = createChartGradient(ctxInf, 'rgba(124, 58, 237, 0.8)', 'rgba(124, 58, 237, 0.1)');
                inflationChartInst = new Chart(ctxInf, { type: 'bar', data: { labels: inflationData.map(d => d.label), datasets: [{ label: 'Inflation %', data: inflationData.map(d => d.value), backgroundColor: gradInf, borderColor: '#7C3AED', borderWidth: 1, borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxTGDP = document.getElementById('topGdpChart').getContext('2d');
                if (topGdpChartInst) topGdpChartInst.destroy();
                let gradTGDP = createChartGradient(ctxTGDP, 'rgba(16, 185, 129, 0.8)', 'rgba(16, 185, 129, 0.1)');
                topGdpChartInst = new Chart(ctxTGDP, { type: 'bar', data: { labels: topGdpData.map(d => d.label), datasets: [{ label: 'GDP (Billions)', data: topGdpData.map(d => d.value), backgroundColor: gradTGDP, borderColor: '#10B981', borderWidth: 1, borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });
            }

            function drawFinancialCharts(currencyTrend, topCurrencyData) {
                currencyTrend = currencyTrend || []; topCurrencyData = topCurrencyData || [];
                const makeSafe = (arr) => arr.length > 0 ? arr : [{ label: 'No Data', value: 0 }];
                currencyTrend = makeSafe(currencyTrend); topCurrencyData = makeSafe(topCurrencyData);

                const ctxCT = document.getElementById('currencyChart').getContext('2d');
                if (currencyChartInst) currencyChartInst.destroy();
                let gradCT = createChartGradient(ctxCT, 'rgba(245, 158, 11, 0.8)', 'rgba(245, 158, 11, 0.1)');
                currencyChartInst = new Chart(ctxCT, { type: 'bar', data: { labels: currencyTrend.map(d => d.label), datasets: [{ label: 'USD Rate', data: currencyTrend.map(d => d.value), backgroundColor: gradCT, borderColor: '#F59E0B', borderWidth: 1, borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxER = document.getElementById('exchangeRateChart').getContext('2d');
                if (exchangeRateChartInst) exchangeRateChartInst.destroy();
                exchangeRateChartInst = new Chart(ctxER, { type: 'line', data: { labels: currencyTrend.map(d => d.label), datasets: [{ label: 'FX Indices', data: currencyTrend.map(d => d.value), borderColor: '#3B82F6', borderWidth: 2, backgroundColor: 'rgba(59,130,246,0.05)', fill: true, tension: 0.3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxTC = document.getElementById('topCurrencyChart').getContext('2d');
                if (topCurrencyChartInst) topCurrencyChartInst.destroy();
                let gradTC = createChartGradient(ctxTC, 'rgba(239, 68, 68, 0.8)', 'rgba(239, 68, 68, 0.1)');
                topCurrencyChartInst = new Chart(ctxTC, { type: 'bar', data: { labels: topCurrencyData.map(d => d.label), datasets: [{ label: 'Volatility Change %', data: topCurrencyData.map(d => d.value), backgroundColor: gradTC, borderColor: '#EF4444', borderWidth: 1, borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });
            }

            function drawWeatherCharts(weatherOverview, tempData, humidityData, weatherTrend, topWeatherData) {
                tempData = tempData || []; humidityData = humidityData || []; weatherTrend = weatherTrend || []; topWeatherData = topWeatherData || [];
                const makeSafe = (arr) => arr.length > 0 ? arr : [{ label: 'No Data', value: 0 }];
                tempData = makeSafe(tempData); humidityData = makeSafe(humidityData); weatherTrend = makeSafe(weatherTrend); topWeatherData = makeSafe(topWeatherData);

                const ctxW = document.getElementById('weatherChart').getContext('2d');
                if (weatherChartInst) weatherChartInst.destroy();
                let gradW = createChartGradient(ctxW, 'rgba(13, 148, 136, 0.8)', 'rgba(13, 148, 136, 0.1)');
                weatherChartInst = new Chart(ctxW, { type: 'bar', data: { labels: tempData.map(d => d.label), datasets: [{ label: 'Temp °C', data: tempData.map(d => d.value), backgroundColor: gradW, borderColor: '#0D9488', borderWidth: 1, borderRadius: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxH = document.getElementById('humidityChart').getContext('2d');
                if (humidityChartInst) humidityChartInst.destroy();
                let gradH = createChartGradient(ctxH, 'rgba(6, 182, 212, 0.8)', 'rgba(6, 182, 212, 0.1)');
                humidityChartInst = new Chart(ctxH, { type: 'bar', data: { labels: humidityData.map(d => d.label), datasets: [{ label: 'Humidity %', data: humidityData.map(d => d.value), backgroundColor: gradH, borderColor: '#06B6D4', borderWidth: 1, borderRadius: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxT = document.getElementById('weatherTrendChart').getContext('2d');
                if (weatherTrendChartInst) weatherTrendChartInst.destroy();
                weatherTrendChartInst = new Chart(ctxT, { type: 'line', data: { labels: weatherTrend.map(d => d.label), datasets: [{ label: 'Storm Indices', data: weatherTrend.map(d => d.value), borderColor: '#F59E0B', borderWidth: 2, backgroundColor: 'rgba(245,158,11,0.05)', fill: true }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });

                const ctxWS = document.getElementById('topWeatherChart').getContext('2d');
                if (topWeatherChartInst) topWeatherChartInst.destroy();
                let gradWS = createChartGradient(ctxWS, 'rgba(63, 81, 181, 0.8)', 'rgba(63, 81, 181, 0.1)');
                topWeatherChartInst = new Chart(ctxWS, { type: 'bar', data: { labels: topWeatherData.map(d => d.label), datasets: [{ label: 'Wind m/s', data: topWeatherData.map(d => d.value), backgroundColor: gradWS, borderColor: '#3F51B5', borderWidth: 1, borderRadius: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } } } });
            }

            function drawLists(d) {
                // 1. Top High Risk
                const tbodyTR = document.getElementById('top-risks-body');
                if (!d.topRisks || d.topRisks.length === 0) {
                    tbodyTR.innerHTML = `<tr><td colspan="3" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyTR.innerHTML = d.topRisks.map(r => `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2.5 font-bold text-slate-200 flex items-center gap-2">
                                ${r.flag ? `<img src="${r.flag}" class="w-5 h-3 object-cover rounded border border-white/5">` : ''}
                                ${r.country_name}
                            </td>
                            <td class="px-3 py-2.5 text-center font-extrabold text-red-400">${r.score}</td>
                            <td class="px-3 py-2.5 text-center">${riskBadge(r.level)}</td>
                        </tr>
                    `).join('');
                }

                // 2. Top Safe
                const tbodySafe = document.getElementById('top-safe-body');
                if (!d.topSafe || d.topSafe.length === 0) {
                    tbodySafe.innerHTML = `<tr><td colspan="3" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodySafe.innerHTML = d.topSafe.map(r => `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2 flex items-center gap-2">
                                ${r.flag ? `<img src="${r.flag}" class="w-5 h-3 object-cover rounded border border-white/5">` : ''}
                                <span class="font-bold text-slate-200">${r.country_name}</span>
                            </td>
                            <td class="px-3 py-2 text-center text-slate-400 font-semibold">${r.score}</td>
                            <td class="px-3 py-2 text-center">${riskBadge(r.level)}</td>
                        </tr>
                    `).join('');
                }

                // 3. Weather Alerts
                const tbodyWeather = document.getElementById('weather-alerts-body');
                if (!d.weatherAlerts || d.weatherAlerts.length === 0) {
                    tbodyWeather.innerHTML = `<tr><td colspan="3" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyWeather.innerHTML = d.weatherAlerts.map(w => `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2.5 flex items-center gap-2">
                                ${w.flag ? `<img src="${w.flag}" class="w-5 h-3 object-cover rounded border border-white/5">` : ''}
                                <span class="font-bold text-slate-200">${w.country_name || 'N/A'}</span>
                            </td>
                            <td class="px-3 py-2.5">
                                ${getWeatherIcon(w.condition)}
                                <span class="${getTempClass(w.temp)}">${Number(w.temp ?? 0).toFixed(1)}°C</span>,
                                <span class="text-slate-400 font-medium">${w.condition || 'N/A'}</span>
                            </td>
                            <td class="px-3 py-2.5 text-center font-extrabold text-orange-400">${formatNum(w.storm_risk)}%</td>
                        </tr>
                    `).join('');
                }

                // 4. News Feed
                const feedContainer = document.getElementById('news-feed-container');
                if (!d.latestNewsList || d.latestNewsList.length === 0) {
                    feedContainer.innerHTML = `<p class="text-center text-slate-500 py-16 text-xs">No data available</p>`;
                } else {
                    feedContainer.innerHTML = d.latestNewsList.map(n => {
                        let sentClass = 'sg-sentiment neutral';
                        if (n.sentiment === 'Negative') sentClass = 'sg-sentiment negative';
                        else if (n.sentiment === 'Positive') sentClass = 'sg-sentiment positive';
                        return `
                            <div class="p-3 bg-white/5 border border-white/5 hover:border-white/10 rounded-xl transition-all">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-semibold">
                                        ${n.flag ? `<img src="${n.flag}" class="w-4 h-2.5 object-cover rounded-sm">` : ''}
                                        ${n.country_name || 'Global'}
                                    </div>
                                    <span class="${sentClass}">${n.sentiment || 'Neutral'} (Imp: ${formatNum(n.impact_score)})</span>
                                </div>
                                <a href="${n.url || '#'}" target="_blank" class="text-xs font-bold text-slate-200 hover:text-orange-400 transition-colors block leading-snug">${n.title || 'N/A'}</a>
                                <span class="text-[9px] text-slate-500 block mt-1.5">${n.published_at || '—'}</span>
                            </div>
                        `;
                    }).join('');
                }

                // 5. Currency Updates
                const tbodyCurrency = document.getElementById('currency-updates-body');
                if (!d.latestCurrencyUpdate || d.latestCurrencyUpdate.length === 0) {
                    tbodyCurrency.innerHTML = `<tr><td colspan="4" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyCurrency.innerHTML = d.latestCurrencyUpdate.map(c => {
                        let pctColor = 'text-green-400 font-bold';
                        let arrow = '↑';
                        const changeVal = Number(c.change_percentage ?? 0);
                        if (changeVal < 0) { pctColor = 'text-red-400 font-bold'; arrow = '↓'; }
                        else if (changeVal === 0) { pctColor = 'text-slate-500'; arrow = '→'; }
                        return `
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2.5 font-bold text-slate-200">${c.currency_code || 'N/A'} <span class="text-[10px] text-slate-500 font-normal">(${c.country_name || 'N/A'})</span></td>
                                <td class="px-3 py-2.5 text-slate-400">${c.symbol || ''}</td>
                                <td class="px-3 py-2.5 font-bold text-slate-300">${Number(c.exchange_rate ?? 0).toFixed(2)}</td>
                                <td class="px-3 py-2.5 text-center ${pctColor} flex items-center justify-center gap-1.5">
                                    <span>${arrow} ${Math.abs(changeVal).toFixed(2)}%</span>
                                    ${getSparkline(changeVal)}
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                // 6. Maritime Ports
                const tbodyPorts = document.getElementById('active-ports-body');
                if (!d.activePorts || d.activePorts.length === 0) {
                    tbodyPorts.innerHTML = `<tr><td colspan="3" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyPorts.innerHTML = d.activePorts.map(p => {
                        let badgeCls = 'sg-port-operational';
                        if (p.status === 'Closed') badgeCls = 'sg-port-closed';
                        else if (p.status === 'Congested') badgeCls = 'sg-port-congested';
                        return `
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2.5 font-bold text-slate-200" title="${p.type || ''}">${p.port_name || 'N/A'}</td>
                                <td class="px-3 py-2.5 text-slate-400 font-medium">${p.city || 'N/A'} (${p.country_name || 'N/A'})</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="sg-port-status ${badgeCls}">
                                        <span class="sg-port-status-dot"></span>
                                        <span style="font-size:10px;text-transform:uppercase">${p.status || 'Active'}</span>
                                    </span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }

                // 7. World Economic Overview
                const tbodyEcon = document.getElementById('economic-overview-body');
                if (!d.economicOverview || d.economicOverview.length === 0) {
                    tbodyEcon.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyEcon.innerHTML = d.economicOverview.map(e => `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2.5 font-bold text-slate-200 flex items-center gap-2">
                                ${e.flag ? `<img src="${e.flag}" class="w-5 h-3 object-cover rounded border border-white/5">` : ''}
                                <span>${e.country_name || 'N/A'}</span>
                            </td>
                            <td class="px-3 py-2.5 text-slate-300 font-bold">$${(Number(e.gdp ?? 0) / 1e9).toFixed(1)}B</td>
                            <td class="px-3 py-2.5 text-center font-bold ${Number(e.gdp_growth ?? 0) >= 0 ? 'text-green-400' : 'text-red-400'}">
                                ${Number(e.gdp_growth ?? 0) >= 0 ? '+' : ''}${Number(e.gdp_growth ?? 0).toFixed(1)}%
                            </td>
                            <td class="px-3 py-2.5 text-center text-orange-400 font-bold">${Number(e.inflation ?? 0).toFixed(1)}%</td>
                            <td class="px-3 py-2.5 text-center text-slate-500 font-semibold">${e.data_year || '—'}</td>
                        </tr>
                    `).join('');
                }

                // 10. Watchlist
                const tbodyWatch = document.getElementById('recent-watchlist-body');
                if (!d.recentWatchlist || d.recentWatchlist.length === 0) {
                    tbodyWatch.innerHTML = `<tr><td colspan="4" class="text-center py-10 text-slate-500">No data available</td></tr>`;
                } else {
                    tbodyWatch.innerHTML = d.recentWatchlist.map(w => `
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-2.5 font-bold text-slate-200 flex items-center gap-2">
                                ${w.flag ? `<img src="${w.flag}" class="w-4 h-2.5 object-cover rounded border border-white/5">` : ''}
                                <span>${w.company_name || 'N/A'}</span>
                            </td>
                            <td class="px-3 py-2.5 text-slate-400">${w.country_name || 'N/A'}</td>
                            <td class="px-3 py-2.5 text-center font-bold text-slate-300">P${w.priority || 1}</td>
                            <td class="px-3 py-2.5 text-center">
                                <div class="sg-risk-meter-wrap">
                                    <div class="sg-risk-bar-bg">
                                        <div class="sg-risk-bar-fill ${getRiskBarClass(w.risk_level === 'Critical' ? 90 : (w.risk_level === 'High' ? 60 : (w.risk_level === 'Medium' ? 40 : 10)))}" style="width:${w.risk_level === 'Critical' ? 90 : (w.risk_level === 'High' ? 60 : (w.risk_level === 'Medium' ? 40 : 10))}%"></div>
                                    </div>
                                    <span style="font-size:10px">${w.risk_level || 'Low'}</span>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                }

                // Last sync time
                document.getElementById('last-sync-time').textContent = d.lastSyncStr || 'Never';
                const ls2 = document.getElementById('last-sync-time-2');
                if (ls2) ls2.textContent = d.lastSyncStr || 'Never';

                // Compile icons generated dynamically
                lucide.createIcons();
            }

            function initLeafletMap(risks, ports) {
                risks = risks || [];
                ports = ports || [];

                if (mapInstance) mapInstance.remove();
                mapInstance = L.map('map-dashboard').setView([15.0, 10.0], 2);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(mapInstance);

                risks.forEach(function(c) {
                    if (!c.lat || !c.lng) return;
                    let color = '#10B981';
                    if (c.score >= 76) color = '#EF4444';
                    else if (c.score >= 51) color = '#F97316';
                    else if (c.score >= 26) color = '#EAB308';

                    const circle = L.circle([c.lat, c.lng], { color: color, fillColor: color, fillOpacity: 0.2, weight: 1.5, radius: 350000 }).addTo(mapInstance);
                    circle.bindPopup(`
                        <div style="font-family: Inter, sans-serif; width: 220px; color:#f8fafc">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                ${c.flag ? `<img src="${c.flag}" style="width:24px;height:14px;object-fit:cover;border-radius:2px;border:1px solid rgba(255,255,255,0.1);" />` : ''}
                                <strong style="font-size: 13px; color: #fff;">${c.country_name || 'N/A'}</strong>
                            </div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">GDP: <strong style="color: #f8fafc;">${c.gdp || 'N/A'}</strong></div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Weather: <strong style="color: #f8fafc;">${c.weather || 'N/A'}</strong></div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Currency: <strong style="color: #f8fafc;">${c.currency || 'N/A'}</strong></div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 8px;">Latest Event: <strong style="color: #cbd5e1;">${c.news || 'N/A'}</strong></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.08);">
                                <span style="font-size: 12px; font-weight: bold; color: #fff;">Score: ${formatNum(c.score)}/100</span>
                                <span style="font-size: 9px; font-weight: bold; background: ${color}20; color: ${color}; border: 1px solid ${color}; border-radius: 4px; padding: 1.5px 6px; text-transform: uppercase;">${c.level || 'Low'}</span>
                            </div>
                        </div>
                    `);
                });

                const portIcon = L.divIcon({ className: '', html: '<div style="font-size: 16px; text-shadow: 0 0 4px rgba(56,189,248,0.6);">⚓</div>', iconSize: [20, 20], iconAnchor: [10, 20], popupAnchor: [0, -20] });
                ports.forEach(function(p) {
                    if (!p.lat || !p.lng) return;
                    const marker = L.marker([p.lat, p.lng], { icon: portIcon }).addTo(mapInstance);
                    marker.bindPopup(`
                        <div style="font-family: Inter, sans-serif; width: 180px; color:#f8fafc">
                            <div style="font-size: 13px; font-weight: bold; color: #38bdf8; margin-bottom: 5px;">⚓ ${p.port_name || 'N/A'}</div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">City: <strong>${p.city || 'N/A'}</strong></div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 2px;">Country: <strong>${p.country_name || 'N/A'}</strong></div>
                            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 6px;">Code: <strong>${p.type || 'N/A'}</strong></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.08);">
                                <span style="font-size: 9px; font-weight: bold; background: rgba(56,189,248,0.1); color: #38bdf8; border-radius: 4px; padding: 1.5px 5px;">${p.type || 'Hub'}</span>
                                <span style="font-size: 9px; font-weight: bold; color: ${p.status === 'Active' ? '#10B981' : '#EF4444'};">${p.status || 'Active'}</span>
                            </div>
                        </div>
                    `);
                });
            }

            async function loadDashboard() {
                try {
                    const res = await fetch(API_URL, { headers: { Accept: 'application/json' } });
                    const json = await res.json();
                    if (!res.ok || json.status !== 'success') throw new Error(json.message || 'API error');

                    const d = json.data;
                    renderStats(d.stats);
                    drawRiskCharts(d.riskProfile, d.topRisks, d.newsSentiment);
                    drawMacroCharts(d.gdpGrowth, d.inflationData, d.topGdpData);
                    drawFinancialCharts(d.currencyTrend, d.topCurrencyData);
                    drawWeatherCharts(d.weatherOverview, d.tempData, d.humidityData, d.weatherTrend, d.topWeatherData);
                    drawLists(d);
                    initLeafletMap(d.topRisks, d.activePorts);

                    const badge = document.getElementById('notif-badge');
                    if (badge && d.alertCount > 0) {
                        badge.textContent = d.alertCount > 9 ? '9+' : d.alertCount;
                        badge.classList.remove('hidden');
                    }
                } catch (err) {
                    document.getElementById('api-error-msg').textContent = 'Gagal memuat data control center: ' + err.message;
                    document.getElementById('api-error').classList.remove('hidden');
                }
            }

            document.addEventListener('DOMContentLoaded', loadDashboard);
        })();
    </script>
</x-app-layout>
