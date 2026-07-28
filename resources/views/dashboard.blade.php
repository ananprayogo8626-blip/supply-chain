<x-app-layout>
@push('head')
<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<!-- Clean Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-white/5 gap-3">
    <div>
        <h1 class="sg-page-title flex items-center gap-2.5 text-xl font-bold text-white tracking-tight">
            <i data-lucide="shield-alert" class="text-orange-500 w-6 h-6"></i>
            Global Risk Intelligence Dashboard
        </h1>
        <p class="text-xs text-slate-400 mt-1" id="dashboard-date">{{ now()->format('l, F j, Y') }} — Live Multi-API Operations</p>
    </div>
    <div class="flex items-center gap-2.5">
        <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold rounded-lg flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            System Operational
        </span>
    </div>
</div>

<!-- API Connection Error Box -->
<div id="api-error" class="hidden mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center gap-3 text-red-400 text-xs">
    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
    <span id="api-error-msg">Gagal menghubungkan ke service API.</span>
</div>

<!-- KPI Metrics Grid (4 Clean Cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- 1. Total Countries -->
    <div class="sg-panel p-5 flex items-center justify-between">
        <div>
            <span class="text-xs font-medium text-slate-400 block mb-1">Total Negara</span>
            <span class="text-2xl font-bold text-white tracking-tight" id="stat-countries">{{ number_format($totalCountries) }}</span>
            <span class="text-[11px] text-slate-500 block mt-1">Negara dipantau</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <i data-lucide="globe" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- 2. Active Ports -->
    <div class="sg-panel p-5 flex items-center justify-between">
        <div>
            <span class="text-xs font-medium text-slate-400 block mb-1">Pelabuhan Aktif</span>
            <span class="text-2xl font-bold text-white tracking-tight" id="stat-ports">{{ number_format($totalPorts) }}</span>
            <span class="text-[11px] text-slate-500 block mt-1">Hub logistik maritim</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
            <i data-lucide="anchor" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- 3. Indexed News -->
    <div class="sg-panel p-5 flex items-center justify-between">
        <div>
            <span class="text-xs font-medium text-slate-400 block mb-1">Berita Terindeks</span>
            <span class="text-2xl font-bold text-white tracking-tight" id="stat-news">{{ number_format($totalNews) }}</span>
            <span class="text-[11px] text-slate-500 block mt-1">Artikel intelijen</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <i data-lucide="newspaper" class="w-5 h-5"></i>
        </div>
    </div>

    <!-- 4. High Risk Warnings -->
    <div class="sg-panel p-5 flex items-center justify-between border-red-500/20">
        <div>
            <span class="text-xs font-medium text-red-400 block mb-1">Peringatan Risiko Tinggi</span>
            <span class="text-2xl font-bold text-red-400 tracking-tight" id="stat-high-risk">{{ number_format($criticalRiskCount + $highRiskCount) }}</span>
            <span class="text-[11px] text-red-400/70 block mt-1">Skor risiko &ge; 51</span>
        </div>
        <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400">
            <i data-lucide="shield-alert" class="w-5 h-5"></i>
        </div>
    </div>
</div>

<!-- Main Section: Global Risk Map + Risk Distribution Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Interactive Risk Terrain Map (Span 2) -->
    <div class="lg:col-span-2 sg-panel p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="map" class="w-4 h-4 text-sky-400"></i>
                    Peta Risiko Global Interaktif
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Visualisasi geolokasi indikator risiko negara dan pelabuhan dunia.</p>
            </div>
            <div class="flex items-center gap-2.5 text-[10px] font-semibold text-slate-400">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444] inline-block"></span>Critical</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#F97316] inline-block"></span>High</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#EAB308] inline-block"></span>Medium</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-[#10B981] inline-block"></span>Low</span>
            </div>
        </div>
        <div id="map-dashboard" style="height: 380px; width: 100%; border-radius: 10px; border: 1px solid var(--sg-border);"></div>
    </div>

    <!-- Right Column: Risk Distribution Chart & Quick Access -->
    <div class="flex flex-col gap-6">
        <!-- Risk Distribution Chart -->
        <div class="sg-panel p-5 flex-1 flex flex-col justify-between">
            <h3 class="text-sm font-bold text-white flex items-center justify-between mb-3">
                <span>Distribusi Segmentasi Risiko</span>
                <span class="text-[10px] text-slate-500 font-normal">Global</span>
            </h3>
            <div class="h-[200px] relative my-auto">
                <canvas id="riskChart"></canvas>
            </div>
        </div>

        <!-- Quick Access Menu -->
        <div class="sg-panel p-5">
            <span class="text-xs font-bold text-slate-400 block mb-3 uppercase tracking-wider">Akses Cepat</span>
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('countries.index') }}" class="px-3 py-2.5 bg-slate-800/80 hover:bg-slate-700/80 border border-white/5 rounded-lg text-xs font-medium text-slate-200 flex items-center gap-2 transition">
                    <i data-lucide="globe" class="w-4 h-4 text-sky-400"></i> Negara
                </a>
                <a href="{{ route('risk-scores.index') }}" class="px-3 py-2.5 bg-slate-800/80 hover:bg-slate-700/80 border border-white/5 rounded-lg text-xs font-medium text-slate-200 flex items-center gap-2 transition">
                    <i data-lucide="shield" class="w-4 h-4 text-red-400"></i> Skor Risiko
                </a>
                <a href="{{ route('ports.index') }}" class="px-3 py-2.5 bg-slate-800/80 hover:bg-slate-700/80 border border-white/5 rounded-lg text-xs font-medium text-slate-200 flex items-center gap-2 transition">
                    <i data-lucide="anchor" class="w-4 h-4 text-indigo-400"></i> Pelabuhan
                </a>
                <a href="{{ route('news.index') }}" class="px-3 py-2.5 bg-slate-800/80 hover:bg-slate-700/80 border border-white/5 rounded-lg text-xs font-medium text-slate-200 flex items-center gap-2 transition">
                    <i data-lucide="newspaper" class="w-4 h-4 text-amber-400"></i> Berita & Event
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Top High Risk Countries + Latest News & Sentiment -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- 1. Top High Risk Countries Table -->
    <div class="sg-panel p-5 flex flex-col h-[380px]">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/5">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                Daftar Negara Risiko Tertinggi
            </h3>
            <a href="{{ route('risk-scores.index') }}" class="text-xs text-sky-400 hover:underline">Lihat Semua &rarr;</a>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-white/5">
                        <th class="pb-2">Negara</th>
                        <th class="pb-2 text-center">Skor Risiko</th>
                        <th class="pb-2 text-center">Tingkat Risiko</th>
                    </tr>
                </thead>
                <tbody id="top-risks-body" class="divide-y divide-white/5">
                    <tr><td colspan="3" class="text-center py-16 text-slate-500">Memuat data risiko...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Latest News & Sentiment -->
    <div class="sg-panel p-5 flex flex-col h-[380px]">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/5">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i data-lucide="newspaper" class="w-4 h-4 text-amber-400"></i>
                Berita & Intelijen Sentimen Terkini
            </h3>
            <a href="{{ route('news.index') }}" class="text-xs text-sky-400 hover:underline">Lihat Berita &rarr;</a>
        </div>
        <div class="overflow-y-auto flex-1 space-y-3 pr-1" id="news-feed-container">
            @forelse($latestNews->take(4) as $news)
            <div class="p-3 bg-white/5 border border-white/5 rounded-lg hover:bg-white/10 transition-colors">
                <a href="{{ $news->url }}" target="_blank" class="text-xs font-semibold text-white hover:text-amber-400 line-clamp-2 block">{{ $news->title }}</a>
                <div class="flex items-center justify-between gap-2 mt-2">
                    <span class="text-[10px] text-slate-400">{{ $news->country->country_name ?? 'Global' }}</span>
                    <div class="flex items-center gap-2">
                        @if($news->sentiment === 'Negative')
                        <span class="px-2 py-0.5 bg-red-500/10 text-red-400 text-[9px] font-bold rounded">Negative</span>
                        @elseif($news->sentiment === 'Positive')
                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[9px] font-bold rounded">Positive</span>
                        @else
                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-400 text-[9px] font-bold rounded">Neutral</span>
                        @endif
                        <span class="text-[9px] text-slate-500">{{ $news->published_at?->diffForHumans() ?? 'Baru saja' }}</span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-slate-500 py-16 text-xs">Belum ada berita terindeks</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Hidden Canvas for topRiskChart compatibility -->
<div class="hidden"><canvas id="topRiskChart"></canvas></div>

<!-- Dashboard Page Script -->
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
            
            // Draw simplified chart
            if (typeof drawRiskDistributionChart === 'function') {
                drawRiskDistributionChart(d.riskProfile);
            }
            
            // Draw list/tables
            if (typeof drawLists === 'function') {
                drawLists(d);
            }

            // Initialize Leaflet map once
            if (!window.mapInstance && typeof initLeafletMap === 'function') {
                initLeafletMap(d.topRisks, d.activePorts);
            }
        } catch (err) {
            const errMsg = document.getElementById('api-error-msg');
            const errBox = document.getElementById('api-error');
            if (errMsg) errMsg.textContent = 'Gagal memuat data dashboard: ' + err.message;
            if (errBox) errBox.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
</x-app-layout>
