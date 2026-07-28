<x-app-layout>
@push('head')
<link rel="stylesheet" href="{{ asset('css/minimalist.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<div class="min-container">
    <!-- Header with View Switcher -->
    <div class="min-header">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="min-title">
                    <i data-lucide="layout-grid" class="w-6 h-6 text-blue-400"></i>
                    SupplyGuard Overview
                </h1>
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-md">Minimalist</span>
            </div>
            <p class="min-subtitle">
                Executive summary of live supply chain risk intelligence &bull; Updated {{ $lastSyncTime }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="min-mode-switch">
                <a href="{{ route('dashboard') }}" class="min-mode-btn">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                    <span>Control Center</span>
                </a>
                <a href="{{ route('dashboard.minimalist') }}" class="min-mode-btn active">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span>Minimalist</span>
                </a>
            </div>

            <!-- Live Status -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Operational
            </div>
        </div>
    </div>

    <!-- 4 Essential Stat Cards -->
    <div class="min-stat-grid">
        <!-- 1. Monitored Countries -->
        <a href="{{ route('countries.index') }}" class="min-card group">
            <div class="flex items-center justify-between mb-2">
                <span class="min-card-label">Monitored Countries</span>
                <div class="min-card-icon group-hover:text-blue-400 group-hover:bg-blue-500/10 transition-colors">
                    <i data-lucide="globe" class="w-4.5 h-4.5"></i>
                </div>
            </div>
            <div>
                <div class="min-card-val">{{ number_format($totalCountries) }}</div>
                <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                    <span>Sovereign Entities</span>
                    <span class="min-badge min-badge-blue">Active</span>
                </div>
            </div>
        </a>

        <!-- 2. Maritime Ports -->
        <a href="{{ route('ports.index') }}" class="min-card group">
            <div class="flex items-center justify-between mb-2">
                <span class="min-card-label">Active Ports</span>
                <div class="min-card-icon group-hover:text-indigo-400 group-hover:bg-indigo-500/10 transition-colors">
                    <i data-lucide="anchor" class="w-4.5 h-4.5"></i>
                </div>
            </div>
            <div>
                <div class="min-card-val">{{ number_format($totalPorts) }}</div>
                <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                    <span>Maritime Hubs</span>
                    <span class="min-badge min-badge-emerald">Tracked</span>
                </div>
            </div>
        </a>

        <!-- 3. Intelligence Articles -->
        <a href="{{ route('news.index') }}" class="min-card group">
            <div class="flex items-center justify-between mb-2">
                <span class="min-card-label">Intelligence News</span>
                <div class="min-card-icon group-hover:text-amber-400 group-hover:bg-amber-500/10 transition-colors">
                    <i data-lucide="newspaper" class="w-4.5 h-4.5"></i>
                </div>
            </div>
            <div>
                <div class="min-card-val">{{ number_format($totalNews) }}</div>
                <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                    <span>Indexed Feeds</span>
                    <span class="min-badge min-badge-amber">Live</span>
                </div>
            </div>
        </a>

        <!-- 4. High Risk Warnings -->
        <a href="{{ route('risk-scores.index') }}" class="min-card group">
            <div class="flex items-center justify-between mb-2">
                <span class="min-card-label">High Risk Alerts</span>
                <div class="min-card-icon text-rose-400 bg-rose-500/10 border-rose-500/20 group-hover:scale-105 transition-transform">
                    <i data-lucide="shield-alert" class="w-4.5 h-4.5"></i>
                </div>
            </div>
            <div>
                <div class="min-card-val text-rose-400">{{ number_format($criticalRiskCount + $highRiskCount) }}</div>
                <div class="flex items-center justify-between mt-3 text-xs text-slate-400">
                    <span>Critical & High Risk</span>
                    <span class="min-badge min-badge-rose">Action Needed</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Left Panel: Risk Breakdown & High Risk Targets (Span 2) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Risk Spectrum Bar -->
            <div class="min-card">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06] mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-blue-400"></i>
                            Global Risk Level Spectrum
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Distribution across sovereign risk classifications</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-300 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/50">
                        Avg Risk: {{ $averageRisk }}/100
                    </span>
                </div>

                @php
                    $totalRiskCount = max(1, $criticalRiskCount + $highRiskCount + $mediumRiskCount + $lowRiskCount);
                    $critPct = round(($criticalRiskCount / $totalRiskCount) * 100);
                    $highPct = round(($highRiskCount / $totalRiskCount) * 100);
                    $medPct  = round(($mediumRiskCount / $totalRiskCount) * 100);
                    $lowPct  = round(($lowRiskCount / $totalRiskCount) * 100);
                @endphp

                <!-- Multi-Segment Spectrum Bar -->
                <div class="h-3 w-full bg-slate-900 rounded-full overflow-hidden flex mb-4">
                    <div style="width: {{ $critPct }}%" class="bg-rose-500 transition-all duration-500" title="Critical: {{ $criticalRiskCount }}"></div>
                    <div style="width: {{ $highPct }}%" class="bg-orange-500 transition-all duration-500" title="High: {{ $highRiskCount }}"></div>
                    <div style="width: {{ $medPct }}%" class="bg-amber-500 transition-all duration-500" title="Medium: {{ $mediumRiskCount }}"></div>
                    <div style="width: {{ $lowPct }}%" class="bg-emerald-500 transition-all duration-500" title="Low: {{ $lowRiskCount }}"></div>
                </div>

                <!-- Legend Pills Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-2.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300">
                        <div class="text-[10px] font-bold uppercase opacity-80">Critical</div>
                        <div class="text-base font-bold text-white mt-0.5">{{ $criticalRiskCount }} <span class="text-[10px] text-slate-400 font-normal">({{ $critPct }}%)</span></div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-orange-500/10 border border-orange-500/20 text-orange-300">
                        <div class="text-[10px] font-bold uppercase opacity-80">High</div>
                        <div class="text-base font-bold text-white mt-0.5">{{ $highRiskCount }} <span class="text-[10px] text-slate-400 font-normal">({{ $highPct }}%)</span></div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300">
                        <div class="text-[10px] font-bold uppercase opacity-80">Medium</div>
                        <div class="text-base font-bold text-white mt-0.5">{{ $mediumRiskCount }} <span class="text-[10px] text-slate-400 font-normal">({{ $medPct }}%)</span></div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">
                        <div class="text-[10px] font-bold uppercase opacity-80">Low</div>
                        <div class="text-base font-bold text-white mt-0.5">{{ $lowRiskCount }} <span class="text-[10px] text-slate-400 font-normal">({{ $lowPct }}%)</span></div>
                    </div>
                </div>
            </div>

            <!-- Highest Risk Sovereign Alerts List -->
            <div class="min-card">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06] mb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                        Highest Risk Countries
                    </h3>
                    <a href="{{ route('risk-scores.index') }}" class="text-xs text-blue-400 hover:underline font-medium">View All &rarr;</a>
                </div>

                <div class="space-y-2">
                    @forelse($topRiskScores as $score)
                        <div class="min-list-item">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center font-bold text-xs text-white border border-slate-700 shrink-0">
                                    {{ $score->country->iso_code_2 ?? '??' }}
                                </div>
                                <div>
                                    <a href="{{ route('countries.show', $score->country_id) }}" class="text-sm font-semibold text-white hover:text-blue-400 transition-colors">
                                        {{ $score->country->country_name ?? 'Unknown Country' }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Region: {{ $score->country->region ?? 'Global' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <span class="text-sm font-extrabold {{ $score->total_score >= 76 ? 'text-rose-400' : ($score->total_score >= 51 ? 'text-orange-400' : 'text-amber-400') }}">
                                        {{ number_format($score->total_score, 1) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">Risk Index</span>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md border 
                                    {{ $score->risk_level === 'Critical' || $score->total_score >= 76 ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' : 
                                       ($score->risk_level === 'High' || $score->total_score >= 51 ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20') }}">
                                    {{ $score->risk_level ?? ($score->total_score >= 76 ? 'Critical' : 'High') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">No risk data recorded.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Panel: Recent Intelligence & Quick Links (Span 1) -->
        <div class="space-y-6">
            
            <!-- Latest News Feed -->
            <div class="min-card">
                <div class="flex items-center justify-between pb-3 border-b border-white/[0.06] mb-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="rss" class="w-4 h-4 text-amber-400"></i>
                        Latest Intelligence
                    </h3>
                    <a href="{{ route('news.index') }}" class="text-xs text-blue-400 hover:underline font-medium">News Feed &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse($latestNews as $article)
                        <div class="p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.04] transition-all">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                    {{ $article->category ?? 'Intelligence' }}
                                </span>
                                <span class="text-[10px] text-slate-500">
                                    {{ optional($article->published_at)->diffForHumans() ?? 'Recent' }}
                                </span>
                            </div>
                            <a href="{{ $article->url ?? route('news.index') }}" target="_blank" class="text-xs font-semibold text-slate-200 hover:text-white line-clamp-2 transition-colors">
                                {{ $article->title }}
                            </a>
                            @if(isset($article->country))
                                <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-slate-500"></i>
                                    {{ $article->country->country_name }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">No recent articles indexed.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Navigation Panel -->
            <div class="min-card">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="compass" class="w-4 h-4 text-indigo-400"></i>
                    Quick Navigation
                </h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('watchlists.index') }}" class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors flex items-center gap-2">
                        <i data-lucide="list" class="w-3.5 h-3.5 text-cyan-400"></i>
                        <span>Watchlist</span>
                    </a>
                    <a href="{{ route('comparison') }}" class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors flex items-center gap-2">
                        <i data-lucide="git-compare" class="w-3.5 h-3.5 text-purple-400"></i>
                        <span>Compare</span>
                    </a>
                    <a href="{{ route('map') }}" class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors flex items-center gap-2">
                        <i data-lucide="map" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Global Map</span>
                    </a>
                    <a href="{{ route('ports.index') }}" class="p-2.5 rounded-lg bg-slate-800/60 border border-slate-700/60 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors flex items-center gap-2">
                        <i data-lucide="anchor" class="w-3.5 h-3.5 text-indigo-400"></i>
                        <span>Ports</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
</x-app-layout>
