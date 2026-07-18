<x-app-layout>
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <style>
            .api-card {
                background: var(--sg-glass);
                border: 1px solid var(--sg-border);
                border-radius: 12px;
                padding: 24px;
                transition: transform 0.2s, border-color 0.2s;
            }
            .api-card:hover {
                transform: translateY(-2px);
                border-color: rgba(255, 255, 255, 0.15);
            }
            
            .risk-badge {
                display: inline-flex;
                align-items: center;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
            }
            .badge-low { background: rgba(16, 185, 129, 0.1); color: #10b981; }
            .badge-medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
            .badge-high { background: rgba(249, 115, 22, 0.1); color: #f97316; }
            .badge-critical { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

            .log-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
            }
            .log-table th {
                text-align: left;
                padding: 10px 12px;
                font-size: 11px;
                font-weight: 700;
                color: var(--sg-text-secondary);
                text-transform: uppercase;
                border-bottom: 1px solid var(--sg-border);
            }
            .log-table td {
                padding: 10px 12px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .log-table tr:hover td {
                background: rgba(255, 255, 255, 0.02);
            }

            .progress-container {
                width: 100%;
                height: 18px;
                background: #334155;
                border-radius: 9px;
                overflow: hidden;
                margin: 20px 0;
            }
            .progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #F59E0B, #EF4444);
                transition: width 0.3s ease;
            }
        </style>
    @endpush

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 mb-6 border-b border-white/5">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i data-lucide="shield-alert" class="w-7 h-7 text-red-500"></i>
                Risk Intelligence Center
            </h1>
            <p class="text-sm text-slate-400 mt-2">Manage supply chain risk assessment rules, recalculate threat indexes, and track trend anomalies.</p>
        </div>
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
            <button onclick="triggerRecalculateAll()" class="sg-btn sg-btn-gradient flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Recalculate All
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="sg-flash sg-flash-success mb-6">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <!-- Low Risk -->
        <div class="api-card" style="border-left: 4px solid #10b981;">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Low Risk</span>
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($stats['low']) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Score 0 - 25</p>
        </div>

        <!-- Medium Risk -->
        <div class="api-card" style="border-left: 4px solid #f59e0b;">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Medium Risk</span>
                <i data-lucide="shield" class="w-4 h-4 text-amber-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($stats['medium']) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Score 26 - 50</p>
        </div>

        <!-- High Risk -->
        <div class="api-card" style="border-left: 4px solid #f97316;">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">High Risk</span>
                <i data-lucide="shield-alert" class="w-4 h-4 text-orange-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($stats['high']) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Score 51 - 75</p>
        </div>

        <!-- Critical Risk -->
        <div class="api-card" style="border-left: 4px solid #ef4444;">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Critical Risk</span>
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($stats['critical']) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Score 76 - 100</p>
        </div>

        <!-- Average Score -->
        <div class="api-card" style="background: rgba(99, 102, 241, 0.05); border-left: 4px solid #6366f1;">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Avg Risk Score</span>
                <i data-lucide="activity" class="w-4 h-4 text-indigo-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($stats['average'], 1) }}</h3>
            <p class="text-[10px] text-slate-500 mt-2">Weighted average</p>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recalculate Selected Country Card -->
        <div class="sg-data-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-md font-bold text-white mb-2 flex items-center gap-2">
                    <i data-lucide="target" class="text-orange-500 w-5 h-5"></i>
                    Recalculate Selected Country
                </h3>
                <p class="text-xs text-slate-400 mb-6">Select a specific country to recalculate its weighted threat index immediately.</p>
            </div>
            <form action="" id="single-recalc-form" method="POST" class="space-y-4">
                @csrf
                <select id="country-select" name="country_id" required class="sg-form-input w-full">
                    <option value="">-- Choose Country --</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->country_name }} ({{ $c->country_code }})</option>
                    @endforeach
                </select>
                <button type="button" onclick="submitSingleRecalc()" class="sg-btn sg-btn-secondary w-full justify-center">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Recalculate Single
                </button>
            </form>
        </div>

        <!-- Charts Card (Risk Distribution) -->
        <div class="sg-data-card p-6">
            <h3 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="pie-chart" class="text-violet-400 w-5 h-5"></i>
                Risk Distribution
            </h3>
            <div style="height: 180px; position: relative;">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

        <!-- Charts Card (Trend Chart) -->
        <div class="sg-data-card p-6">
            <h3 class="text-md font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="line-chart" class="text-cyan-400 w-5 h-5"></i>
                Platform Risk Trend
            </h3>
            <div style="height: 180px; position: relative;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Country Lists Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top 10 High Risk Country -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="shield-alert" class="w-5 h-5 text-rose-500"></i>
                    <h2 class="sg-data-title">Top 10 High Risk Countries</h2>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Risk Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topHighRisk as $item)
                            <tr>
                                <td class="flex items-center gap-2">
                                    @if($item->country && $item->country->flag)
                                        <img src="{{ $item->country->flag }}" class="w-5 h-3 object-cover rounded border border-white/5" onerror="this.src='https://flagcdn.com/w40/un.png';">
                                    @endif
                                    <a href="{{ route('countries.show', $item->country_id) }}" class="font-bold text-slate-200 hover:text-orange-400">{{ $item->country->country_name ?? 'N/A' }}</a>
                                </td>
                                <td class="font-bold text-slate-300">{{ $item->total_score }}</td>
                                <td>
                                    <span class="risk-badge badge-{{ strtolower($item->risk_level) }}">{{ $item->risk_level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-slate-500 py-6">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top 10 Safest Country -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                    <h2 class="sg-data-title">Top 10 Safest Countries</h2>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Risk Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSafest as $item)
                            <tr>
                                <td class="flex items-center gap-2">
                                    @if($item->country && $item->country->flag)
                                        <img src="{{ $item->country->flag }}" class="w-5 h-3 object-cover rounded border border-white/5" onerror="this.src='https://flagcdn.com/w40/un.png';">
                                    @endif
                                    <a href="{{ route('countries.show', $item->country_id) }}" class="font-bold text-slate-200 hover:text-orange-400">{{ $item->country->country_name ?? 'N/A' }}</a>
                                </td>
                                <td class="font-bold text-slate-300">{{ $item->total_score }}</td>
                                <td>
                                    <span class="risk-badge badge-{{ strtolower($item->risk_level) }}">{{ $item->risk_level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-slate-500 py-6">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Logs & History Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Risk Calculation Log (Recalculation Jobs) -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="file-text" class="w-5 h-5 text-violet-400"></i>
                    <h2 class="sg-data-title">Risk Calculation Log</h2>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Started At</th>
                            <th>Finished At</th>
                            <th>Processed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($progressLogs as $log)
                            <tr>
                                <td class="font-mono text-slate-400">#{{ $log->id }}</td>
                                <td>{{ $log->started_at ? $log->started_at->format('Y-m-d H:i:s') : '—' }}</td>
                                <td>{{ $log->finished_at ? $log->finished_at->format('Y-m-d H:i:s') : '—' }}</td>
                                <td class="font-semibold text-slate-300">{{ $log->processed }} / {{ $log->total }} countries</td>
                                <td>
                                    <span class="risk-badge badge-{{ $log->status === 'completed' ? 'low' : ($log->status === 'failed' ? 'critical' : 'medium') }}">{{ $log->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500 py-8">No calculation job logs recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Risk History Logs -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="history" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="sg-data-title">Risk History Log</h2>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Total Score</th>
                            <th>Level</th>
                            <th>Calculated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riskHistoryLogs as $history)
                            <tr>
                                <td class="font-bold text-slate-300">
                                    {{ $history->country->country_name ?? 'N/A' }}
                                </td>
                                <td class="font-semibold text-slate-200">{{ $history->total_score }}</td>
                                <td>
                                    <span class="risk-badge badge-{{ strtolower($history->risk_level) }}">{{ $history->risk_level }}</span>
                                </td>
                                <td class="text-xs text-slate-400">{{ $history->calculated_at ? $history->calculated_at->diffForHumans() : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-500 py-8">No historical risk changes logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Distribution Chart (Doughnut)
            const distCtx = document.getElementById('distributionChart').getContext('2d');
            new Chart(distCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Low', 'Medium', 'High', 'Critical'],
                    datasets: [{
                        data: [
                            {{ $stats['low'] }},
                            {{ $stats['medium'] }},
                            {{ $stats['high'] }},
                            {{ $stats['critical'] }}
                        ],
                        backgroundColor: ['#10B981', '#F59E0B', '#F97316', '#EF4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                color: '#94a3b8',
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });

            // 2. Trend Chart (Line)
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendData = @json($riskTrend);
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.label),
                    datasets: [{
                        label: 'Average Score',
                        data: trendData.map(d => d.value),
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
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
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 9 } }
                        }
                    }
                }
            });
        });

        // Submit Single Recalculation handler
        function submitSingleRecalc() {
            const countryId = document.getElementById('country-select').value;
            if (!countryId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Country',
                    text: 'Please choose a country to recalculate.',
                    background: '#1B2433',
                    color: '#F8FAFC'
                });
                return;
            }
            
            const form = document.getElementById('single-recalc-form');
            form.action = `/admin/risk-intelligence/recalculate-country/${countryId}`;
            form.submit();
        }

        // Trigger batch recalculation
        function triggerRecalculateAll() {
            Swal.fire({
                title: 'Recalculating Risk Scores...',
                html: '<div class="progress-container"><div class="progress-bar" id="sync-progress-bar" style="width:0%"></div></div><p id="sync-status">Dispatching batch queue worker...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1B2433',
                color: '#F8FAFC',
                didOpen: () => {
                    fetch('{{ route('admin.risk-intelligence.recalculate-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            pollRiskProgress();
                        } else {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Recalculation Failed',
                                text: data.message || 'Failed to trigger batch job.',
                                confirmButtonColor: '#FF6B00',
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.message,
                            confirmButtonColor: '#FF6B00',
                            background: '#1B2433',
                            color: '#F8FAFC'
                        });
                    });
                }
            });
        }

        function pollRiskProgress() {
            const interval = setInterval(async () => {
                try {
                    const res = await fetch('/api/import/progress/risk_scores');
                    const json = await res.json();

                    const bar = document.getElementById('sync-progress-bar');
                    const statusText = document.getElementById('sync-status');

                    if (bar) {
                        bar.style.width = json.percentage + '%';
                    }
                    if (statusText) {
                        statusText.textContent = `Progress: ${json.percentage}% | Processed: ${json.processed}/${json.total} countries`;
                    }

                    if (json.status === 'completed' || json.percentage >= 100) {
                        clearInterval(interval);
                        setTimeout(() => {
                            Swal.close();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Recalculation Completed Successfully',
                                showConfirmButton: false,
                                timer: 3000,
                                background: '#1B2433',
                                color: '#F8FAFC'
                            });
                            window.location.reload();
                        }, 500);
                    }

                    if (json.status === 'failed') {
                        clearInterval(interval);
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Sync Failed',
                            text: 'Recalculation failed. Please verify recent activity logs.',
                            confirmButtonColor: '#FF6B00',
                            background: '#1B2433',
                            color: '#F8FAFC'
                        });
                    }
                } catch (err) {
                    clearInterval(interval);
                }
            }, 2000);
        }
    </script>
</x-app-layout>
