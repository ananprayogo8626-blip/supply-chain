<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Risk Scores"
        description="Supply chain risk assessment matrix for all monitored countries."
        icon="shield-alert"
        iconColor="text-red-500"
    >

        <a href="{{ route('risk-scores.calculate-all') }}"
           onclick="return confirm('Recalculate risk scores for all countries? This may take a moment.')"
           class="sg-btn sg-btn-sm sg-btn-gradient" id="btn-recalc-risk">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Recalculate Risk Scores
        </a>
        <a href="{{ route('risk-scores.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Score Card
        </a>
    </x-crud-header>

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

    <!-- Standardized Toolbar -->
    <x-crud-toolbar 
        searchPlaceholder="Search risk scores..."
        searchValue="{{ request('search') }}"
        :showRefresh="true"
        :showExport="false"
        :showImport="false"
        :showAdd="false"
    >
        <select name="status" onchange="this.form.submit()">
            <option value="">Active</option>
            <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
        </select>
    </x-crud-toolbar>

    <!-- Main Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="list" style="width:16px;height:16px;color:var(--sg-danger)"></i>
                <h2 class="sg-data-title">Risk Score Data
                    <span class="sg-count-badge">{{ $scores->total() }} records</span>
                </h2>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="risk-scores-table">
                <thead>
                    <tr>
                        <th style="width:44px" class="sg-td-center">#</th>
                        <th>Country</th>
                        <th class="sg-td-center">🌦 Weather</th>
                        <th class="sg-td-center">📈 Economy</th>
                        <th class="sg-td-center">💱 Currency</th>
                        <th class="sg-td-center">📰 News</th>
                        <th class="sg-td-center">⚓ Port</th>
                        <th class="sg-td-center">Total</th>
                        <th class="sg-td-center">Risk Level</th>
                        <th class="sg-td-center">Status</th>
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
                            <td class="sg-td-num">{{ $scores->firstItem() + $loop->index }}</td>
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
                                <span style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">{{ number_format($score->economic_score ?? 0, 1) }}</span>
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
                            <td class="sg-td-center">
                                @if($score->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    <span class="sg-badge low">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($score->trashed())
                                        <form action="{{ route('risk-scores.restore', $score->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Risk Score">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
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
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align:center; padding:40px;">
                                <div class="sg-empty-state">
                                    <div class="sg-empty-icon">
                                        <i data-lucide="shield-off" class="w-8 h-8"></i>
                                    </div>
                                    <h3>No Risk Score Data</h3>
                                    <p>No risk score data found.</p>
                                    <a href="{{ route('risk-scores.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">Add Record</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($scores, 'hasPages') && $scores->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $scores->firstItem() }}</strong>–<strong>{{ $scores->lastItem() }}</strong> of <strong>{{ $scores->total() }}</strong> records
                </div>
                <div class="sg-pagination-nav">
                    {{ $scores->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    <style>
        .sg-form-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--sg-border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            color: var(--sg-text-primary);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            font-family: inherit;
        }
        .sg-form-input:focus {
            border-color: rgba(255,107,0,0.5);
            box-shadow: 0 0 0 3px rgba(255,107,0,0.08);
        }
    </style>
</x-app-layout>