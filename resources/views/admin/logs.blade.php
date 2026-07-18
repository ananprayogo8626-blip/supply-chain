<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <h1 class="sg-crud-title">System & Audit Logs</h1>
                <p class="sg-crud-description">Lihat log aktivitas user, log login, dan riwayat kegagalan API sync</p>
            </div>
        </div>
    </div>

    <!-- Active Tab state via PHP -->
    @php
        $activeTab = request('tab', 'activity');
    @endphp

    <div class="sg-data-card">
        <!-- Tabs -->
        <div style="display:flex; gap:4px; padding:16px; border-bottom:1px solid var(--sg-border);">
            <a href="{{ route('admin.logs', ['tab' => 'activity', 'search' => request('search')]) }}" class="sg-tab {{ $activeTab === 'activity' ? 'sg-tab-active' : '' }}">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Activity Log
            </a>
            <a href="{{ route('admin.logs', ['tab' => 'login', 'search' => request('search')]) }}" class="sg-tab {{ $activeTab === 'login' ? 'sg-tab-active' : '' }}">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Login Log
            </a>
            <a href="{{ route('admin.logs', ['tab' => 'api', 'search' => request('search')]) }}" class="sg-tab {{ $activeTab === 'api' ? 'sg-tab-active' : '' }}">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                API Sync Log
            </a>
        </div>

        <!-- Toolbar / Search -->
        <div style="padding:16px; border-bottom:1px solid var(--sg-border);">
            <form method="GET" action="{{ route('admin.logs') }}" style="display:flex; gap:12px; max-width:500px;">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." class="sg-form-input">
                <button type="submit" class="sg-btn sg-btn-secondary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.logs', ['tab' => $activeTab]) }}" class="sg-btn sg-btn-secondary">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- 1. Activity Logs Tab Content -->
        @if($activeTab === 'activity')
            <div style="overflow-x:auto;">
                <table class="sg-data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Model</th>
                            <th>IP Address</th>
                            <th>Browser/OS</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight:600; color:var(--sg-text-primary);">{{ $log->user->name ?? 'System' }}</div>
                                    <div style="font-size:11px; color:var(--sg-text-muted);">{{ $log->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="sg-badge {{ in_array($log->action, ['Delete', 'Destroy']) ? 'high' : (in_array($log->action, ['Create', 'Store']) ? 'low' : 'medium') }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="max-width:300px; white-space:normal; color:var(--sg-text-primary);">
                                    {{ $log->description }}
                                </td>
                                <td style="color:var(--sg-text-secondary); font-size:12px;">
                                    @if($log->model_type)
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="color:var(--sg-text-primary);">{{ $log->ip_address ?? '—' }}</td>
                                <td style="font-size:12px; color:var(--sg-text-secondary);">
                                    {{ $log->browser ?? 'Unknown' }} / {{ $log->platform ?? 'Unknown' }}
                                </td>
                                <td style="font-size:12px; color:var(--sg-text-muted);">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color:var(--sg-text-secondary);">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activityLogs->hasPages())
                <div class="sg-pagination">
                    <div class="sg-pagination-info">
                        Showing {{ $activityLogs->firstItem() }}–{{ $activityLogs->lastItem() }} of {{ $activityLogs->total() }} entries
                    </div>
                    <div class="sg-pagination-nav">
                        {{ $activityLogs->links() }}
                    </div>
                </div>
            @endif
        @endif

        <!-- 2. Login Logs Tab Content -->
        @if($activeTab === 'login')
            <div style="overflow-x:auto;">
                <table class="sg-data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Device / OS</th>
                            <th>User Agent</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight:600; color:var(--sg-text-primary);">{{ $log->user->name ?? 'System' }}</div>
                                    <div style="font-size:11px; color:var(--sg-text-muted);">{{ $log->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="sg-badge {{ $log->action === 'Login' ? 'low' : 'high' }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td style="color:var(--sg-text-primary);">{{ $log->description }}</td>
                                <td style="color:var(--sg-text-primary);">{{ $log->ip_address ?? '—' }}</td>
                                <td style="font-size:12px; color:var(--sg-text-secondary);">
                                    {{ $log->browser ?? 'Unknown' }} / {{ $log->platform ?? 'Unknown' }}
                                </td>
                                <td style="font-size:11px; color:var(--sg-text-muted); max-width:200px; truncate;" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 40) }}
                                </td>
                                <td style="font-size:12px; color:var(--sg-text-muted);">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color:var(--sg-text-secondary);">
                                    No login logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($loginLogs->hasPages())
                <div class="sg-pagination">
                    <div class="sg-pagination-info">
                        Showing {{ $loginLogs->firstItem() }}–{{ $loginLogs->lastItem() }} of {{ $loginLogs->total() }} entries
                    </div>
                    <div class="sg-pagination-nav">
                        {{ $loginLogs->links() }}
                    </div>
                </div>
            @endif
        @endif

        <!-- 3. API Sync Logs Tab Content -->
        @if($activeTab === 'api')
            <div style="overflow-x:auto;">
                <table class="sg-data-table">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>API Service</th>
                            <th>Country</th>
                            <th>Exception Class</th>
                            <th>Error Message</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiLogs as $log)
                            <tr>
                                <td style="font-family:monospace; font-size:12px; color:var(--sg-text-primary);">
                                    {{ Str::limit($log->batch_id, 8, '') }}...
                                </td>
                                <td>
                                    <span class="sg-badge high">{{ $log->stage }}</span>
                                </td>
                                <td>
                                    <div style="font-weight:600; color:var(--sg-text-primary);">{{ $log->country->country_name ?? '—' }}</div>
                                    <div style="font-size:11px; color:var(--sg-text-muted);">{{ $log->country_code ?? '' }}</div>
                                </td>
                                <td style="font-size:12px; color:var(--sg-text-secondary); max-width:150px; truncate;" title="{{ $log->exception_class }}">
                                    {{ class_basename($log->exception_class) }}
                                </td>
                                <td style="color:var(--sg-danger); font-size:12.5px; max-width:300px; white-space:normal;">
                                    {{ $log->error_message }}
                                </td>
                                <td style="font-size:12px; color:var(--sg-text-muted);">
                                    {{ $log->failed_at ? $log->failed_at->format('Y-m-d H:i:s') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:40px; color:var(--sg-text-secondary);">
                                    No API sync error logs found. All synchronizations successful.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($apiLogs->hasPages())
                <div class="sg-pagination">
                    <div class="sg-pagination-info">
                        Showing {{ $apiLogs->firstItem() }}–{{ $apiLogs->lastItem() }} of {{ $apiLogs->total() }} entries
                    </div>
                    <div class="sg-pagination-nav">
                        {{ $apiLogs->links() }}
                    </div>
                </div>
            @endif
        @endif

    </div>

    <style>
        .sg-tab {
            padding: 10px 16px;
            background: transparent;
            border: none;
            color: var(--sg-text-secondary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .sg-tab:hover {
            background: rgba(255,255,255,0.05);
            color: var(--sg-text-primary);
        }
        .sg-tab-active {
            background: rgba(255,107,0,0.1);
            color: #FF6B00;
            font-weight: 600;
        }
        .sg-form-input {
            flex: 1;
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
