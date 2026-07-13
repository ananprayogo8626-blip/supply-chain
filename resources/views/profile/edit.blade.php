<x-app-layout>
    <div class="sg-profile-container">
        <!-- Page Header -->
        <div class="sg-profile-header">
            <div class="sg-profile-icon">👤</div>
            <div>
                <h1 class="sg-profile-title">My Profile</h1>
                <p class="sg-profile-subtitle">Manage your account information and security.</p>
            </div>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="sg-alert sg-alert-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Profile information updated successfully.
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="sg-alert sg-alert-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Password updated successfully.
            </div>
        @endif

        <!-- Profile Information Card -->
        <div class="sg-profile-card">
            <div class="sg-card-header">
                <h2 class="sg-card-title">Profile Information</h2>
                <p class="sg-card-description">Update your personal information.</p>
            </div>
            
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="sg-profile-form">
                @csrf
                @method('patch')

                <div class="sg-profile-avatar">
                    <div class="sg-avatar">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <button type="button" class="sg-avatar-upload">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Change Photo
                    </button>
                </div>

                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label class="sg-form-label">Full Name</label>
                        <input type="text" id="name" name="name" 
                               class="sg-form-input" 
                               value="{{ old('name', $user->name) }}" 
                               required autofocus autocomplete="name"
                               placeholder="Enter your full name">
                        @error('name')
                            <div class="sg-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label">Email Address</label>
                        <input type="email" id="email" name="email" 
                               class="sg-form-input" 
                               value="{{ old('email', $user->email) }}" 
                               required autocomplete="username"
                               placeholder="Enter your email">
                        @error('email')
                            <div class="sg-form-error">{{ $message }}</div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="sg-verification-notice">
                                <p class="text-sm mt-2 text-slate-400">
                                    Your email address is unverified.
                                    <button form="send-verification" class="sg-link">
                                        Click here to re-send the verification	email.
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm text-green-400">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label class="sg-form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               class="sg-form-input" 
                               value="{{ old('phone', $user->phone ?? '') }}" 
                               autocomplete="tel"
                               placeholder="Enter your phone number (optional)">
                        @error('phone')
                            <div class="sg-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-form-label">Role</label>
                        <input type="text" 
                               class="sg-form-input sg-form-input-readonly" 
                               value="{{ $user->role ?? 'User' }}" 
                               readonly>
                    </div>
                </div>

                <div class="sg-form-group">
                    <label class="sg-form-label">Member Since</label>
                    <input type="text" 
                           class="sg-form-input sg-form-input-readonly" 
                           value="{{ $user->created_at ? $user->created_at->format('F j, Y') : 'N/A' }}" 
                           readonly>
                </div>

                <div class="sg-form-actions">
                    <button type="submit" class="sg-btn sg-btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="sg-profile-card">
            <div class="sg-card-header">
                <h2 class="sg-card-title">Update Password</h2>
                <p class="sg-card-description">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="sg-profile-form">
                @csrf
                @method('put')

                <div class="sg-form-group">
                    <label class="sg-form-label">Current Password</label>
                    <div class="sg-input-wrapper">
                        <input type="password" id="update_password_current_password" name="current_password" 
                               class="sg-form-input" 
                               autocomplete="current-password"
                               placeholder="Enter your current password">
                        <button type="button" class="sg-password-toggle" onclick="togglePassword('update_password_current_password', this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('updatePassword.current_password')
                        <div class="sg-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="sg-form-group">
                    <label class="sg-form-label">New Password</label>
                    <div class="sg-input-wrapper">
                        <input type="password" id="update_password_password" name="password" 
                               class="sg-form-input" 
                               autocomplete="new-password"
                               placeholder="Enter your new password"
                               oninput="checkPasswordStrength(this.value)">
                        <button type="button" class="sg-password-toggle" onclick="togglePassword('update_password_password', this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('updatePassword.password')
                        <div class="sg-form-error">{{ $message }}</div>
                    @enderror
                    <div class="sg-password-strength">
                        <div class="sg-strength-bar">
                            <div class="sg-strength-fill" id="password-strength-bar"></div>
                        </div>
                        <span class="sg-strength-text" id="password-strength-text">Password strength</span>
                    </div>
                </div>

                <div class="sg-form-group">
                    <label class="sg-form-label">Confirm Password</label>
                    <div class="sg-input-wrapper">
                        <input type="password" id="update_password_password_confirmation" name="password_confirmation" 
                               class="sg-form-input" 
                               autocomplete="new-password"
                               placeholder="Confirm your new password">
                        <button type="button" class="sg-password-toggle" onclick="togglePassword('update_password_password_confirmation', this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('updatePassword.password_confirmation')
                        <div class="sg-form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="sg-form-actions">
                    <button type="submit" class="sg-btn sg-btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Account Card -->
        <div class="sg-profile-card sg-card-danger">
            <div class="sg-card-header sg-card-header-danger">
                <div class="sg-danger-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="sg-card-title sg-card-title-danger">Delete Account</h2>
                    <p class="sg-card-description">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                </div>
            </div>

            <button type="button" 
                    class="sg-btn sg-btn-danger"
                    onclick="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('profile.destroy') }}';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);
                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';
                        form.appendChild(method);
                        const password = document.createElement('input');
                        password.type = 'hidden';
                        password.name = 'password';
                        const pwd = prompt('Please enter your password to confirm deletion:');
                        if(pwd) {
                            password.value = pwd;
                            form.appendChild(password);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Account
            </button>
        </div>
    </div>

    <style>
        .sg-profile-container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 30px;
            animation: fadeIn 0.5s ease-in;
            position: relative;
            z-index: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sg-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .sg-profile-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-orange-light));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(255, 107, 0, 0.3);
        }

        .sg-profile-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--sg-text-primary);
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }

        .sg-profile-subtitle {
            font-size: 14px;
            color: var(--sg-text-secondary);
            margin: 5px 0 0 0;
        }

        .sg-profile-card {
            background: rgba(25, 25, 35, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            transition: all 0.3s ease;
            pointer-events: auto;
            position: relative;
            z-index: 5;
        }

        .sg-profile-card:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.45);
        }

        .sg-card-header {
            margin-bottom: 25px;
        }

        .sg-card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--sg-text-primary);
            margin: 0 0 8px 0;
            font-family: 'Outfit', sans-serif;
        }

        .sg-card-description {
            font-size: 14px;
            color: var(--sg-text-secondary);
            margin: 0;
        }

        .sg-profile-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .sg-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
            color: white;
            border: 3px solid var(--accent-orange);
            box-shadow: 0 8px 24px rgba(255, 107, 0, 0.3);
        }

        .sg-avatar-upload {
            padding: 8px 16px;
            background: rgba(255, 107, 0, 0.1);
            border: 1px solid rgba(255, 107, 0, 0.3);
            border-radius: 8px;
            color: var(--accent-orange);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }

        .sg-avatar-upload:hover {
            background: rgba(255, 107, 0, 0.2);
            border-color: var(--accent-orange);
        }

        .sg-profile-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sg-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .sg-form-row {
                grid-template-columns: 1fr;
            }
            .sg-profile-container {
                padding: 20px;
                margin: 20px;
            }
        }

        .sg-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sg-form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--sg-text-secondary);
        }

        .sg-form-input {
            height: 48px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
            pointer-events: auto;
            position: relative;
            z-index: 5;
        }

        .sg-form-input:focus {
            outline: none;
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }

        .sg-form-input::placeholder {
            color: #9CA3AF;
        }

        .sg-form-input-readonly {
            background: rgba(255, 255, 255, 0.02);
            cursor: not-allowed;
        }

        .sg-form-error {
            font-size: 12px;
            color: var(--sg-danger);
            margin-top: 4px;
        }

        .sg-input-wrapper {
            position: relative;
        }

        .sg-password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--sg-text-secondary);
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
            pointer-events: auto;
            position: relative;
            z-index: 15;
        }

        .sg-password-toggle:hover {
            color: var(--accent-orange);
        }

        .sg-password-strength {
            margin-top: 8px;
        }

        .sg-strength-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .sg-strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .sg-strength-text {
            font-size: 12px;
            color: var(--sg-text-secondary);
        }

        .sg-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .sg-btn {
            height: 48px;
            padding: 0 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }

        .sg-btn-primary {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-orange-light));
            color: white;
        }

        .sg-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 107, 0, 0.4);
        }

        .sg-btn-danger {
            background: var(--sg-danger);
            color: white;
        }

        .sg-btn-danger:hover {
            background: #DC2626;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
        }

        .sg-card-danger {
            border-color: rgba(239, 68, 68, 0.3);
        }

        .sg-card-header-danger {
            display: flex;
            gap: 16px;
        }

        .sg-card-title-danger {
            color: var(--sg-danger);
        }

        .sg-danger-icon {
            width: 48px;
            height: 48px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sg-danger);
        }

        .sg-alert {
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sg-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--sg-success);
        }

        .sg-link {
            background: none;
            border: none;
            color: var(--accent-orange);
            text-decoration: underline;
            cursor: pointer;
            font-size: 13px;
        }

        .sg-link:hover {
            color: var(--accent-orange-hover);
        }

        .sg-verification-notice {
            margin-top: 12px;
            padding: 12px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
        }
    </style>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('svg');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            const colors = ['#EF4444', '#F59E0B', '#10B981', '#10B981'];
            const texts = ['Weak', 'Fair', 'Good', 'Strong'];
            
            strengthBar.style.width = (strength * 25) + '%';
            strengthBar.style.backgroundColor = colors[strength - 1] || '#EF4444';
            strengthText.textContent = texts[strength - 1] || 'Password strength';
            strengthText.style.color = colors[strength - 1] || '#9CA3AF';
        }
    </script>
</x-app-layout>
