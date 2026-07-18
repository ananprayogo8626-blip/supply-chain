<x-app-layout>

    <!-- Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <h1 class="sg-crud-title">Settings</h1>
                <p class="sg-crud-description">System configuration and management</p>
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

    <div x-data="{ activeTab: 'system' }" class="sg-data-card">
        <!-- Tabs -->
        <div style="display:flex; gap:4px; padding:16px; border-bottom:1px solid var(--sg-border);">
            <button @click="activeTab = 'system'" :class="activeTab === 'system' ? 'sg-tab-active' : 'sg-tab'" class="sg-tab">
                <i data-lucide="settings" class="w-4 h-4"></i>
                System
            </button>
            <button @click="activeTab = 'api'" :class="activeTab === 'api' ? 'sg-tab-active' : 'sg-tab'" class="sg-tab">
                <i data-lucide="key" class="w-4 h-4"></i>
                API
            </button>
            <button @click="activeTab = 'theme'" :class="activeTab === 'theme' ? 'sg-tab-active' : 'sg-tab'" class="sg-tab">
                <i data-lucide="palette" class="w-4 h-4"></i>
                Theme
            </button>
            @if(auth()->user()->isSuperAdmin())
            <button @click="activeTab = 'backup'" :class="activeTab === 'backup' ? 'sg-tab-active' : 'sg-tab'" class="sg-tab">
                <i data-lucide="database" class="w-4 h-4"></i>
                Backup
            </button>
            <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'sg-tab-active' : 'sg-tab'" class="sg-tab">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Logs
            </button>
            @endif
        </div>

        <!-- System Settings -->
        <div x-show="activeTab === 'system'" style="padding:24px;">
            <div class="sg-data-head" style="margin-bottom:20px;">
                <div class="sg-data-head-left">
                    <i data-lucide="settings" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">System Settings</h2>
                </div>
            </div>

            <form action="{{ route('settings.system') }}" method="POST">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div>
                        <label class="sg-form-label">Application Name</label>
                        <input type="text" name="app_name" class="sg-form-input" value="{{ config('app.name') }}" required>
                    </div>
                    <div>
                        <label class="sg-form-label">Timezone</label>
                        <select name="app_timezone" class="sg-form-input" required>
                            @foreach(['UTC', 'Asia/Jakarta', 'Asia/Singapore', 'America/New_York', 'Europe/London', 'Asia/Tokyo'] as $timezone)
                                <option value="{{ $timezone }}" {{ config('app.timezone') === $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="sg-form-label">Locale</label>
                        <select name="app_locale" class="sg-form-input" required>
                            <option value="en" {{ config('app.locale') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="id" {{ config('app.locale') === 'id' ? 'selected' : '' }}>Indonesian</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save System Settings
                </button>
            </form>
        </div>

        <!-- API Settings -->
        <div x-show="activeTab === 'api'" style="padding:24px;">
            <div class="sg-data-head" style="margin-bottom:20px;">
                <div class="sg-data-head-left">
                    <i data-lucide="key" class="w-5 h-5 text-purple-400"></i>
                    <h2 class="sg-data-title">API Configuration</h2>
                </div>
            </div>

            <form action="{{ route('settings.api') }}" method="POST">
                @csrf
                <div style="display:grid; grid-template-columns:1fr; gap:20px; margin-bottom:20px;">
                    <div>
                        <label class="sg-form-label">OpenWeather API Key</label>
                        <input type="text" name="openweather_api_key" class="sg-form-input" value="{{ env('OPENWEATHER_API_KEY') }}">
                        <p style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">API key for weather data</p>
                    </div>
                    <div>
                        <label class="sg-form-label">Exchange Rate API Key</label>
                        <input type="text" name="exchange_rate_api_key" class="sg-form-input" value="{{ env('EXCHANGE_RATE_API_KEY') }}">
                        <p style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">API key for currency exchange rates</p>
                    </div>
                    <div>
                        <label class="sg-form-label">GNews API Key</label>
                        <input type="text" name="gnews_api_key" class="sg-form-input" value="{{ env('GNEWS_API_KEY') }}">
                        <p style="font-size:12px; color:var(--sg-text-secondary); margin-top:4px;">API key for news articles</p>
                    </div>
                </div>
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save API Settings
                </button>
            </form>
        </div>

        <!-- Theme Settings -->
        <div x-show="activeTab === 'theme'" style="padding:24px;">
            <div class="sg-data-head" style="margin-bottom:20px;">
                <div class="sg-data-head-left">
                    <i data-lucide="palette" class="w-5 h-5 text-orange-400"></i>
                    <h2 class="sg-data-title">Theme Settings</h2>
                </div>
            </div>

            <form action="{{ route('settings.theme') }}" method="POST">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div>
                        <label class="sg-form-label">Theme Mode</label>
                        <select name="theme" class="sg-form-input" required>
                            <option value="dark" {{ session('theme') === 'dark' ? 'selected' : '' }}>Dark Mode</option>
                            <option value="light" {{ session('theme') === 'light' ? 'selected' : '' }}>Light Mode</option>
                        </select>
                    </div>
                    <div>
                        <label class="sg-form-label">Primary Color</label>
                        <select name="primary_color" class="sg-form-input" required>
                            <option value="#FF6B00" {{ session('primary_color') === '#FF6B00' ? 'selected' : '' }}>Orange</option>
                            <option value="#3B82F6" {{ session('primary_color') === '#3B82F6' ? 'selected' : '' }}>Blue</option>
                            <option value="#10B981" {{ session('primary_color') === '#10B981' ? 'selected' : '' }}>Green</option>
                            <option value="#8B5CF6" {{ session('primary_color') === '#8B5CF6' ? 'selected' : '' }}>Purple</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="sg-btn sg-btn-gradient">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Theme Settings
                </button>
            </form>
        </div>

        <!-- Backup & Restore -->
        @if(auth()->user()->isSuperAdmin())
        <div x-show="activeTab === 'backup'" style="padding:24px;">
            <div class="sg-data-head" style="margin-bottom:20px;">
                <div class="sg-data-head-left">
                    <i data-lucide="database" class="w-5 h-5 text-green-400"></i>
                    <h2 class="sg-data-title">Backup & Restore</h2>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
                <!-- Backup -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                    <h3 style="font-size:18px; font-weight:600; color:var(--sg-text-primary); margin:0 0 16px 0;">Create Backup</h3>
                    <p style="color:var(--sg-text-secondary); font-size:14px; margin-bottom:20px;">
                        Create a full database backup. Backups are stored in the storage/app/backups directory.
                    </p>
                    <form action="{{ route('settings.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="sg-btn sg-btn-teal">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Create Backup
                        </button>
                    </form>
                </div>

                <!-- Restore -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                    <h3 style="font-size:18px; font-weight:600; color:var(--sg-text-primary); margin:0 0 16px 0;">Restore Backup</h3>
                    <p style="color:var(--sg-text-secondary); font-size:14px; margin-bottom:20px;">
                        Restore database from a previous backup. This will replace all current data.
                    </p>
                    <form action="{{ route('settings.restore') }}" method="POST" onsubmit="return confirm('Are you sure you want to restore? This will replace all current data.');">
                        @csrf
                        <select name="backup_file" class="sg-form-input" style="margin-bottom:12px;" required>
                            <option value="">-- Select Backup File --</option>
                            @foreach($backups ?? [] as $backup)
                                <option value="{{ $backup['name'] }}">{{ $backup['name'] }} ({{ $backup['size'] }} - {{ $backup['date'] }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="sg-btn sg-btn-danger">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            Restore Backup
                        </button>
                    </form>
                </div>
            </div>

            <!-- Clear Cache -->
            <div class="bg-white/5 border border-white/10 rounded-xl p-6" style="margin-top:24px;">
                <h3 style="font-size:18px; font-weight:600; color:var(--sg-text-primary); margin:0 0 16px 0;">Clear Application Cache</h3>
                <p style="color:var(--sg-text-secondary); font-size:14px; margin-bottom:20px;">
                    Clear all application cache including config, routes, views, and compiled files.
                </p>
                <form action="{{ route('settings.clear-cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="sg-btn sg-btn-warning">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Clear All Cache
                    </button>
                </form>
            </div>
        </div>

        <!-- Logs -->
        <div x-show="activeTab === 'logs'" style="padding:24px;">
            <div class="sg-data-head" style="margin-bottom:20px;">
                <div class="sg-data-head-left">
                    <i data-lucide="file-text" class="w-5 h-5 text-red-400"></i>
                    <h2 class="sg-data-title">System Logs</h2>
                </div>
            </div>

            <form action="{{ route('settings.logs') }}" method="GET" style="margin-bottom:20px;">
                <div style="display:flex; gap:12px;">
                    <select name="log_type" class="sg-form-input" style="width:200px;">
                        <option value="laravel">Laravel Log</option>
                        <option value="error">Error Log</option>
                    </select>
                    <input type="number" name="lines" class="sg-form-input" style="width:120px;" value="100" placeholder="Lines">
                    <button type="submit" class="sg-btn sg-btn-secondary">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        View Logs
                    </button>
                </div>
            </form>

            <div style="background:#0f172a; border:1px solid var(--sg-border); border-radius:12px; padding:16px; max-height:400px; overflow-y:auto;">
                <pre style="margin:0; font-family:'Courier New', monospace; font-size:12px; color:#10b981; white-space:pre-wrap;">{{ $logs ?? 'No logs available.' }}</pre>
            </div>
        </div>
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
