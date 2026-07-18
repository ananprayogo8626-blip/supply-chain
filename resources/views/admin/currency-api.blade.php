<x-app-layout>
    @push('head')
        <style>
            /* Cards layout */
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
            
            /* Status badges */
            .api-status-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .api-status-active { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
            .api-status-degraded { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
            .api-status-inactive { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

            /* Table formatting */
            .log-table {
                width: 100%;
                border-collapse: collapse;
            }
            .log-table th {
                text-align: left;
                padding: 12px 16px;
                font-size: 11px;
                font-weight: 700;
                color: var(--sg-text-secondary);
                text-transform: uppercase;
                border-bottom: 1px solid var(--sg-border);
                letter-spacing: 0.5px;
            }
            .log-table td {
                padding: 14px 16px;
                font-size: 13px;
                color: var(--sg-text-primary);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .log-table tr:hover td {
                background: rgba(255, 255, 255, 0.02);
            }

            /* Log status badges */
            .log-status {
                display: inline-flex;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
            }
            .log-status-completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
            .log-status-failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
            .log-status-processing { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
            .log-status-pending { background: rgba(148, 163, 184, 0.1); color: #94a3b8; }

            /* Progress bar within Swal */
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
                background: linear-gradient(90deg, #FF9F00, #FFB020);
                transition: width 0.3s ease;
            }
        </style>
    @endpush

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 mb-6 border-b border-white/5">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i data-lucide="coins" class="w-7 h-7 text-amber-400"></i>
                Currency API Management
            </h1>
            <p class="text-sm text-slate-400 mt-2">Monitor integration status and manually trigger ExchangeRate API background synchronization.</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <button onclick="triggerSync()" class="sg-btn sg-btn-gradient flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Sync Currency Rates
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <!-- API Status -->
        <div class="api-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">API Status</span>
                <i data-lucide="activity" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div class="mt-2">
                @if($status === 'ACTIVE')
                    <span class="api-status-badge api-status-active">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Active
                    </span>
                @elseif($status === 'DEGRADED')
                    <span class="api-status-badge api-status-degraded">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span> Degraded
                    </span>
                @else
                    <span class="api-status-badge api-status-inactive">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span> Inactive
                    </span>
                @endif
            </div>
            <p class="text-[10px] text-slate-500 mt-4">API health overview</p>
        </div>

        <!-- Last Sync -->
        <div class="api-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Last Sync</span>
                <i data-lucide="clock" class="w-4 h-4 text-blue-400"></i>
            </div>
            <h3 class="text-lg font-bold text-white mt-2">{{ $lastSync }}</h3>
            <p class="text-[10px] text-slate-500 mt-4">Time since latest pull</p>
        </div>

        <!-- Response Time -->
        <div class="api-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Response Time</span>
                <i data-lucide="zap" class="w-4 h-4 text-cyan-400"></i>
            </div>
            <h3 class="text-lg font-bold text-white mt-2">{{ $responseTime }}</h3>
            <p class="text-[10px] text-slate-500 mt-4">Duration of last execution</p>
        </div>

        <!-- Masked Key -->
        <div class="api-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Masked API Key</span>
                <i data-lucide="key" class="w-4 h-4 text-rose-400"></i>
            </div>
            <h3 class="text-xs font-mono text-slate-300 mt-3 truncate" title="{{ $maskedKey }}">
                {{ $maskedKey }}
            </h3>
            <p class="text-[10px] text-slate-500 mt-4">Resolved from services config</p>
        </div>

        <!-- Total Currencies -->
        <div class="api-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Total Data</span>
                <i data-lucide="database" class="w-4 h-4 text-violet-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($totalCurrencies) }}</h3>
            <p class="text-[10px] text-slate-500 mt-4">Monitored currency rows</p>
        </div>
    </div>

    <!-- Sync Log Panel -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="file-text" class="w-5 h-5 text-amber-500"></i>
                <h2 class="sg-data-title">Synchronization Logs
                    <span class="sg-count-badge">ExchangeRate history</span>
                </h2>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Started At</th>
                        <th>Finished At</th>
                        <th>Duration</th>
                        <th>Processed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($syncLogs as $log)
                        <tr>
                            <td class="font-mono text-slate-400">#{{ $log->id }}</td>
                            <td>{{ $log->started_at ? $log->started_at->format('Y-m-d H:i:s') : '—' }}</td>
                            <td>{{ $log->finished_at ? $log->finished_at->format('Y-m-d H:i:s') : '—' }}</td>
                            <td>
                                @if($log->started_at && $log->finished_at)
                                    {{ $log->finished_at->diffInSeconds($log->started_at) }}s
                                @else
                                    —
                                @endif
                            </td>
                            <td class="font-semibold text-slate-300">
                                {{ $log->processed }} / {{ $log->total }} countries
                            </td>
                            <td>
                                <span class="log-status log-status-{{ $log->status }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-400 py-8">
                                No synchronization logs found. Click "Sync Currency Rates" to trigger sync.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sync Polling Script -->
    <script>
        function triggerSync() {
            Swal.fire({
                title: 'Syncing Currency Exchange Rates...',
                html: '<div class="progress-container"><div class="progress-bar" id="sync-progress-bar" style="width:0%"></div></div><p id="sync-status">Dispatching background job...</p>',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1B2433',
                color: '#F8FAFC',
                didOpen: () => {
                    fetch('{{ route('admin.currency-api.sync') }}', {
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
                            pollSyncProgress();
                        } else {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Sync Dispatch Failed',
                                text: data.message || 'Failed to trigger sync job.',
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

        function pollSyncProgress() {
            const interval = setInterval(async () => {
                try {
                    const res = await fetch('/api/import/progress/currency');
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
                                title: 'Sync Completed Successfully',
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
                            text: 'Exchange rates synchronization failed. Please check log files.',
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
