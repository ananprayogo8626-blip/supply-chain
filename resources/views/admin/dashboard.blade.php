<x-app-layout>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
        <p class="text-slate-400">Overview sistem SupplyGuard - Global Supply Chain Risk Intelligence Platform</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-400"></i>
                </div>
                <span class="text-xs text-slate-400">Total Users</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalUsers }}</h3>
            <div class="flex gap-2 text-xs">
                <span class="text-green-400">Super Admin: {{ $superAdminCount }}</span>
                <span class="text-yellow-400">Admin: {{ $adminCount }}</span>
                <span class="text-orange-400">Analyst: {{ $analystCount }}</span>
                <span class="text-slate-400">Viewer: {{ $viewerCount }}</span>
            </div>
        </div>

        <!-- Countries -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="globe" class="w-6 h-6 text-green-400"></i>
                </div>
                <span class="text-xs text-slate-400">Countries</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalCountries }}</h3>
            <p class="text-xs text-slate-400">Negara yang dimonitor</p>
        </div>

        <!-- Weather -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="cloud" class="w-6 h-6 text-cyan-400"></i>
                </div>
                <span class="text-xs text-slate-400">Weather Data</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalWeather }}</h3>
            <p class="text-xs text-slate-400">Data cuaca tercatat</p>
        </div>

        <!-- Economy -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-purple-400"></i>
                </div>
                <span class="text-xs text-slate-400">Economy Data</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalEconomy }}</h3>
            <p class="text-xs text-slate-400">Data ekonomi tercatat</p>
        </div>

        <!-- Currency -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-amber-400"></i>
                </div>
                <span class="text-xs text-slate-400">Currency Data</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalCurrency }}</h3>
            <p class="text-xs text-slate-400">Data mata uang tercatat</p>
        </div>

        <!-- Ports -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="anchor" class="w-6 h-6 text-indigo-400"></i>
                </div>
                <span class="text-xs text-slate-400">Ports</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalPorts }}</h3>
            <p class="text-xs text-slate-400">Pelabuhan tercatat</p>
        </div>

        <!-- News -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="newspaper" class="w-6 h-6 text-orange-400"></i>
                </div>
                <span class="text-xs text-slate-400">News Articles</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalNews }}</h3>
            <p class="text-xs text-slate-400">Artikel berita tercatat</p>
        </div>

        <!-- Risk Scores -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield-alert" class="w-6 h-6 text-red-400"></i>
                </div>
                <span class="text-xs text-slate-400">Risk Scores</span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-2">{{ $totalRiskScores }}</h3>
            <div class="flex gap-2 text-xs">
                <span class="text-red-400">High: {{ $highRiskCount }}</span>
                <span class="text-yellow-400">Medium: {{ $mediumRiskCount }}</span>
                <span class="text-green-400">Low: {{ $lowRiskCount }}</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Growth Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">User Growth</h3>
            <canvas id="userGrowthChart" height="200"></canvas>
        </div>

        <!-- API Activity Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">API Activity</h3>
            <canvas id="apiActivityChart" height="200"></canvas>
        </div>

        <!-- Weather Trend Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Weather Trend (Temperature)</h3>
            <canvas id="weatherTrendChart" height="200"></canvas>
        </div>

        <!-- Currency Trend Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Currency Trend</h3>
            <canvas id="currencyTrendChart" height="200"></canvas>
        </div>

        <!-- Economy Trend Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Economy Trend (GDP)</h3>
            <canvas id="economyTrendChart" height="200"></canvas>
        </div>

        <!-- News Trend Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">News Trend</h3>
            <canvas id="newsTrendChart" height="200"></canvas>
        </div>

        <!-- Risk Trend Chart -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Risk Trend</h3>
            <canvas id="riskTrendChart" height="200"></canvas>
        </div>

        <!-- System Status -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">System Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Status</span>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-bold">{{ $systemStatus['status'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Uptime</span>
                    <span class="text-white font-semibold">{{ $systemStatus['uptime'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Memory Usage</span>
                    <span class="text-white font-semibold">{{ $systemStatus['memory'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">PHP Version</span>
                    <span class="text-white font-semibold">{{ $systemStatus['php_version'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Laravel Version</span>
                    <span class="text-white font-semibold">{{ $systemStatus['laravel_version'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Sync Logs -->
        <div class="lg:col-span-2 bg-white/5 border border-white/10 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Recent API Activity</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="pb-3 text-slate-400 font-medium">Stage</th>
                            <th class="pb-3 text-slate-400 font-medium">Country</th>
                            <th class="pb-3 text-slate-400 font-medium">Error</th>
                            <th class="pb-3 text-slate-400 font-medium">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSyncLogs as $log)
                            <tr class="border-b border-white/5">
                                <td class="py-3 text-white">{{ $log->stage }}</td>
                                <td class="py-3 text-slate-300">{{ $log->country->country_name ?? '—' }}</td>
                                <td class="py-3 text-red-400 text-xs truncate max-w-[200px]">{{ $log->error_message ?? '—' }}</td>
                                <td class="py-3 text-slate-400 text-xs">{{ $log->failed_at?->diffForHumans() ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">No recent activity</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Database & Storage Status -->
        <div class="space-y-6">
            <!-- Database Status -->
            <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Database Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Status</span>
                        <span class="px-3 py-1 {{ $databaseStatus['status'] === 'connected' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }} rounded-full text-xs font-bold">{{ $databaseStatus['status'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Database</span>
                        <span class="text-white font-semibold">{{ $databaseStatus['database'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Driver</span>
                        <span class="text-white font-semibold">{{ $databaseStatus['driver'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Storage Status -->
            <div class="bg-white/5 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Storage Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total</span>
                        <span class="text-white font-semibold">{{ $storageStatus['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Used</span>
                        <span class="text-white font-semibold">{{ $storageStatus['used'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Free</span>
                        <span class="text-white font-semibold">{{ $storageStatus['free'] }}</span>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-white/10 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $storageStatus['usage_percent'] }}%"></div>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 text-right">{{ $storageStatus['usage_percent'] }}% used</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Chart.js configuration
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#94a3b8'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                }
            }
        };

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($userGrowth->pluck('month')),
                datasets: [{
                    label: 'New Users',
                    data: @json($userGrowth->pluck('count')),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // API Activity Chart
        const apiActivityCtx = document.getElementById('apiActivityChart').getContext('2d');
        new Chart(apiActivityCtx, {
            type: 'bar',
            data: {
                labels: @json($apiActivity->pluck('date')),
                datasets: [{
                    label: 'API Calls',
                    data: @json($apiActivity->pluck('count')),
                    backgroundColor: '#10b981'
                }]
            },
            options: chartOptions
        });

        // Weather Trend Chart
        const weatherTrendCtx = document.getElementById('weatherTrendChart').getContext('2d');
        new Chart(weatherTrendCtx, {
            type: 'line',
            data: {
                labels: @json($weatherTrend->pluck('date')),
                datasets: [{
                    label: 'Temperature (°C)',
                    data: @json($weatherTrend->pluck('temperature')),
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // Currency Trend Chart
        const currencyTrendCtx = document.getElementById('currencyTrendChart').getContext('2d');
        new Chart(currencyTrendCtx, {
            type: 'line',
            data: {
                labels: @json($currencyTrend->pluck('date')),
                datasets: [{
                    label: 'Exchange Rate',
                    data: @json($currencyTrend->pluck('rate')),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // Economy Trend Chart
        const economyTrendCtx = document.getElementById('economyTrendChart').getContext('2d');
        new Chart(economyTrendCtx, {
            type: 'line',
            data: {
                labels: @json($economyTrend->pluck('month')),
                datasets: [{
                    label: 'GDP (Billion USD)',
                    data: @json($economyTrend->pluck('gdp')),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // News Trend Chart
        const newsTrendCtx = document.getElementById('newsTrendChart').getContext('2d');
        new Chart(newsTrendCtx, {
            type: 'bar',
            data: {
                labels: @json($newsTrend->pluck('date')),
                datasets: [{
                    label: 'News Articles',
                    data: @json($newsTrend->pluck('count')),
                    backgroundColor: '#f97316'
                }]
            },
            options: chartOptions
        });

        // Risk Trend Chart
        const riskTrendCtx = document.getElementById('riskTrendChart').getContext('2d');
        new Chart(riskTrendCtx, {
            type: 'line',
            data: {
                labels: @json($riskTrend->pluck('date')),
                datasets: [{
                    label: 'Risk Score',
                    data: @json($riskTrend->pluck('risk')),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });
    </script>
</x-app-layout>
