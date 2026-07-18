<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <h1 class="sg-crud-title">Add New User</h1>
                <p class="sg-crud-description">Create a new user account</p>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
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
                <i data-lucide="user-plus" class="w-5 h-5 text-green-400"></i>
                <h2 class="sg-data-title">User Information</h2>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Name -->
                <div>
                    <label class="sg-form-label">Full Name <span style="color:var(--sg-danger);">*</span></label>
                    <input type="text" name="name" class="sg-form-input"
                           value="{{ old('name') }}"
                           placeholder="Enter full name" required>
                </div>

                <!-- Email -->
                <div>
                    <label class="sg-form-label">Email Address <span style="color:var(--sg-danger);">*</span></label>
                    <input type="email" name="email" class="sg-form-input"
                           value="{{ old('email') }}"
                           placeholder="user@example.com" required>
                </div>

                <!-- Password -->
                <div>
                    <label class="sg-form-label">Password <span style="color:var(--sg-danger);">*</span></label>
                    <input type="password" name="password" class="sg-form-input"
                           placeholder="Minimum 8 characters" required>
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="sg-form-label">Confirm Password <span style="color:var(--sg-danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="sg-form-input"
                           placeholder="Re-enter password" required>
                </div>

                <!-- Role -->
                <div>
                    <label class="sg-form-label">Role <span style="color:var(--sg-danger);">*</span></label>
                    <select name="role" class="sg-form-input" required>
                        <option value="">-- Select Role --</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="analyst" {{ old('role') === 'analyst' ? 'selected' : '' }}>Analyst</option>
                        <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>Viewer</option>
                    </select>
                </div>

                <!-- Phone -->
                <div>
                    <label class="sg-form-label">Phone Number</label>
                    <input type="text" name="phone" class="sg-form-input"
                           value="{{ old('phone') }}"
                           placeholder="+62 812 3456 7890">
                </div>

                <!-- Profile Photo (full width) -->
                <div style="grid-column: span 2;">
                    <label class="sg-form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="sg-form-input"
                           accept="image/jpeg,image/png,image/jpg,image/gif">
                    <p style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">
                        Allowed formats: JPEG, PNG, JPG, GIF (Max 2MB)
                    </p>
                </div>
            </div>

            <div style="display:flex; gap:12px;">
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Create User
                </button>
                <a href="{{ route('users.index') }}" class="sg-btn sg-btn-secondary">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Cancel
                </a>
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
