<x-app-layout>

    <!-- Standardized Header -->
    <x-crud-header 
        title="Port Management"
        description="Global port data sourced from World Port Index — monitor operational status and logistics capacity."
        icon="anchor"
        iconColor="text-cyan-400"
    >

        <a href="{{ route('ports.import') }}" class="sg-btn sg-btn-sm sg-btn-outline-orange">
            <i data-lucide="download" class="w-4 h-4"></i>
            Sync World Port API
        </a>
    </x-crud-header>

    @if(session('success'))
        <div class="sg-flash sg-flash-success">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif


    <!-- Standardized Toolbar -->
    <x-crud-toolbar 
        searchPlaceholder="Search ports..."
        searchValue="{{ request('search') }}"
        :showRefresh="true"
        :showExport="false"
        :showImport="false"
        :showAdd="false"
    >
        <select name="country" onchange="this.form.submit()">
            <option value="">All Countries</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                    {{ $country->country_name }}
                </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">All Status / Active</option>
            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active Only</option>
            <option value="Congested" {{ request('status') == 'Congested' ? 'selected' : '' }}>Congested</option>
            <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
            <option value="trash" {{ request('status') == 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
        </select>
        <select name="type" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="Seaport" {{ request('type') == 'Seaport' ? 'selected' : '' }}>Seaport</option>
            <option value="River Port" {{ request('type') == 'River Port' ? 'selected' : '' }}>River Port</option>
        </select>
    </x-crud-toolbar>

    <!-- Glass Card with Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="anchor" class="w-5 h-5 text-cyan-400"></i>
                <h2 class="sg-data-title">Port Database
                    <span class="sg-count-badge">{{ $ports->total() }} ports</span>
                </h2>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-2">
                <i data-lucide="globe" class="w-4 h-4 text-blue-400"></i>
                Source: World Port Index
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="ports-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>Country</th>
                        <th>Port Name</th>
                        <th class="sg-td-center">UNLOCODE</th>
                        <th>City</th>
                        <th class="sg-td-center">Type</th>
                        <th class="sg-td-center">Status</th>
                        <th class="sg-td-center">Location</th>
                        <th class="sg-td-center">Capacity</th>
                        <th class="sg-td-center">Map</th>
                        <th class="sg-td-center" style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $port)
                        <tr>
                            <td class="sg-td-num">{{ $ports->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="sg-flag-cell">
                                    @if($port->country && $port->country->flag)
                                        <img src="{{ $port->country->flag }}" alt="" loading="lazy"
                                            onerror="this.onerror=null;this.src='https://flagcdn.com/w40/un.png';"
                                            style="width:28px;height:19px;object-fit:cover;border-radius:3px;border:1px solid #e2e8f0">
                                    @endif
                                    <span class="sg-country-name" style="font-size:12px">{{ $port->country->country_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="sg-td-bold" style="font-size:13px">{{ $port->port_name }}</td>
                            <td class="sg-td-center">
                                @if($port->port_code)
                                    <span class="sg-code-badge">{{ $port->port_code }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="font-size:13px;color:#475569">{{ $port->city ?? '—' }}</td>
                            <td class="sg-td-center">
                                <span style="font-size:12px;padding:2px 8px;border-radius:6px;font-weight:600;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">
                                    {{ $port->port_type ?? 'Sea Port' }}
                                </span>
                            </td>
                            <td class="sg-td-center">
                                @if($port->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @else
                                    @php $status = $port->status ?? 'Active'; @endphp
                                    @if(str_contains(strtolower($status), 'congested') || str_contains(strtolower($status), 'congestion'))
                                        <span class="sg-port-status sg-port-congested">
                                            <span class="sg-port-status-dot"></span>Congested
                                        </span>
                                    @elseif(str_contains(strtolower($status), 'inactive') || str_contains(strtolower($status), 'closed'))
                                        <span class="sg-port-status sg-port-closed">
                                            <span class="sg-port-status-dot"></span>Closed
                                        </span>
                                    @else
                                        <span class="sg-port-status sg-port-operational">
                                            <span class="sg-port-status-dot"></span>Active
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="sg-td-center" style="font-size:11px;color:#94a3b8">
                                @if($port->latitude && $port->longitude)
                                    {{ number_format((float)$port->latitude, 3) }}°,
                                    {{ number_format((float)$port->longitude, 3) }}°
                                @else
                                    —
                                @endif
                            </td>
                            <td class="sg-td-center" style="color:#64748b; font-size:13px">
                                {{ $port->capacity ?? '—' }}
                            </td>
                            <td class="sg-td-center">
                                @if($port->latitude && $port->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $port->latitude }},{{ $port->longitude }}"
                                       target="_blank" rel="noopener"
                                       class="sg-btn sg-btn-xs" style="background:rgba(59, 130, 246, 0.15);color:#60A5FA;border-color:rgba(59, 130, 246, 0.3);display:inline-flex;align-items:center;gap:4px">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        Maps
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    @if($port->trashed())
                                        <form action="{{ route('ports.restore', $port->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore Port">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('ports.show', $port->id) }}" class="sg-btn sg-btn-xs sg-btn-secondary">
                                            <i data-lucide="eye" class="w-3 h-3"></i> View
                                        </a>
                                        <a href="{{ route('ports.edit', $port->id) }}" class="sg-btn sg-btn-xs sg-btn-warning">Edit</a>
                                        <form action="{{ route('ports.destroy', $port->id) }}" method="POST" class="sg-delete-form" onsubmit="return confirm('Delete this port?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger">Del</button>
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
                                        <i data-lucide="anchor" class="w-8 h-8"></i>
                                    </div>
                                    <h3>No Ports Found</h3>
                                    <p>Import ports from World Port Index, or add records manually.</p>
                                    <a href="{{ route('ports.import') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                        Import Now
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($ports, 'hasPages') && $ports->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $ports->firstItem() }}</strong>–<strong>{{ $ports->lastItem() }}</strong> of <strong>{{ $ports->total() }}</strong> ports
                </div>
                <div class="sg-pagination-nav">
                    {{ $ports->withQueryString()->links() }}
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