<x-app-layout>
    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($weather->country && $weather->country->flag)
                        <img src="{{ $weather->country->flag }}" alt="{{ $weather->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">Weather Details</h1>
                        <p class="sg-crud-description">
                            {{ $weather->country->country_name ?? 'Unknown' }} • {{ $weather->country->capital ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('weather.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('weather.edit', $weather->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
                @if($weather->country)
                <a href="{{ route('weather.sync', $weather->country->id) }}" class="sg-btn sg-btn-sm sg-btn-outline-orange">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Sync
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Weather Statistics Grid -->
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:24px;">
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="thermometer" class="w-8 h-8 text-orange-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">{{ $weather->temperature ?? '--' }}°C</div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">Temperature</div>
        </div>
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="droplets" class="w-8 h-8 text-blue-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">{{ $weather->humidity ?? '--' }}%</div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">Humidity</div>
        </div>
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="wind" class="w-8 h-8 text-cyan-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">{{ $weather->wind_speed ?? '--' }}</div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">Wind Speed (km/h)</div>
        </div>
        <div class="sg-data-card" style="text-align:center; padding:24px;">
            <i data-lucide="gauge" class="w-8 h-8 text-purple-400 mx-auto mb-3"></i>
            <div style="font-size:36px; font-weight:800; color:var(--sg-text-primary);">{{ $weather->pressure ?? '--' }}</div>
            <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">Pressure (hPa)</div>
        </div>
    </div>

    <!-- Additional Details -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Weather Condition Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="cloud" class="w-5 h-5 text-slate-400"></i>
                    <h2 class="sg-data-title">Weather Condition</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="cloud-sun" class="w-10 h-10 text-yellow-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:var(--sg-text-primary);">
                            {{ $weather->weather_condition ?? 'Clear' }}
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Current conditions
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rainfall Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="cloud-rain" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">Rainfall</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="umbrella" class="w-10 h-10 text-blue-400"></i>
                    </div>
                    <div>
                        <div style="font-size:28px; font-weight:700; color:var(--sg-text-primary);">
                            {{ $weather->rainfall ?? '0' }} mm
                        </div>
                        <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:4px;">
                            Precipitation level
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Updated Card -->
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
                        {{ $weather->last_updated ? \Carbon\Carbon::parse($weather->last_updated)->format('M d, Y H:i:s') : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $weather->country->country_name ?? 'Unknown' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $weather->id }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
