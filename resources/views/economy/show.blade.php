<x-app-layout>
    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($economy->country && $economy->country->flag)
                        <img src="{{ $economy->country->flag }}" alt="{{ $economy->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">Economic Details</h1>
                        <p class="sg-crud-description">
                            {{ $economy->country->country_name ?? 'Unknown' }} • {{ $economy->data_year ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('economy.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('economy.edit', $economy->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
                @if($economy->country)
                <a href="{{ route('economy.sync', $economy->country->id) }}" class="sg-btn sg-btn-sm sg-btn-outline-orange">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Sync
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Economic Statistics Grid -->
    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; margin-bottom:24px;">
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="dollar-sign" class="w-8 h-8 text-green-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">
                {{ $economy->gdp ? '$' . number_format($economy->gdp / 1e9, 2) . 'B' : '--' }}
            </div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">GDP</div>
        </div>
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="trending-up" class="w-8 h-8 {{ $economy->gdp_growth >= 0 ? 'text-green-400' : 'text-red-400' }} mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:{{ $economy->gdp_growth >= 0 ? 'var(--sg-success)' : 'var(--sg-danger)' }};">
                {{ $economy->gdp_growth ? number_format($economy->gdp_growth, 2) . '%' : '--' }}
            </div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">GDP Growth</div>
        </div>
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="percent" class="w-8 h-8 text-orange-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">
                {{ $economy->inflation ? number_format($economy->inflation, 2) . '%' : '--' }}
            </div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">Inflation Rate</div>
        </div>
    </div>

    <!-- Trade Statistics -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Imports Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="package" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">Imports</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="arrow-down" class="w-10 h-10 text-blue-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:var(--sg-text-primary);">
                            {{ $economy->imports ? '$' . number_format($economy->imports / 1e9, 2) . 'B' : '--' }}
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Total imports value
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exports Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="package" class="w-5 h-5 text-green-400"></i>
                    <h2 class="sg-data-title">Exports</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="arrow-up" class="w-10 h-10 text-green-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:var(--sg-text-primary);">
                            {{ $economy->exports ? '$' . number_format($economy->exports / 1e9, 2) . 'B' : '--' }}
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Total exports value
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Details -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="info" class="w-5 h-5 text-purple-400"></i>
                <h2 class="sg-data-title">Record Information</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Year</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">{{ $economy->data_year ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $economy->country->country_name ?? 'Unknown' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Trade Balance</span>
                    <span style="color:{{ $economy->trade_balance >= 0 ? 'var(--sg-success)' : 'var(--sg-danger)' }}; font-weight:600;">
                        {{ $economy->trade_balance ? '$' . number_format($economy->trade_balance / 1e9, 2) . 'B' : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $economy->id }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
