<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" 
                             style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--sg-border);">
                    @else
                        <div style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, #FF6B00, #FF8C00); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:32px; border:3px solid var(--sg-border);">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="sg-crud-title">{{ $user->name }}</h1>
                        <p class="sg-crud-description">
                            {{ $user->email }} • {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('users.edit', $user->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit User
                </a>
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px; margin-bottom:24px;">
        
        <!-- Profile Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="user" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">Profile</h2>
                </div>
            </div>
            <div style="padding:24px; text-align:center;">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" 
                         style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid var(--sg-border); margin:0 auto 16px;">
                @else
                    <div style="width:120px; height:120px; border-radius:50%; background:linear-gradient(135deg, #FF6B00, #FF8C00); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:48px; margin:0 auto 16px; border:4px solid var(--sg-border);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <h3 style="font-size:24px; font-weight:700; color:var(--sg-text-primary); margin:0 0 8px 0;">
                    {{ $user->name }}
                </h3>
                <p style="color:var(--sg-text-secondary); margin:0 0 16px 0;">
                    {{ $user->email }}
                </p>
                <span class="sg-badge {{ match($user->role) {
                    'super_admin' => 'critical',
                    'admin' => 'high',
                    'analyst' => 'medium',
                    'viewer' => 'low',
                    default => 'low'
                }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </div>
        </div>

        <!-- Information Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="info" class="w-5 h-5 text-purple-400"></i>
                    <h2 class="sg-data-title">User Information</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">User ID</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $user->id }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Email</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $user->email }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Role</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Phone</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Status</span>
                        @if($user->email_verified_at)
                            <span class="sg-badge low">Active</span>
                        @else
                            <span class="sg-badge high">Inactive</span>
                        @endif
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:16px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Email Verified</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">
                            {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i') : '—' }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Created At</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">
                            {{ $user->created_at->format('M d, Y H:i:s') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Form -->
    @if(auth()->user()->hasAdminAccess() && !$user->isSuperAdmin())
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="key" class="w-5 h-5 text-orange-400"></i>
                <h2 class="sg-data-title">Reset Password</h2>
            </div>
        </div>
        <form action="{{ route('users.reset-password', $user->id) }}" method="POST" style="padding:24px;">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label class="sg-form-label">New Password <span style="color:var(--sg-danger);">*</span></label>
                    <input type="password" name="password" class="sg-form-input" placeholder="Minimum 8 characters" required>
                </div>
                <div>
                    <label class="sg-form-label">Confirm Password <span style="color:var(--sg-danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="sg-form-input" placeholder="Re-enter password" required>
                </div>
            </div>
            <button type="submit" class="sg-btn sg-btn-warning">
                <i data-lucide="key" class="w-4 h-4"></i>
                Reset Password
            </button>
        </form>
    </div>
    @endif

    <style>
        .sg-form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--sg-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
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
