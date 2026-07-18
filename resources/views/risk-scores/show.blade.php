<x-app-layout>
    @php
        $country = $riskScore->country;
        $level = $riskScore->risk_level ?? 'Low';
        $color = match($level) {
            'Critical' => 'var(--sg-danger)',
            'High' => 'var(--accent-orange)',
            'Medium' => 'var(--sg-warning)',
            default => 'var(--sg-success)'
        };
    @endphp

    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($country && $country->flag)
                        <img src="{{ $country->flag }}" alt="{{ $country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">Risk Score Details</h1>
                        <p class="sg-crud-description">
                            {{ $country->country_name ?? 'Unknown' }} • {{ $country->capital ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('risk-scores.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('risk-scores.calculate', $country->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Recalculate
                </a>
            </div>
        </div>
    </div>

    <!-- Overall Risk Score Card -->
    <div class="sg-data-card" style="text-align:center; padding:40px; margin-bottom:24px; background:rgba(30, 41, 59, 0.3); border-color:{{ $color }}40;">
        <span style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--sg-text-secondary); letter-spacing:0.05em;">Overall Risk Index</span>
        <div style="font-size:72px; font-weight:800; color:{{ $color }}; line-height:1; margin:16px 0 8px 0;">
            {{ $riskScore->total_score ?? 0 }}<span style="font-size:28px; font-weight:500; color:var(--sg-text-secondary);">/100</span>
        </div>
        <span class="sg-badge {{ strtolower($level) }}" style="margin-bottom:16px;">{{ $level }}</span>
        <div style="border-top:1px solid var(--sg-border); padding-top:16px; text-align:left; max-width:400px; margin:0 auto;">
            <span style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--sg-text-secondary); display:block; margin-bottom:4px;">Risk Analyst Recommendation:</span>
            <p style="font-size:13px; color:var(--sg-text-primary); line-height:1.5; margin:0;">
                {{ $riskScore->recommendation ?: 'No specific warnings issued. Monitor weather updates and port throughput regularly.' }}
            </p>
        </div>
    </div>

    <!-- Risk Factor Breakdown -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-orange-400"></i>
                <h2 class="sg-data-title">Risk Factor Breakdown</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px;">
                <div style="text-align:center;">
                    <div style="font-size:32px; font-weight:800; color:{{ $riskScore->weather_score >= 76 ? 'var(--sg-danger)' : ($riskScore->weather_score >= 51 ? 'var(--accent-orange)' : ($riskScore->weather_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)') }};">{{ $riskScore->weather_score ?? 0 }}</div>
                    <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">🌤️ Weather (30%)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:32px; font-weight:800; color:{{ $riskScore->economic_score >= 76 ? 'var(--sg-danger)' : ($riskScore->economic_score >= 51 ? 'var(--accent-orange)' : ($riskScore->economic_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)') }};">{{ $riskScore->economic_score ?? 0 }}</div>
                    <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">� Inflation (25%)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:32px; font-weight:800; color:{{ $riskScore->currency_score >= 76 ? 'var(--sg-danger)' : ($riskScore->currency_score >= 51 ? 'var(--accent-orange)' : ($riskScore->currency_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)') }};">{{ $riskScore->currency_score ?? 0 }}</div>
                    <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">� Exchange Rate (20%)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:32px; font-weight:800; color:{{ $riskScore->news_score >= 76 ? 'var(--sg-danger)' : ($riskScore->news_score >= 51 ? 'var(--accent-orange)' : ($riskScore->news_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)') }};">{{ $riskScore->news_score ?? 0 }}</div>
                    <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">� News Sentiment (25%)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk History -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="history" class="w-5 h-5 text-purple-400"></i>
                <h2 class="sg-data-title">Risk History</h2>
            </div>
        </div>
        <div style="padding:20px;">
            @php
                $riskHistory = \App\Models\RiskHistory::forCountry($country->id)->recent(10)->get();
            @endphp
            @if($riskHistory->count() > 0)
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($riskHistory as $history)
                        @php
                            $hColor = match($history->risk_level) {
                                'Critical' => 'var(--sg-danger)',
                                'High' => 'var(--accent-orange)',
                                'Medium' => 'var(--sg-warning)',
                                default => 'var(--sg-success)'
                            };
                        @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:rgba(30, 41, 59, 0.5); border-radius:8px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="font-size:24px; font-weight:700; color:{{ $hColor }};">{{ $history->total_score }}</div>
                                <span class="sg-badge {{ strtolower($history->risk_level) }}">{{ $history->risk_level }}</span>
                            </div>
                            <span style="font-size:12px; color:var(--sg-text-muted);">
                                {{ \Carbon\Carbon::parse($history->calculated_at)->format('M d, Y H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:var(--sg-text-secondary); text-align:center; padding:20px;">No risk history available</p>
            @endif
        </div>
    </div>
            
    <!-- Last Calculated Card -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="clock" class="w-5 h-5 text-green-400"></i>
                <h2 class="sg-data-title">Calculation Information</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Last Calculated</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $riskScore->updated_at ? \Carbon\Carbon::parse($riskScore->updated_at)->format('M d, Y H:i:s') : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $country->country_name ?? 'Unknown' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $riskScore->id }}</span>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
