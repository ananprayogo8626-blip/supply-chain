<x-app-layout>
    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($currency->country && $currency->country->flag)
                        <img src="{{ $currency->country->flag }}" alt="{{ $currency->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">{{ $currency->currency_code ?? 'Unknown' }}</h1>
                        <p class="sg-crud-description">
                            {{ $currency->currency_name ?? '—' }} • {{ $currency->country->country_name ?? 'Unknown' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('currency.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('currency.edit', $currency->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
                @if($currency->country)
                <a href="{{ route('currency.sync', $currency->country->id) }}" class="sg-btn sg-btn-sm sg-btn-outline-orange">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Sync
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Exchange Rate Card -->
    <div class="sg-data-card" style="text-align:center; padding:40px; margin-bottom:24px;">
        <i data-lucide="banknote" class="w-12 h-12 text-amber-500 mx-auto mb-4"></i>
        <div style="font-size:64px; font-weight:800; color:var(--sg-text-primary); line-height:1;">
            {{ number_format((float)($currency->exchange_rate ?? 0), 4) }}
        </div>
        <div style="font-size:16px; color:var(--sg-text-secondary); margin-top:8px;">
            Exchange Rate (per USD)
        </div>
        <div style="margin-top:16px;">
            <span class="sg-code-badge">{{ $currency->currency_code }} ({{ $currency->symbol ?? '—' }})</span>
        </div>
    </div>

    <!-- Buy/Sell Rates -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Buy Rate Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="arrow-down" class="w-5 h-5 text-green-400"></i>
                    <h2 class="sg-data-title">Buy Rate</h2>
                </div>
            </div>
            <div style="padding:20px; text-align:center;">
                <div style="font-size:48px; font-weight:800; color:#0f766e;">
                    {{ number_format((float)($currency->buy ?? 0), 4) }}
                </div>
                <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">
                    Bank buys at this rate
                </div>
            </div>
        </div>

        <!-- Sell Rate Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="arrow-up" class="w-5 h-5 text-red-400"></i>
                    <h2 class="sg-data-title">Sell Rate</h2>
                </div>
            </div>
            <div style="padding:20px; text-align:center;">
                <div style="font-size:48px; font-weight:800; color:#b91c1c;">
                    {{ number_format((float)($currency->sell ?? 0), 4) }}
                </div>
                <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">
                    Bank sells at this rate
                </div>
            </div>
        </div>
    </div>

    <!-- Change Indicator -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="trending-up" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="sg-data-title">Trend Indicator</h2>
            </div>
        </div>
        <div style="padding:20px;">
            @php $chg = (float)($currency->change_percentage ?? 0); @endphp
            @if(abs($chg) < 0.001)
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="minus" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:var(--sg-text-secondary);">
                            → 0.00%
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            No significant change
                        </div>
                    </div>
                </div>
            @elseif($chg > 0)
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(16, 185, 129, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="trending-up" class="w-10 h-10 text-green-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:#10b981;">
                            +{{ number_format($chg, 2) }}%
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Currency appreciating
                        </div>
                    </div>
                </div>
            @else
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(239, 68, 68, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="trending-down" class="w-10 h-10 text-red-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:#ef4444;">
                            {{ number_format($chg, 2) }}%
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Currency depreciating
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Record Information -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="clock" class="w-5 h-5 text-green-400"></i>
                <h2 class="sg-data-title">Record Information</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Last Updated</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $currency->last_updated ? \Carbon\Carbon::parse($currency->last_updated)->format('M d, Y H:i:s') : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $currency->country->country_name ?? 'Unknown' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $currency->id }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
