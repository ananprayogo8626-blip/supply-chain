<x-app-layout>
@push('head')
<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/minimalist.css') }}?v={{ time() }}">
@endpush

<!-- Minimal Hero Header & Real-Time Status Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between pb-4 mb-6 border-b border-white/[0.08] gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
            <i data-lucide="layout-grid" class="w-5 h-5"></i>
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-white tracking-tight font-outfit">
                    Risk Intelligence Overview
                </h1>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-md">Live</span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5" id="dashboard-date">
                {{ now()->format('l, F j, Y') }} &bull; Monitored Grid Operations
            </p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 shrink-0">
        <!-- View Mode Switcher -->
        <div class="min-mode-switch">
            <a href="{{ route('dashboard') }}" class="min-mode-btn active">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                <span>Minimalist</span>
            </a>
            <a href="{{ route('dashboard.minimalist') }}" class="min-mode-btn">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                <span>Executive Feed</span>
            </a>
        </div>

        <div class="hidden sm:flex px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium rounded-full items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Operational
        </div>
    </div>
</div>

<!-- API Error Alert (Hidden unless failed) -->
<div id="api-error" class="hidden mb-6 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-3 text-rose-400 text-xs">
    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
    <div>
        <h4 class="font-bold uppercase tracking-wider text-[11px]">System Alert</h4>
        <p id="api-error-msg" class="mt-0.5 opacity-90">Failed to connect to backend services.</p>
    </div>
</div>

<!-- 4 Clean Minimal KPI Stat Cards -->
<div class="min-stat-grid">
    <!-- 1. Total Countries -->
    <a href="{{ route('countries.index') }}" class="min-card group" title="View Countries Directory">
        <div class="flex items-center justify-between mb-2">
            <span class="min-card-label">Monitored Countries</span>
            <div class="min-card-icon group-hover:text-blue-400 group-hover:bg-blue-500/10 transition-colors">
                <i data-lucide="globe" class="w-4 h-4"></i>
            </div>
        </div>
        <div>
            <div class="min-card-val" id="stat-countries">{{ number_format($totalCountries) }}</div>
            <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                <span>Sovereign Entities</span>
                <span class="min-badge min-badge-blue">Monitored</span>
            </div>
        </div>
    </a>

    <!-- 2. Active Ports -->
    <a href="{{ route('ports.index') }}" class="min-card group" title="View Maritime Ports">
        <div class="flex items-center justify-between mb-2">
            <span class="min-card-label">Active Ports</span>
            <div class="min-card-icon group-hover:text-indigo-400 group-hover:bg-indigo-500/10 transition-colors">
                <i data-lucide="anchor" class="w-4 h-4"></i>
            </div>
        </div>
        <div>
            <div class="min-card-val" id="stat-ports">{{ number_format($totalPorts) }}</div>
            <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                <span>Maritime Hubs</span>
                <span class="min-badge min-badge-emerald">Tracked</span>
            </div>
        </div>
    </a>

    <!-- 3. Indexed Articles -->
    <a href="{{ route('news.index') }}" class="min-card group" title="View Intelligence News Feed">
        <div class="flex items-center justify-between mb-2">
            <span class="min-card-label">Indexed Articles</span>
            <div class="min-card-icon group-hover:text-amber-400 group-hover:bg-amber-500/10 transition-colors">
                <i data-lucide="newspaper" class="w-4 h-4"></i>
            </div>
        </div>
        <div>
            <div class="min-card-val" id="stat-news">{{ number_format($totalNews) }}</div>
            <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                <span>Risk Feeds</span>
                <span class="min-badge min-badge-amber">Real-Time</span>
            </div>
        </div>
    </a>

    <!-- 4. High Risk Warnings -->
    <a href="{{ route('risk-scores.index') }}" class="min-card group" title="View High Risk Sovereign Alerts">
        <div class="flex items-center justify-between mb-2">
            <span class="min-card-label">High Risk Alerts</span>
            <div class="min-card-icon text-rose-400 bg-rose-500/10 border-rose-500/20 group-hover:scale-105 transition-transform">
                <i data-lucide="shield-alert" class="w-4 h-4"></i>
            </div>
        </div>
        <div>
            <div class="min-card-val text-rose-400" id="stat-high-risk">{{ number_format($criticalRiskCount + $highRiskCount) }}</div>
            <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                <span>Score &ge; 51</span>
                <span class="min-badge min-badge-rose">Action Needed</span>
            </div>
        </div>
    </a>
</div>

