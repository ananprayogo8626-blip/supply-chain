<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SupplyGuard — Global Supply Chain Risk Intelligence</title>
    <meta name="description" content="Real-time supply chain risk monitoring platform powered by live global data APIs">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/supplyguard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ time() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}" defer></script>
    <script>
        // Default to dark mode/navy theme
        document.documentElement.classList.add('dark-mode');
    </script>
    @stack('head')
</head>
<body>
    <!-- Background Glow Elements -->
    <div class="sg-bg-glow-container">
        <div class="sg-glow-circle circle-1"></div>
        <div class="sg-glow-circle circle-2"></div>
        <div class="sg-glow-circle circle-3"></div>
    </div>

    <div class="sg-app" x-data="{ sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', mobileMenuOpen: false }" :class="{ 'sidebar-collapsed': sidebarCollapsed, 'sidebar-open': mobileMenuOpen }">
        
        {{-- Sidebar --}}
        <aside class="sg-sidebar">
            <div class="sg-sidebar-logo">
                <div style="display:flex; align-items:center; gap:10px">
                    <div class="sg-sidebar-logo-icon">
                        <i data-lucide="shield-alert" style="width: 20px; height: 20px; color: #fff;"></i>
                    </div>
                    <div class="sg-sidebar-logo-text" x-show="!sidebarCollapsed">
                        <span>SupplyGuard</span>
                        <span>Risk Intelligence</span>
                    </div>
                </div>
                <!-- Collapse Button -->
                <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)" class="sg-collapse-btn" style="margin-left: auto;" :style="sidebarCollapsed ? 'margin: 0 auto;' : ''" title="Collapse Sidebar">
                    <i :data-lucide="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" style="width:16px;height:16px;"></i>
                </button>
            </div>

            <nav class="sg-nav">
                <div class="sg-nav-section" x-show="!sidebarCollapsed">Main Control</div>

                <a href="{{ route('dashboard') }}" class="sg-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" id="nav-dashboard" :title="sidebarCollapsed ? 'Dashboard' : ''">
                    <i data-lucide="layout-dashboard"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Dashboard</span>
                </a>

                <a href="{{ route('dashboard.minimalist') }}" class="sg-nav-link {{ request()->routeIs('dashboard.minimalist') ? 'active' : '' }}" id="nav-dashboard-minimalist" :title="sidebarCollapsed ? 'Minimalist' : ''">
                    <i data-lucide="layout-grid"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Minimalist View</span>
                </a>

                <a href="{{ route('watchlists.index') }}" class="sg-nav-link {{ request()->routeIs('watchlists.*') ? 'active' : '' }}" id="nav-watchlist" :title="sidebarCollapsed ? 'Watchlist' : ''">
                    <i data-lucide="list"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Watchlist</span>
                </a>

                <a href="{{ route('comparison') }}" class="sg-nav-link {{ request()->routeIs('comparison') ? 'active' : '' }}" id="nav-compare" :title="sidebarCollapsed ? 'Compare' : ''">
                    <i data-lucide="git-compare"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Compare</span>
                </a>

                <a href="{{ route('map') }}" class="sg-nav-link {{ request()->routeIs('map') ? 'active' : '' }}" id="nav-map" :title="sidebarCollapsed ? 'Global Map' : ''">
                    <i data-lucide="map"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Global Map</span>
                </a>

                <div class="sg-nav-section" x-show="!sidebarCollapsed">Risk Intelligence</div>

                <a href="{{ route('countries.index') }}" class="sg-nav-link {{ request()->routeIs('countries.*') ? 'active' : '' }}" id="nav-countries" :title="sidebarCollapsed ? 'Countries' : ''">
                    <i data-lucide="globe"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Countries</span>
                </a>

                <a href="{{ route('ports.index') }}" class="sg-nav-link {{ request()->routeIs('ports.*') ? 'active' : '' }}" id="nav-ports" :title="sidebarCollapsed ? 'Ports' : ''">
                    <i data-lucide="anchor"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Ports</span>
                </a>

                <a href="{{ route('news.index') }}" class="sg-nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" id="nav-news" :title="sidebarCollapsed ? 'News' : ''">
                    <i data-lucide="newspaper"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">News & Events</span>
                </a>

                <a href="{{ route('risk-scores.index') }}" class="sg-nav-link {{ request()->routeIs('risk-scores.*') ? 'active' : '' }}" id="nav-risk" :title="sidebarCollapsed ? 'Risk Scores' : ''">
                    <i data-lucide="shield-check"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Risk Scores</span>
                </a>

                @if(auth()->check() && auth()->user()->hasAdminAccess())
                <div class="sg-nav-section" x-show="!sidebarCollapsed">Admin Panel</div>

                <a href="{{ route('admin.dashboard') }}" class="sg-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" id="nav-admin-dashboard" :title="sidebarCollapsed ? 'Admin Dashboard' : ''">
                    <i data-lucide="layout-dashboard"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Dashboard Admin</span>
                </a>

                <a href="{{ route('users.index') }}" class="sg-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" id="nav-users" :title="sidebarCollapsed ? 'Users' : ''">
                    <i data-lucide="users"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Kelola User</span>
                </a>

                <a href="{{ route('admin.api-management') }}" class="sg-nav-link {{ request()->routeIs('admin.api-management') ? 'active' : '' }}" id="nav-api-management" :title="sidebarCollapsed ? 'API Management' : ''">
                    <i data-lucide="cpu"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">API Management</span>
                </a>

                <a href="{{ route('admin.risk-intelligence') }}" class="sg-nav-link {{ request()->routeIs('admin.risk-intelligence') ? 'active' : '' }}" id="nav-risk-intelligence" :title="sidebarCollapsed ? 'Risk Intelligence' : ''">
                    <i data-lucide="shield-alert"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Risk Intelligence</span>
                </a>

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.logs') }}" class="sg-nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}" id="nav-admin-logs" :title="sidebarCollapsed ? 'Logs' : ''">
                    <i data-lucide="file-text"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Logs (Activity & API)</span>
                </a>

                <a href="{{ route('settings.index') }}" class="sg-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" id="nav-settings" :title="sidebarCollapsed ? 'Settings' : ''">
                    <i data-lucide="settings"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">System Setting</span>
                </a>
                @endif
                @endif

                <div class="sg-nav-section" x-show="!sidebarCollapsed">Account</div>

                <a href="{{ route('profile.edit') }}" class="sg-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" id="nav-profile" :title="sidebarCollapsed ? 'Profile' : ''">
                    <i data-lucide="user"></i>
                    <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Profile</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit()" class="sg-nav-link" id="nav-logout" :title="sidebarCollapsed ? 'Logout' : ''">
                        <i data-lucide="log-out"></i>
                        <span class="sg-nav-link-label" x-show="!sidebarCollapsed">Logout</span>
                    </a>
                </form>
            </nav>
        </aside>

        {{-- Main Wrapper --}}
        <div class="sg-main-wrap">
            {{-- Header --}}
            <header class="sg-header">
                <div class="sg-header-left">
                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="sg-collapse-btn md:hidden" style="margin-right:10px" title="Menu">
                        <i data-lucide="menu" style="width:20px;height:20px"></i>
                    </button>

                    <div class="sg-search-wrap" x-data="{ q: '', results: [], open: false }">
                        <i data-lucide="search" class="sg-search-icon"></i>
                        <input type="text" id="global-search" class="sg-search" placeholder="Search countries, ports..."
                            x-model="q"
                            @input.debounce.300ms="
                                if (q.length < 2) { results = []; open = false; return; }
                                fetch('{{ url('/api/countries') }}?search=' + encodeURIComponent(q))
                                    .then(r => r.json())
                                    .then(j => { results = j.data?.slice(0,6) || []; open = results.length > 0; });
                            "
                            @keydown.escape="open = false">
                        <div x-show="open" @click.away="open = false" class="sg-search-dropdown" x-cloak>
                            <template x-for="c in results" :key="c.id">
                                <a :href="'{{ url('/countries') }}/' + c.id + '/edit'" class="sg-search-item">
                                    <img :src="c.flag" style="width:20px;height:12px;object-fit:cover;border-radius:1px;border:1px solid rgba(255,255,255,0.1)" onerror="this.style.display='none'">
                                    <span x-text="c.country_name" style="font-weight:600;color:var(--sg-text-primary)"></span>
                                    <span x-text="c.country_code" style="font-size:10px;color:var(--sg-text-muted);margin-left:auto"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="sg-header-right">
                    {{-- Live Running Clock Widget --}}
                    <div class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-[rgba(0,210,255,0.18)] bg-[rgba(9,21,39,0.7)] backdrop-blur-md shadow-sm" title="Real-Time System Clock">
                        <i data-lucide="clock" style="width:14px;height:14px;color:var(--accent-cyan)"></i>
                        <span id="global-live-clock" style="font-family:'Courier New', monospace; font-size:12px; font-weight:700; color:#F8FAFC; letter-spacing:0.05em">00:00:00</span>
                    </div>

                    {{-- Notification Button --}}
                    <div class="sg-profile-container" x-data="{ open: false }" style="position:relative">
                        <button id="notif-btn" class="sg-notif-btn" @click="open = !open" title="Notifications">
                            <i data-lucide="bell" style="width:16px;height:16px"></i>
                            <span id="notif-badge" class="sg-notif-badge hidden">0</span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="sg-dropdown-menu show"
                             style="position:absolute;right:0;top:100%;margin-top:8px;width:220px;">
                            <div style="padding:12px;border-bottom:1px solid var(--sg-border)">
                                <div style="font-size:13px;font-weight:600;color:var(--sg-text-primary)">Notifications</div>
                                <div style="font-size:11px;color:var(--sg-text-muted)">No new notifications</div>
                            </div>
                            <div style="padding:8px 12px;font-size:12px;color:var(--sg-text-muted);text-align:center">
                                <a href="#" style="color:var(--accent-orange);text-decoration:none">View All</a>
                            </div>
                        </div>
                    </div>

                    @php
                        $userName = Auth::user()->name ?? 'Admin';
                        $parts = explode(' ', $userName);
                        $initials = strtoupper(substr($parts[0], 0, 1)) . strtoupper(substr($parts[1] ?? '', 0, 1));
                    @endphp
                    <div class="sg-profile-container" x-data="{ open: false }" style="position:relative">
                        <div class="sg-profile-btn" id="profile-btn" @click="open = !open" style="cursor:pointer;user-select:none">
                            <div class="sg-avatar" id="header-avatar">{{ $initials }}</div>
                            <div class="hidden sm:block">
                                <div class="sg-profile-name">{{ $userName }}</div>
                                <div class="sg-profile-role">Chief Risk Officer</div>
                            </div>
                            <i data-lucide="chevron-down" style="width:13px;height:13px;color:var(--sg-text-muted)"></i>
                        </div>
                        <div x-show="open" @click.away="open = false" x-cloak class="sg-dropdown-menu show"
                             style="position:absolute;right:0;top:100%;margin-top:8px;width:180px;">
                            <a href="{{ route('profile.edit') }}" class="sg-dropdown-item">
                                <i data-lucide="user" style="width:14px;height:14px"></i>
                                Profile Details
                            </a>
                            <a href="{{ route('profile.edit') }}" class="sg-dropdown-item">
                                <i data-lucide="settings" style="width:14px;height:14px"></i>
                                Settings
                            </a>
                            <div style="border-top:1px solid var(--sg-border);margin:4px 0"></div>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sg-dropdown-item danger">
                                <i data-lucide="log-out" style="width:14px;height:14px"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="sg-content animate-fade-in">
                @isset($header)
                    <div class="sg-page-title" style="margin-bottom:18px">{{ $header }}</div>
                @endisset
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="sg-footer" style="padding:12px 28px;border-top:1px solid var(--sg-border);background:rgba(17,24,39,0.3);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--sg-text-muted);flex-shrink:0;width:100%">
                <div>
                    <strong>SupplyGuard</strong> &copy; {{ date('Y') }} — Global Risk Operations.
                </div>
                <div style="display:inline-flex;align-items:center;gap:12px;">
                    <span>System: <strong style="color:var(--sg-success)">Operational</strong></span>
                    <span class="hidden sm:inline">Version: <strong>3.0.0-Premium</strong></span>
                </div>
            </footer>
        </div>
    </div>

    {{-- Global Loading Overlay --}}
    <div id="global-loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.85);backdrop-filter:blur(8px);z-index:9999;flex-direction:column;align-items:center;justify-content:center;color:#fff;font-family:Inter,sans-serif">
        <div style="background:rgba(22,28,45,0.9);padding:32px 40px;border-radius:16px;box-shadow:var(--sg-shadow-md);text-align:center;max-width:400px;border:1px solid rgba(255,255,255,0.08)">
            {{-- Spinner --}}
            <div style="width:48px;height:48px;border:3px solid rgba(255,255,255,0.08);border-top-color:var(--accent-orange);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 20px auto"></div>
            <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;font-family:'Outfit',sans-serif;color:#fff">Synchronizing Live Data</h3>
            <p style="font-size:12px;color:var(--sg-text-secondary);line-height:1.5" id="global-loading-text">Fetching operational indicators from global REST APIs (World Bank, Open-Meteo, GNews, ExchangeRate)...</p>
            {{-- Progress bar --}}
            <div style="width:100%;height:4px;background:rgba(255,255,255,0.08);border-radius:10px;margin-top:20px;overflow:hidden">
                <div id="global-loading-progress" style="width:0%;height:100%;background:linear-gradient(90deg, var(--accent-orange), var(--accent-blue));border-radius:10px;transition:width 0.4s ease"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide Icons
        lucide.createIcons();

        // Live Ticking Clock (Updates Header Clock & Dashboard Subheader every 1 sec)
        function updateGlobalClock() {
            const clockEl = document.getElementById('global-live-clock');
            const dashDateEl = document.getElementById('dashboard-date');
            const now = new Date();
            
            if (clockEl) {
                clockEl.textContent = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
            
            if (dashDateEl) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateStr = now.toLocaleDateString('en-US', options);
                const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                dashDateEl.innerHTML = dateStr + ' &bull; <span class="text-cyan-400 font-mono font-bold">' + timeStr + '</span> &bull; Live Security Grid';
            }
        }
        updateGlobalClock();
        setInterval(updateGlobalClock, 1000);

        const overlay = document.getElementById('global-loading-overlay');
        const progress = document.getElementById('global-loading-progress');
        const text = document.getElementById('global-loading-text');

        document.addEventListener('click', function(e) {
            const el = e.target.closest('a, button');
            if (!el) return;

            const href = el.getAttribute('href') || '';
            const textContent = el.textContent || '';

            const isSync = href.includes('/sync') || href.includes('/import') || textContent.toLowerCase().includes('sync') || textContent.toLowerCase().includes('import');

            if (isSync) {
                overlay.style.display = 'flex';
                let p = 0;
                const interval = setInterval(() => {
                    p = Math.min(95, p + Math.random() * 15);
                    progress.style.width = p + '%';
                    if (p > 75) {
                        text.textContent = 'Writing payloads to MySQL database and recalculating Country Risk Index...';
                    } else if (p > 40) {
                        text.textContent = 'Parsing API payloads and applying sentiment score weights...';
                    }
                }, 800);
            }
        });

        document.addEventListener('submit', function(e) {
            const form = e.target;
            const action = form.getAttribute('action') || '';
            const isSyncForm = action.includes('/sync') || action.includes('/import');

            if (isSyncForm) {
                overlay.style.display = 'flex';
                let p = 0;
                const interval = setInterval(() => {
                    p = Math.min(95, p + Math.random() * 15);
                    progress.style.width = p + '%';
                }, 800);
            }
        });

        // Global Table CSV Export Helper
        window.exportTableToCSV = function(filename, tableId) {
            const csv = [];
            const table = tableId ? document.getElementById(tableId) : document.querySelector('table');
            if (!table) return;

            const rows = table.querySelectorAll('tr');
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                // Skip rows that are empty or have no columns
                if (cols.length === 0) continue;
                
                let isActionColIndex = -1;
                for (let j = 0; j < cols.length; j++) {
                    const colText = cols[j].textContent.trim();
                    if (colText.toLowerCase() === 'actions') {
                        isActionColIndex = j;
                    }
                }
                
                for (let j = 0; j < cols.length; j++) {
                    if (j === isActionColIndex || (j === cols.length - 1 && isActionColIndex === -1 && (cols[j].querySelector('.sg-dropdown-actions') || cols[j].textContent.includes('Edit') || cols[j].textContent.includes('Delete')))) {
                        continue; // Skip actions column
                    }
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s+)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }

            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const downloadLink = document.createElement('a');
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };

        // Transform table buttons into GitHub-style three-dot menus (vanilla JS, no Alpine)
        function transformActionGroups() {
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const td = row.querySelector('td:last-child');
                if (!td) return;
                if (td.querySelector('.sg-dropdown-actions')) return;

                const links = td.querySelectorAll('a');
                const forms = td.querySelectorAll('form');
                if (links.length === 0 && forms.length === 0) return;

                let menuItemsHtml = '';
                let hasActions = false;

                // Process Links
                links.forEach((link) => {
                    const text = link.textContent.trim().toLowerCase();
                    const href = link.getAttribute('href');
                    let label = link.textContent.trim();
                    let icon = 'eye';

                    if (text.includes('edit')) {
                        icon = 'pencil';
                        label = '✏ Edit';
                    } else if (text.includes('sync') || text.includes('calc')) {
                        icon = 'refresh-cw';
                        label = '🔄 Sync';
                    } else if (text.includes('scorecard') || text.includes('view') || text.includes('detail')) {
                        icon = 'eye';
                        label = '👁 View';
                    } else if (text.includes('maps')) {
                        icon = 'map-pin';
                        label = '🗺 Maps';
                    }

                    menuItemsHtml += `<a href="${href}" class="sg-dropdown-item" title="${label}"><i data-lucide="${icon}" style="width:13px;height:13px"></i><span>${label}</span></a>`;
                    hasActions = true;
                    link.style.display = 'none';
                });

                // Process Delete Forms
                forms.forEach((form) => {
                    const methodInput = form.querySelector('input[name="_method"]');
                    const isDelete = methodInput && methodInput.value.toUpperCase() === 'DELETE';
                    if (isDelete) {
                        const formId = 'df-' + Math.random().toString(36).substr(2, 9);
                        form.id = formId;
                        form.style.display = 'none';
                        menuItemsHtml += `<button type="button" onclick="if(confirm('Hapus data ini?'))document.getElementById('${formId}').submit();" class="sg-dropdown-item danger"><i data-lucide="trash-2" style="width:13px;height:13px"></i><span>🗑 Hapus</span></button>`;
                        hasActions = true;
                    }
                });

                if (hasActions) {
                    const dropdownId = 'dd-' + Math.random().toString(36).substr(2, 9);
                    const container = document.createElement('div');
                    container.className = 'sg-dropdown-actions';
                    container.style.cssText = 'display:flex;align-items:center;justify-content:center';
                    container.innerHTML = `
                        <button class="sg-dots-btn" data-dropdown="${dropdownId}" title="Actions" style="cursor:pointer">
                            <i data-lucide="more-horizontal" style="width:16px;height:16px"></i>
                        </button>
                        <div class="sg-dropdown-menu" id="${dropdownId}">
                            ${menuItemsHtml}
                        </div>
                    `;
                    td.appendChild(container);
                }
            });

            if (window.lucide) {
                lucide.createIcons();
            }
        }

        transformActionGroups();

        // Global dropdown toggle handler (vanilla JS — works without Alpine)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-dropdown]');
            if (btn) {
                e.stopPropagation();
                const menuId = btn.getAttribute('data-dropdown');
                const menu = document.getElementById(menuId);
                if (!menu) return;
                const isOpen = menu.classList.contains('show');
                // Close all other open menus
                document.querySelectorAll('.sg-dropdown-menu.show').forEach(m => m.classList.remove('show'));
                if (!isOpen) menu.classList.add('show');
            } else if (!e.target.closest('.sg-dropdown-actions')) {
                // Click outside — close all
                document.querySelectorAll('.sg-dropdown-menu.show').forEach(m => m.classList.remove('show'));
            }
        });
    });
    </script>
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{!! addslashes(session('success')) !!}",
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#1B2433',
                    color: '#F8FAFC'
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Gagal',
                    text: "{!! addslashes(session('error')) !!}",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    background: '#1B2433',
                    color: '#F8FAFC'
                });
            });
        </script>
    @endif
    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Peringatan',
                    text: "{!! addslashes(session('warning')) !!}",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    background: '#1B2433',
                    color: '#F8FAFC'
                });
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
