<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">Port Management</h1>
                <p class="sg-page-desc">Global port data sourced from World Port Index — monitor operational status and logistics capacity.</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('ports.import') }}"
                   class="sg-btn sg-btn-outline-orange" id="btn-import-ports">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Ports API
                </a>
                <a href="{{ route('ports.create') }}" class="sg-btn sg-btn-gradient" id="btn-add-port">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Port
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="sg-flash sg-flash-success mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Import Progress -->
    <div id="import-progress" class="sg-data-card mb-4" style="display:none">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <svg fill="none" stroke="#0891b2" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <h2 class="sg-data-title">Import Progress</h2>
            </div>
        </div>
        <div style="padding:20px">
            <div style="margin-bottom:10px">
                <span id="progress-stage">Preparing...</span>
                <span style="float:right" id="progress-percentage">0%</span>
            </div>
            <div style="background:#1e293b;height:8px;border-radius:4px;overflow:hidden">
                <div id="progress-bar" style="background:#0891b2;height:100%;width:0%;transition:width 0.3s"></div>
            </div>
            <div style="margin-top:10px;font-size:12px;color:#94a3b8">
                <span id="progress-processed">0</span> / <span id="progress-total">0</span> ports
            </div>
        </div>
    </div>

    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <svg fill="none" stroke="#0891b2" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12H3l9-9 9 9h-2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7M9 21V12h6v9"/></svg>
                <h2 class="sg-data-title">Port Database
                    <span class="sg-count-badge">{{ $ports->total() }} ports</span>
                </h2>
            </div>
            <div style="font-size:12px;color:#64748b">Source: World Port Index</div>
        </div>

        <!-- Search and Filters -->
        <div style="padding:20px;background:#1e293b;border-bottom:1px solid #334155">
            <form method="GET" action="{{ route('ports.index') }}" style="display:flex;gap:15px;flex-wrap:wrap;align-items:center">
                <div style="flex:1;min-width:200px">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ports..." 
                           style="width:100%;padding:10px 15px;background:#0f172a;border:1px solid #334155;border-radius:6px;color:#f8fafc;font-size:14px">
                </div>
                <div style="min-width:150px">
                    <select name="country" style="width:100%;padding:10px 15px;background:#0f172a;border:1px solid #334155;border-radius:6px;color:#f8fafc;font-size:14px">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:120px">
                    <select name="status" style="width:100%;padding:10px 15px;background:#0f172a;border:1px solid #334155;border-radius:6px;color:#f8fafc;font-size:14px">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Congested" {{ request('status') == 'Congested' ? 'selected' : '' }}>Congested</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div style="min-width:120px">
                    <select name="type" style="width:100%;padding:10px 15px;background:#0f172a;border:1px solid #334155;border-radius:6px;color:#f8fafc;font-size:14px">
                        <option value="">All Types</option>
                        <option value="Seaport" {{ request('type') == 'Seaport' ? 'selected' : '' }}>Seaport</option>
                        <option value="River Port" {{ request('type') == 'River Port' ? 'selected' : '' }}>River Port</option>
                    </select>
                </div>
                <button type="submit" class="sg-btn sg-btn-primary" style="padding:10px 20px">Filter</button>
                <a href="{{ route('ports.index') }}" class="sg-btn sg-btn-outline" style="padding:10px 20px">Clear</a>
            </form>
        </div>

        <div style="overflow-x:auto">
            <table class="sg-data-table" id="ports-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="sg-td-center">#</th>
                        <th>
                            <a href="{{ route('ports.index', array_merge(request()->query(), ['sort' => request('sort') === 'country' ? 'country_desc' : 'country'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Country
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('ports.index', array_merge(request()->query(), ['sort' => request('sort') === 'port_name' ? 'port_name_desc' : 'port_name'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                Port Name
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">UNLOCODE</th>
                        <th>
                            <a href="{{ route('ports.index', array_merge(request()->query(), ['sort' => request('sort') === 'city' ? 'city_desc' : 'city'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px">
                                City
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">Type</th>
                        <th class="sg-td-center">
                            <a href="{{ route('ports.index', array_merge(request()->query(), ['sort' => request('sort') === 'status' ? 'status_desc' : 'status'])) }}"
                               style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:4px;justify-content:center">
                                Status
                                <svg style="width:12px;height:12px;opacity:0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="sg-td-center">Location</th>
                        <th class="sg-td-center">Map</th>
                        <th class="sg-td-center" style="width:110px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $port)
                        <tr>
                            <td class="sg-td-num">{{ $loop->iteration }}</td>
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
                            </td>
                            <td class="sg-td-center" style="font-size:11px;color:#94a3b8">
                                @if($port->latitude && $port->longitude)
                                    {{ number_format((float)$port->latitude, 3) }}°,
                                    {{ number_format((float)$port->longitude, 3) }}°
                                @else
                                    —
                                @endif
                            </td>
                            <td class="sg-td-center">
                                @if($port->latitude && $port->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $port->latitude }},{{ $port->longitude }}"
                                       target="_blank" rel="noopener"
                                       class="sg-btn sg-btn-xs" style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;display:inline-flex;align-items:center;gap:4px">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Maps
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="sg-action-group">
                                    <a href="{{ route('ports.edit', $port->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-port-{{ $port->id }}">Edit</a>
                                    <form action="{{ route('ports.destroy', $port->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this port?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-port-{{ $port->id }}">Del</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="sg-empty">
                                <div class="sg-empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12H3l9-9 9 9h-2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7M9 21V12h6v9"/></svg>
                                </div>
                                <p>No ports yet. Add ports from World Port Index data.</p>
                                <a href="{{ route('ports.create') }}" class="sg-btn sg-btn-primary" id="btn-add-port-empty">Add Port</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ports->hasPages())
            <div style="padding:20px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #334155">
                <div style="font-size:13px;color:#94a3b8">
                    Showing {{ $ports->firstItem() }} to {{ $ports->lastItem() }} of {{ $ports->total() }} ports
                </div>
                <div style="display:flex;gap:5px">
                    @if($ports->onFirstPage())
                        <span class="sg-btn sg-btn-xs sg-btn-disabled">Previous</span>
                    @else
                        <a href="{{ $ports->previousPageUrl() }}" class="sg-btn sg-btn-xs sg-btn-outline">Previous</a>
                    @endif
                    
                    @foreach($ports->getUrlRange(1, $ports->lastPage()) as $page => $url)
                        @if($page == $ports->currentPage())
                            <span class="sg-btn sg-btn-xs sg-btn-primary">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="sg-btn sg-btn-xs sg-btn-outline">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($ports->hasMorePages())
                        <a href="{{ $ports->nextPageUrl() }}" class="sg-btn sg-btn-xs sg-btn-outline">Next</a>
                    @else
                        <span class="sg-btn sg-btn-xs sg-btn-disabled">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for import progress
    const progressDiv = document.getElementById('import-progress');
    if (progressDiv) {
        checkProgress();
    }

    function checkProgress() {
        fetch('/api/import/progress/ports')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'processing' || data.status === 'pending') {
                    progressDiv.style.display = 'block';
                    document.getElementById('progress-stage').textContent = data.stage || 'Processing...';
                    document.getElementById('progress-percentage').textContent = (data.percentage || 0) + '%';
                    document.getElementById('progress-bar').style.width = (data.percentage || 0) + '%';
                    document.getElementById('progress-processed').textContent = data.processed || 0;
                    document.getElementById('progress-total').textContent = data.total || 0;
                    
                    // Continue polling
                    setTimeout(checkProgress, 2000);
                } else if (data.status === 'completed') {
                    progressDiv.style.display = 'block';
                    document.getElementById('progress-stage').textContent = 'Completed';
                    document.getElementById('progress-percentage').textContent = '100%';
                    document.getElementById('progress-bar').style.width = '100%';
                    document.getElementById('progress-processed').textContent = data.processed || 0;
                    document.getElementById('progress-total').textContent = data.total || 0;
                    
                    // Reload page after 2 seconds to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if (data.status === 'failed') {
                    progressDiv.style.display = 'block';
                    document.getElementById('progress-stage').textContent = 'Failed: ' + (data.error_message || 'Unknown error');
                    document.getElementById('progress-bar').style.background = '#ef4444';
                }
            })
            .catch(error => {
                console.error('Error checking progress:', error);
            });
    }
});
</script>