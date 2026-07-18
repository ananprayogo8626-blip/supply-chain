<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" 
                             style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid var(--sg-border);">
                    @else
                        <div style="width:60px; height:60px; border-radius:50%; background:linear-gradient(135deg, #FF6B00, #FF8C00); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:24px; border:2px solid var(--sg-border);">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="sg-crud-title">Edit User</h1>
                        <p class="sg-crud-description">
                            {{ $user->name }} — #{{ $user->id }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('users.show', $user->id) }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    View
                </a>
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="sg-flash sg-flash-error" style="margin-bottom:20px;">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="edit" class="w-5 h-5 text-orange-400"></i>
                <h2 class="sg-data-title">Edit User Information</h2>
            </div>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Name -->
                <div>
                    <label class="sg-form-label">Full Name <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="name" class="sg-form-input"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Enter full name" required>
                </div>

                <!-- Email -->
                <div>
                    <label class="sg-form-label">Email Address <span style="color:var(--sg-danger);">*</span></label>
                    <input type="email" name="email" class="sg-form-input"
                           value="{{ old('email', $user->email) }}"
                           placeholder="user@example.com" required>
                </div>

                <!-- Role -->
                <div>
                    <label class="sg-form-label">Role <span style="color:var(--sg-danger);">*</span></label>
                    <select name="role" class="sg-form-input" required>
                        <option value="">-- Select Role --</option>
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="analyst" {{ old('role', $user->role) === 'analyst' ? 'selected' : '' }}>Analyst</option>
                        <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>Viewer</option>
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label class="sg-form-label">Phone Number</label>
                    <input type="text" name="phone" class="sg-form-input"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="+62 812 3456 7890">
                </div>

                <!-- Password (optional) -->
                <div>
                    <label class="sg-form-label">New Password</label>
                    <input type="password" name="password" class="sg-form-input"
                           placeholder="Leave blank to keep current password">
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="sg-form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="sg-form-input"
                           placeholder="Re-enter new password">
                </div>

                <!-- Profile Photo (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="sg-form-input"
                           accept="image/jpeg,image/png,image/jpg,image/gif">
                    @if($user->profile_photo)
                        <div style="margin-top:10px;">
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Current Photo"
                                 style="height:100px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                        </div>
                    @endif
                </div>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update User
                </button>
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-secondary">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Cancel
                </a>
                @if(!$user->isSuperAdmin() && auth()->id() !== $user->id)
                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                      style="margin:0;"
                      onsubmit="return confirm('Delete this user? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sg-btn sg-btn-danger">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </form>
    </div>

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
        .sg-form-input option {
            background: #1B2433;
            color: var(--sg-text-primary);
        }
    </style>
</x-app-layout>
