<x-app-layout>
@push('head')
<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<!-- Header & Platform Status Grid -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 mb-6 border-b border-white/5">
    <div>
        <h1 class="sg-page-title flex items-center gap-3">
            <i data-lucide="layout-dashboard" class="text-orange-500 w-7 h-7"></i>
            Risk Intelligence Control Center
        </h1>
        <p class="text-xs text-slate-400 mt-2" id="dashboard-date">{{ now()->format('l, F j, Y') }} — Enterprise Live Security Grid</p>
    </div>
    <div class="mt-4 lg:mt-0 flex flex-wrap items-center gap-3">
        <div class="px-3 py-1.5 bg-green-500/10 border border-green-500/25 text-green-400 text-xs font-semibold rounded-md flex items-center gap-2">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
            System Health: Good
        </div>
        <div class="px-3 py-1.5 bg-blue-500/10 border border-blue-500/25 text-blue-400 text-xs font-semibold rounded-md flex items-center gap-2">
            <i data-lucide="activity" class="w-3.5 h-3.5"></i>
            Active APIs: Live Grid
        </div>
        <a href="{{ route('sync.all') }}" class="sg-sync-btn">
            <i data-lucide="refresh-cw" class="w-4 h-4 animate-spin-slow"></i>
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

<!-- KPI Statistics Grid (4 Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6 mb-6 auto-rows-fr">
    <!-- 1. Total Countries -->
    <div class="sg-stat-card h-full">
        <div class="flex items-center justify-between mb-2">
            <span class="sg-stat-label">Total Countries</span>
            <i data-lucide="globe" class="w-4 h-4 text-blue-400"></i>
        </div>
        <div class="sg-stat-row">
            <span class="sg-stat-value" id="stat-countries">{{ number_format($totalCountries) }}</span>
        </div>
        <p class="text-[10px] text-slate-500 mt-1">Monitored sovereign targets</p>
    </div>

    <!-- 2. Active Ports -->
    <div class="sg-stat-card h-full">
        <div class="flex items-center justify-between mb-2">
            <span class="sg-stat-label">Active Ports</span>
            <i data-lucide="anchor" class="w-4 h-4 text-indigo-400"></i>
        </div>
        <div class="sg-stat-row">
            <span class="sg-stat-value" id="stat-ports">{{ number_format($totalPorts) }}</span>
        </div>
        <p class="text-[10px] text-slate-500 mt-1">Global maritime hubs</p>
    </div>

    <!-- 3. Indexed Articles -->
    <div class="sg-stat-card h-full">
        <div class="flex items-center justify-between mb-2">
            <span class="sg-stat-label">Indexed News</span>
            <i data-lucide="newspaper" class="w-4 h-4 text-orange-400"></i>
        </div>
        <div class="sg-stat-row">
            <span class="sg-stat-value" id="stat-news">{{ number_format($totalNews) }}</span>
        </div>
        <p class="text-[10px] text-slate-500 mt-1">Risk-related news feed</p>
    </div>

    <!-- 4. High Risk Warnings -->
    <div class="sg-stat-card h-full" style="border-color: rgba(239, 68, 68, 0.2)">
        <div class="flex items-center justify-between mb-2">
            <span class="sg-stat-label text-red-400">High Risk Warnings</span>
            <i data-lucide="shield-alert" class="w-4 h-4 text-red-500"></i>
        </div>
        <div class="sg-stat-row">
            <span class="sg-stat-value text-red-400" id="stat-high-risk">{{ number_format($criticalRiskCount + $highRiskCount) }}</span>
        </div>
        <p class="text-[10px] text-red-500/80 mt-1">Active risk index &gt;= 51</p>
    </div>
</div>

<!-- Map & System Status Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- Interactive Map (Span 2) -->
    <div class="xl:col-span-2 sg-panel p-6 flex flex-col justify-between">
        <div class="sg-panel-head flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <div>
                <h3 class="sg-panel-title flex items-center gap-2">
                    <i data-lucide="map" class="w-4 h-4 text-blue-400"></i>
                    Interactive Global Risk Map
                </h3>
                <p class="text-[11px] text-slate-400 mt-1">Geolocating critical ports and risk levels across monitored territories.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-[10px] font-semibold text-slate-400">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444] inline-block"></span>Crit</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#F97316] inline-block"></span>High</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EAB308] inline-block"></span>Med</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span>Low</span>
                <span class="flex items-center gap-1">⚓ Ports</span>
            </div>
        </div>
        <div id="map-dashboard" style="height: 380px; width: 100%; border-radius: 12px; border: 1px solid var(--sg-border);"></div>
    </div>

    <!-- Quick Actions & API Status (Span 1) -->
    <div class="sg-panel p-6 flex flex-col justify-between">
        <div>
            <div class="sg-panel-head mb-4">
                <h3 class="sg-panel-title flex items-center gap-2">
                    <i data-lucide="cpu" class="w-4 h-4 text-purple-400"></i>
                    System Health & APIs
                </h3>
            </div>
            <div class="space-y-3" id="api-status-container">
                <p class="text-center text-slate-500 py-6 text-xs">Checking API connectivity...</p>
            </div>
        </div>
        
        <div class="pt-4 border-t border-white/5 mt-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Sync Log Time</span>
                    <span class="text-xs font-extrabold text-white block mt-0.5" id="last-sync-time">
                        {{ $lastSyncTime }}
                    </span>
                </div>
                <div class="w-9 h-9 bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            
            <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block mb-2">Quick Access</span>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('sync.all') }}" class="px-3 py-2 bg-orange-500/20 border border-orange-500/30 hover:bg-orange-500/30 text-orange-400 font-bold rounded-lg text-center text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Sync All
                </a>
                <a href="{{ route('risk-scores.index') }}" class="px-3 py-2 bg-red-500/20 border border-red-500/30 hover:bg-red-500/30 text-red-400 font-bold rounded-lg text-center text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i> Risks
                </a>
                <a href="{{ route('watchlists.index') }}" class="px-3 py-2 bg-cyan-500/20 border border-cyan-500/30 hover:bg-cyan-500/30 text-cyan-400 font-bold rounded-lg text-center text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Watchlist
                </a>
                <a href="{{ route('news.index') }}" class="px-3 py-2 bg-blue-500/20 border border-blue-500/30 hover:bg-blue-500/30 text-blue-400 font-bold rounded-lg text-center text-xs transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i> News Center
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section (2 side-by-side charts) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Chart 1: Risk Level Distribution -->
    <div class="sg-panel p-6">
        <h4 class="text-sm font-bold text-slate-300 mb-4 flex justify-between items-center">
            <span>Risk Level Distribution</span>
            <span class="text-[10px] text-slate-500">World risk segments</span>
        </h4>
        <div class="h-[250px] relative">
            <canvas id="riskChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Top Sovereign Risks -->
    <div class="sg-panel p-6">
        <h4 class="text-sm font-bold text-slate-300 mb-4 flex justify-between items-center">
            <span>Top 5 High Risk Countries</span>
            <span class="text-[10px] text-slate-500">Sovereign risk indexes</span>
        </h4>
        <div class="h-[250px] relative">
            <canvas id="topRiskChart"></canvas>
        </div>
    </div>
</div>

<!-- Data Summary Grid (3 tables side-by-side) -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- 1. Top Sovereign Risks -->
    <div class="sg-panel flex flex-col h-[400px]">
        <div class="sg-panel-head flex items-center gap-2 pb-3 mb-2 border-b border-white/5">
            <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-ping"></span>
            <h3 class="sg-panel-title">Top Sovereign Risks</h3>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-white/5">
                        <th class="pb-2">Country</th>
                        <th class="pb-2 text-center">Score</th>
                        <th class="pb-2 text-center">Level</th>
                    </tr>
                </thead>
                <tbody id="top-risks-body" class="divide-y divide-white/5">
                    <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading risk entities...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Active Ports Status -->
    <div class="sg-panel flex flex-col h-[400px]">
        <div class="sg-panel-head flex items-center gap-2 pb-3 mb-2 border-b border-white/5">
            <i data-lucide="anchor" class="w-4 h-4 text-indigo-400"></i>
            <h3 class="sg-panel-title">Active Ports Status</h3>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-white/5">
                        <th class="pb-2">Port</th>
                        <th class="pb-2">Location</th>
                        <th class="pb-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="active-ports-body" class="divide-y divide-white/5">
                    <tr><td colspan="3" class="text-center py-20 text-slate-500">Loading ports...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3. Latest News Feed -->
    <div class="sg-panel flex flex-col h-[400px]">
        <div class="sg-panel-head flex items-center gap-2 pb-3 mb-2 border-b border-white/5">
            <i data-lucide="newspaper" class="w-4 h-4 text-orange-500"></i>
            <h3 class="sg-panel-title">Latest News & Sentiment</h3>
        </div>
        <div class="overflow-y-auto flex-1 space-y-3 pr-1 pt-1" id="news-feed-container">
            @forelse($latestNews->take(4) as $news)
            <div class="p-3 bg-white/5 border border-white/5 rounded-xl hover:bg-white/8 transition-colors">
                <a href="{{ $news->url }}" target="_blank" class="text-xs font-semibold text-white hover:text-orange-400 line-clamp-2 block">{{ $news->title }}</a>
                <div class="flex items-center justify-between gap-2 mt-2">
                    <span class="text-[10px] text-slate-400">{{ $news->country->country_name ?? 'Global' }}</span>
                    <div class="flex items-center gap-2">
                        @if($news->sentiment === 'Negative')
                        <span class="px-2 py-0.5 bg-red-500/10 text-red-400 text-[9px] font-bold rounded">Negative</span>
                        @elseif($news->sentiment === 'Positive')
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 text-[9px] font-bold rounded">Positive</span>
                        @else
                        <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-400 text-[9px] font-bold rounded">Neutral</span>
                        @endif
                        <span class="text-[9px] text-slate-500">{{ $news->published_at?->diffForHumans() ?? 'Unknown' }}</span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-slate-500 py-20 text-xs">No news articles available</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Page Script -->
<script>
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

    function renderStats(s) {
        if (!s) return;
        
        const setEl = (id, val) => { 
            const el = document.getElementById(id); 
            if (el) el.textContent = formatNum(val); 
        };
        
        setEl('stat-countries', s.totalCountries);
        setEl('stat-ports', s.totalPorts);
        setEl('stat-news', s.totalArticles);
        setEl('stat-high-risk', Number(s.criticalRisk || 0) + Number(s.highRisk || 0));
        
        const lastSyncEl = document.getElementById('last-sync-time');
        if (lastSyncEl) lastSyncEl.textContent = s.lastSyncStr || '—';
    }

    async function loadDashboard() {
        try {
            const res = await fetch(API_URL, { headers: { Accept: 'application/json' } });
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'API error');

            const d = json.data;
            renderStats(d.stats);
            
            // Draw simplified charts
            if (typeof drawRiskDistributionChart === 'function') {
                drawRiskDistributionChart(d.riskProfile);
            }
            if (typeof drawTopRisksChart === 'function') {
                drawTopRisksChart(d.topRisks);
            }
            
            // Draw list/tables
            if (typeof drawLists === 'function') {
                drawLists(d);
            }
            if (typeof drawApiStatus === 'function') {
                drawApiStatus(d.apiStatus);
            }

            // Initialize map once
            if (!window.mapInstance && typeof initLeafletMap === 'function') {
                initLeafletMap(d.topRisks, d.activePorts);
            }
        } catch (err) {
            const errMsg = document.getElementById('api-error-msg');
            const errBox = document.getElementById('api-error');
            if (errMsg) errMsg.textContent = 'Gagal memuat data control center: ' + err.message;
            if (errBox) errBox.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
    setInterval(loadDashboard, 5000);
})();
</script>
</x-app-layout>
