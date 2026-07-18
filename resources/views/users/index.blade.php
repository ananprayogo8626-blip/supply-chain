<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <h1 class="sg-crud-title">User Management</h1>
                <p class="sg-crud-description">Kelola pengguna dan hak akses sistem</p>
            </div>
            <div class="sg-crud-header-actions" style="display:flex; gap:8px;">
                <a href="{{ route('users.export') }}" class="sg-btn sg-btn-sm sg-btn-teal">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </a>
                <a href="{{ route('users.export-pdf') }}" class="sg-btn sg-btn-sm sg-btn-secondary" target="_blank">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Export PDF
                </a>
                <a href="{{ route('users.create') }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add User
                </a>
            </div>
        </div>
    </div>

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

    <!-- Import CSV section -->
    <div class="sg-data-card" style="margin-bottom:20px; padding:16px;">
        <h3 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 10px 0;">Import Users from CSV</h3>
        <form action="{{ route('users.import-csv') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            @csrf
            <input type="file" name="file" accept=".csv" required class="sg-form-input" style="width:auto; flex:1; min-width:200px;">
            <button type="submit" class="sg-btn sg-btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import CSV
            </button>
        </form>
    </div>

    <!-- Toolbar -->
    <div class="sg-data-card" style="margin-bottom:20px; padding:16px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <form method="GET" action="{{ route('users.index') }}" style="display:flex; gap:12px; flex:1;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="sg-form-input" style="flex:1; min-width:200px;">
                <select name="role" class="sg-form-input" style="width:150px;">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="analyst" {{ request('role') === 'analyst' ? 'selected' : '' }}>Analyst</option>
                    <option value="viewer" {{ request('role') === 'viewer' ? 'selected' : '' }}>Viewer</option>
                </select>
                <select name="status" class="sg-form-input" style="width:150px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
                </select>
                <button type="submit" class="sg-btn sg-btn-secondary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Search
                </button>
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-secondary">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Clear
                </a>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                <h2 class="sg-data-title">All Users
                    <span class="sg-count-badge">{{ $users->total() }} users</span>
                </h2>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="sg-data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" 
                                             style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--sg-border);">
                                    @else
                                        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, #FF6B00, #FF8C00); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:14px;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600; color:var(--sg-text-primary);">{{ $user->name }}</div>
                                        <div style="font-size:12px; color:var(--sg-text-secondary);">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="color:var(--sg-text-primary);">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="sg-badge {{ match($user->role) {
                                    'super_admin' => 'critical',
                                    'admin' => 'high',
                                    'analyst' => 'medium',
                                    'viewer' => 'low',
                                    default => 'low'
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td>
                                <span style="color:var(--sg-text-primary);">{{ $user->phone ?? '—' }}</span>
                            </td>
                            <td>
                                @if($user->trashed())
                                    <span class="sg-badge high">Deleted</span>
                                @elseif($user->email_verified_at)
                                    <span class="sg-badge low">Active</span>
                                @else
                                    <span class="sg-badge high">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span style="color:var(--sg-text-secondary); font-size:13px;">
                                    {{ $user->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    @if($user->trashed())
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="sg-btn sg-btn-xs sg-btn-teal" title="Restore User">
                                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('users.show', $user->id) }}" class="sg-btn sg-btn-xs sg-btn-secondary" title="View">
                                            <i data-lucide="eye" class="w-3 h-3"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" title="Edit">
                                            <i data-lucide="edit" class="w-3 h-3"></i>
                                        </a>
                                        @if(!$user->isSuperAdmin() && auth()->id() !== $user->id)
                                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="sg-btn sg-btn-xs {{ $user->email_verified_at ? 'sg-btn-secondary' : 'sg-btn-teal' }}" title="{{ $user->email_verified_at ? 'Deactivate' : 'Activate' }}">
                                                    <i data-lucide="{{ $user->email_verified_at ? 'user-x' : 'user-check' }}" class="w-3 h-3"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" title="Delete">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px;">
                                <div style="color:var(--sg-text-secondary);">
                                    <i data-lucide="users" class="w-12 h-12" style="display:block; margin:0 auto 12px; opacity:0.5;"></i>
                                    <p>No users found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="sg-pagination">
                <div class="sg-pagination-info">
                    Showing <strong>{{ $users->firstItem() }}</strong>–<strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> users
                </div>
                <div class="sg-pagination-nav">
                    {{ $users->withQueryString()->links() }}
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
        .sg-form-input option {
            background: #1B2433;
            color: var(--sg-text-primary);
        }
    </style>
</x-app-layout>
