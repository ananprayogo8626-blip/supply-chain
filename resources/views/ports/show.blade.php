<x-app-layout>
    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($port->country && $port->country->flag)
                        <img src="{{ $port->country->flag }}" alt="{{ $port->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">{{ $port->port_name }}</h1>
                        <p class="sg-crud-description">
                            {{ $port->country->country_name ?? 'Unknown' }} • {{ $port->city ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('ports.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('ports.edit', $port->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
                @if($port->latitude && $port->longitude)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $port->latitude }},{{ $port->longitude }}"
                   target="_blank" rel="noopener"
                   class="sg-btn sg-btn-sm sg-btn-teal">
                    <i data-lucide="map" class="w-4 h-4"></i>
                    Open in Google Maps
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Port Information Grid -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Basic Info Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="anchor" class="w-5 h-5 text-cyan-400"></i>
                    <h2 class="sg-data-title">Port Information</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Port Name</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $port->port_name ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">UN/LOCODE</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $port->port_code ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">City</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $port->city ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">
                            {{ $port->country->country_name ?? 'Unknown' }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Port Type</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $port->port_type ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="activity" class="w-5 h-5 text-green-400"></i>
                    <h2 class="sg-data-title">Operational Status</h2>
                </div>
            </div>
            <div style="padding:20px;">
                @php 
                    $status = $port->status ?? 'Active';
                    $statusColor = match($status) {
                        'Active' => 'var(--sg-success)',
                        'Congested' => 'var(--sg-warning)',
                        'Closed' => 'var(--sg-danger)',
                        default => 'var(--sg-text-secondary)'
                    };
                @endphp
                <div style="text-align:center; padding:20px;">
                    <div style="width:100px; height:100px; background:{{ $statusColor }}20; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <i data-lucide="anchor" class="w-12 h-12" style="color:{{ $statusColor }}"></i>
                    </div>
                    <div style="font-size:32px; font-weight:800; color:{{ $statusColor }};">
                        {{ $status }}
                    </div>
                    <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">
                        Current operational status
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coordinates Card -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i>
                <h2 class="sg-data-title">Location Coordinates</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                    <i data-lucide="arrow-up" class="w-6 h-6 text-orange-400 mx-auto mb-2"></i>
                    <div style="font-size:24px; font-weight:700; color:var(--sg-text-primary);">
                        {{ $port->latitude ?? '—' }}
                    </div>
                    <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Latitude</div>
                </div>
                <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; text-align:center;">
                    <i data-lucide="arrow-right" class="w-6 h-6 text-cyan-400 mx-auto mb-2"></i>
                    <div style="font-size:24px; font-weight:700; color:var(--sg-text-primary);">
                        {{ $port->longitude ?? '—' }}
                    </div>
                    <div style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">Longitude</div>
                </div>
            </div>
            @if($port->latitude && $port->longitude)
            <a href="https://www.google.com/maps/search/?api=1&query={{ $port->latitude }},{{ $port->longitude }}"
               target="_blank" rel="noopener"
               class="sg-btn sg-btn-sm sg-btn-secondary" style="margin-top:16px; width:100%; justify-content:center;">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Open in Google Maps
            </a>
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
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $port->id }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Source</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">World Port Index</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
