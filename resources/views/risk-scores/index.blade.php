<x-app-layout>

    <!-- Page Header -->
    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title" style="display:flex;align-items:center;gap:8px">
                    <i data-lucide="shield-alert" style="color:var(--sg-danger);width:22px;height:22px"></i>
                    Risk Scores
                </h1>
                <p class="sg-page-desc">Supply chain risk assessment matrix for all monitored countries.</p>
            </div>
            <div class="sg-data-actions">
                <button onclick="exportTableToCSV('risk-scores-export.csv', 'risk-scores-table')" class="sg-btn sg-btn-outline-orange">
                    <i data-lucide="download" style="width:14px;height:14px"></i> Export CSV
                </button>
                <a href="{{ route('risk-scores.calculate-all') }}"
                   onclick="return confirm('Recalculate risk scores for all countries? This may take a moment.')"
                   class="sg-btn sg-btn-gradient" id="btn-recalc-risk">
                    <i data-lucide="refresh-cw" style="width:14px;height:14px"></i> Recalculate Risk Scores
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="sg-flash sg-flash-success mb-4">
            <i data-lucide="check-circle" style="width:16px;height:16px;flex-shrink:0"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error mb-4">
            <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Row -->
    <div class="sg-grid-stats mb-5">
        <div class="sg-stat-card" style="border-color:rgba(239,68,68,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-danger)">Critical</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--sg-danger)">
                        {{ $scores->filter(fn($s) => $s->risk_level === 'Critical')->count() }}
                    </span>
                    <span class="sg-stat-icon red"><i data-lucide="alert-octagon" style="width:16px;height:16px"></i></span>
                </div>
            </div>
        </div>
        <div class="sg-stat-card" style="border-color:rgba(249,115,22,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--accent-orange)">High Risk</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--accent-orange)">
                        {{ $scores->filter(fn($s) => $s->risk_level === 'High')->count() }}
                    </span>
                    <span class="sg-stat-icon orange"><i data-lucide="alert-triangle" style="width:16px;height:16px"></i></span>
                </div>
            </div>
        </div>
        <div class="sg-stat-card" style="border-color:rgba(245,158,11,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-warning)">Medium</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--sg-warning)">
                        {{ $scores->filter(fn($s) => $s->risk_level === 'Medium')->count() }}
                    </span>
                    <span class="sg-stat-icon" style="background:rgba(245,158,11,0.1);color:var(--sg-warning)"><i data-lucide="alert-circle" style="width:16px;height:16px"></i></span>
                </div>
            </div>
        </div>
        <div class="sg-stat-card" style="border-color:rgba(16,185,129,0.2)">
            <div>
                <span class="sg-stat-label" style="color:var(--sg-success)">Low Risk</span>
                <div class="sg-stat-row">
                    <span class="sg-stat-value" style="color:var(--sg-success)">
                        {{ $scores->filter(fn($s) => $s->risk_level === 'Low')->count() }}
                    </span>
                    <span class="sg-stat-icon green"><i data-lucide="check-circle" style="width:16px;height:16px"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="list" style="width:16px;height:16px;color:var(--sg-danger)"></i>
                <h2 class="sg-data-title">Risk Score Data
                    <span class="sg-count-badge">{{ method_exists($scores,'total') ? $scores->total() : $scores->count() }} records</span>
                </h2>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="risk-scores-table">
                <thead>
                    <tr>
                        <th style="width:44px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('risk-scores.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">🌦 Weather</th>
                        <th class="sg-td-center">📈 Economy</th>
                        <th class="sg-td-center">💱 Currency</th>
                        <th class="sg-td-center">📰 News</th>
                        <th class="sg-td-center">⚓ Port</th>
                        <th class="sg-td-center">
                            <a href="{{ route('risk-scores.index', array_merge(request()->query(), ['sort' => request('sort') === 'total_score' ? 'total_score_desc' : 'total_score'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Total
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">
                            <a href="{{ route('risk-scores.index', array_merge(request()->query(), ['sort' => request('sort') === 'risk_level' ? 'risk_level_desc' : 'risk_level'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Risk Level
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center" style="width:130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scores as $score)
                        @php
                            $level = $score->risk_level ?? 'Low';
                            $total = (float)($score->total_score ?? 0);
                            $barColor = match($level) {
                                'Critical' => 'var(--sg-danger)',
                                'High'     => 'var(--accent-orange)',
                                'Medium'   => 'var(--sg-warning)',
                                default    => 'var(--sg-success)'
                            };
                            $badgeClass = match($level) {
                                'Critical' => 'critical',
                                'High'     => 'high',
                                'Medium'   => 'medium',
                                default    => 'low'
                            };
                        @endphp
                        <tr>
                            <td class="sg-td-num">{{ $loop->iteration }}</td>
                            <td>
                                <div class="sg-flag-cell">
                                    @if($score->country && $score->country->flag)
                                        <img src="{{ $score->country->flag }}" alt="" loading="lazy"
                                            onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                            style="width:28px;height:19px;object-fit:cover;border-radius:3px;border:1px solid rgba(255,255,255,0.1)">
                                    @endif
                                    <div>
                                        <div class="sg-country-name">{{ $score->country->country_name ?? 'Unknown' }}</div>
                                        <div style="font-size:11px;color:var(--sg-text-muted)">{{ $score->country->region ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="sg-td-center">
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->weather_score ?? 0, 1) }}</span>
                            </td>
                            <td class="sg-td-center">
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->economy_score ?? 0, 1) }}</span>
                            </td>
                            <td class="sg-td-center">
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->currency_score ?? 0, 1) }}</span>
                            </td>
                            <td class="sg-td-center">
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->news_score ?? 0, 1) }}</span>
                            </td>
                            <td class="sg-td-center">
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->port_score ?? 0, 1) }}</span>
                            </td>
                            <td class="sg-td-center">
                                <!-- Risk bar + score -->
                                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;min-width:70px">
                                    <span style="font-size:14px;font-weight:800;color:{{ $barColor }}">{{ number_format($total, 1) }}</span>
                                    <div style="width:60px;height:5px;background:rgba(255,255,255,0.06);border-radius:99px;overflow:hidden">
                                        <div style="height:100%;width:{{ min($total, 100) }}%;background:{{ $barColor }};border-radius:99px;transition:width 0.8s ease"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="sg-td-center">
                                <span class="sg-badge {{ $badgeClass }}">{{ $level }}</span>
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    <a href="{{ route('risk-scores.show', $score->id) }}"
                                       class="sg-btn sg-btn-xs sg-btn-indigo" id="view-risk-{{ $score->id }}" title="View Scorecard">
                                        <i data-lucide="eye" style="width:11px;height:11px"></i> View
                                    </a>
                                    @if($score->country)
                                    <a href="{{ route('risk-scores.calculate', $score->country->id) }}"
                                       class="sg-btn sg-btn-xs sg-btn-teal" id="calc-risk-{{ $score->id }}" title="Recalculate">
                                        <i data-lucide="refresh-cw" style="width:11px;height:11px"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('risk-scores.edit', $score->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-risk-{{ $score->id }}" title="Edit">
                                        <i data-lucide="edit" style="width:11px;height:11px"></i>
                                    </a>
                                    <form action="{{ route('risk-scores.destroy', $score->id) }}" method="POST" style="display:inline"
                                          onsubmit="return confirm('Hapus data risk score ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-risk-{{ $score->id }}" title="Delete">
                                            <i data-lucide="trash-2" style="width:11px;height:11px"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <i data-lucide="shield-off" style="width:40px;height:40px;opacity:0.3"></i>
                                </div>
                                <p>No risk score data found.</p>
                                <a href="{{ route('risk-scores.create') }}" class="sg-btn sg-btn-primary" style="margin-top:8px">Add Record</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($scores, 'hasPages') && $scores->hasPages())
            <div class="sg-pagination-wrap">
                <p>Showing {{ $scores->firstItem() }} to {{ $scores->lastItem() }} of {{ $scores->total() }} results</p>
                <div>{{ $scores->links() }}</div>
            </div>
        @endif
    </div>

</x-app-layout>