<!-- Map & Quick Summary Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- Clean Interactive Map Panel (Span 2) -->
    <div class="xl:col-span-2 min-card p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-2.5 border-b border-white/[0.06]">
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-400"></i>
                    Global Risk & Port Heatmap
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Geolocating risk scores & maritime port hubs</p>
            </div>
            <!-- Minimal Map Legend Pills -->
            <div class="flex items-center gap-2 text-[11px] text-slate-300 bg-slate-900/80 px-3 py-1 rounded-full border border-slate-800 shrink-0">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Crit</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span>High</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Med</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Low</span>
                <span class="flex items-center gap-1">⚓ Ports</span>
            </div>
        </div>
        <div id="map-dashboard"></div>
    </div>

    <!-- Quick Navigation & High Risk Summary (Span 1) -->
    <div class="min-card p-4 flex flex-col justify-between">
        <div>
            <div class="pb-2.5 border-b border-white/[0.06] mb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4 text-amber-400"></i>
                    Highest Risk Sovereign Scores
                </h3>
                <a href="{{ route('risk-scores.index') }}" class="text-xs text-blue-400 hover:underline">View All &rarr;</a>
            </div>
            
            <div class="space-y-2">
                @forelse($topRiskScores->take(4) as $score)
                    <div class="min-list-item">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center font-bold text-xs text-white border border-slate-700 shrink-0">
                                {{ $score->country->iso_code_2 ?? '??' }}
                            </span>
                            <span class="text-xs font-semibold text-white">
                                {{ $score->country->country_name ?? 'Unknown' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold {{ $score->total_score >= 76 ? 'text-rose-400' : 'text-orange-400' }}">
                                {{ number_format($score->total_score, 1) }}
                            </span>
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded border {{ $score->total_score >= 76 ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 'bg-orange-500/10 text-orange-400 border-orange-500/20' }}">
                                {{ $score->risk_level ?? 'High' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-3 text-center">No risk data available</p>
                @endforelse
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-white/[0.06]">
            <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Quick Navigation</h4>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="{{ route('countries.index') }}" class="sg-action-card">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i>
                    <span>Countries</span>
                </a>
                <a href="{{ route('risk-scores.index') }}" class="sg-action-card">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-rose-400 shrink-0"></i>
                    <span>Risk Scores</span>
                </a>
                <a href="{{ route('watchlists.index') }}" class="sg-action-card">
                    <i data-lucide="eye" class="w-3.5 h-3.5 text-cyan-400 shrink-0"></i>
                    <span>Watchlist</span>
                </a>
                <a href="{{ route('news.index') }}" class="sg-action-card">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5 text-orange-400 shrink-0"></i>
                    <span>News Feed</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Chart 1: Risk Level Distribution -->
    <div class="min-card p-4">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/[0.06]">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-4 h-4 text-blue-400"></i>
                Risk Level Distribution
            </h3>
            <span class="text-xs text-slate-400">Sovereign Targets</span>
        </div>
        <div class="h-56 relative">
            <canvas id="riskChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Top Sovereign Risk Index -->
    <div class="min-card p-4">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/[0.06]">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-amber-400"></i>
                Top Risk Score Index
            </h3>
            <a href="{{ route('risk-scores.index') }}" class="text-xs text-blue-400 hover:underline">Details &rarr;</a>
        </div>
        <div class="h-56 relative">
            <canvas id="topRiskChart"></canvas>
        </div>
    </div>
</div>

<!-- Intelligence News Feed Section -->
<div class="min-card p-4">
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/[0.06]">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="rss" class="w-4 h-4 text-orange-400"></i>
            Live Intelligence News Feed
        </h3>
        <a href="{{ route('news.index') }}" class="text-xs text-blue-400 hover:underline">View All Articles &rarr;</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($latestNews->take(6) as $news)
        <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.05] transition-all flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                    {{ $news->category ?? 'News' }}
                </span>
                <a href="{{ $news->url ?? route('news.index') }}" target="_blank" class="block text-xs font-semibold text-slate-200 hover:text-white mt-2 line-clamp-2 transition-colors">
                    {{ $news->title }}
                </a>
            </div>
            <div class="flex items-center justify-between gap-2 mt-3 pt-2 border-t border-white/[0.04] text-[10px] text-slate-400">
                <span>{{ $news->country->country_name ?? 'Global' }}</span>
                <span>{{ $news->published_at?->diffForHumans() ?? 'Recent' }}</span>
            </div>
        </div>
        @empty
        <p class="col-span-3 text-center text-slate-400 py-8 text-xs">No articles available</p>
        @endforelse
    </div>
</div>

<!-- Dashboard Script -->
<script>
(function () {
    const API_URL = '{{ url("/api/dashboard") }}';
    
    function formatNum(n) {
        n = Number(n ?? 0);
        if (n >= 1000) return (n / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
        return n.toLocaleString();
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
    }

    async function loadDashboard() {
        try {
            const res = await fetch(API_URL, { headers: { Accept: 'application/json' } });
            const json = await res.json();
            if (!res.ok || json.status !== 'success') throw new Error(json.message || 'API error');

            const d = json.data;
            renderStats(d.stats);
            
            // Draw charts
            if (typeof drawRiskDistributionChart === 'function') {
                drawRiskDistributionChart(d.riskProfile);
            }
            if (typeof drawTopRisksChart === 'function') {
                drawTopRisksChart(d.topRisks);
            }

            // Initialize Leaflet map
            if (!window.mapInstance && typeof initLeafletMap === 'function') {
                initLeafletMap(d.topRisks, d.activePorts);
            }
        } catch (err) {
            console.warn('Dashboard live update note:', err.message);
        }
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
</x-app-layout>
