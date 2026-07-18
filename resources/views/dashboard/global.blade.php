<x-app-layout>
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <h1 class="sg-crud-title">Global Country Dashboard</h1>
                <p class="sg-crud-description">Select a country to view comprehensive supply chain intelligence</p>
            </div>
        </div>
    </div>

    <!-- Country Selector -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div style="padding:20px;">
            <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                <div style="flex:1; min-width:300px;">
                    <select id="country-selector" class="form-select" style="width:100%; padding:12px 16px; border-radius:8px; border:1px solid var(--sg-border); background:var(--sg-bg); color:var(--sg-text-primary); font-size:14px;">
                        <option value="">Select a country...</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->country_name }} ({{ $country->country_code }})</option>
                        @endforeach
                    </select>
                </div>
                <button id="load-dashboard" class="sg-btn sg-btn-gradient" style="padding:12px 24px;">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                    Load Dashboard
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Skeleton -->
    <div id="loading-skeleton" style="display:none;">
        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:24px;">
            @for($i = 0; $i < 4; $i++)
                <div class="sg-data-card sg-skeleton-card">
                    <div style="padding:24px;">
                        <div class="sg-skeleton-text" style="height:16px; width:60%; margin-bottom:12px;"></div>
                        <div class="sg-skeleton-text" style="height:32px; width:80%;"></div>
                    </div>
                </div>
            @endfor
        </div>
        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px;">
            <div class="sg-data-card sg-skeleton-card">
                <div style="padding:24px;">
                    <div class="sg-skeleton-text" style="height:20px; width:40%; margin-bottom:16px;"></div>
                    @for($j = 0; $j < 5; $j++)
                        <div class="sg-skeleton-text" style="height:14px; width:100%; margin-bottom:12px;"></div>
                    @endfor
                </div>
            </div>
            <div class="sg-data-card sg-skeleton-card">
                <div style="padding:24px;">
                    <div class="sg-skeleton-text" style="height:20px; width:40%; margin-bottom:16px;"></div>
                    <div class="sg-skeleton-text" style="height:200px; width:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div id="dashboard-content" style="display:none;">
        
        <!-- Country Header -->
        <div class="sg-data-card" style="margin-bottom:24px;">
            <div style="padding:24px; display:flex; align-items:center; gap:20px;">
                <img id="country-flag" src="" alt="" style="width:100px; height:65px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border); display:none;">
                <div style="flex:1;">
                    <h2 id="country-name" style="font-size:28px; font-weight:700; color:var(--sg-text-primary); margin:0 0 8px 0;"></h2>
                    <p id="country-meta" style="color:var(--sg-text-secondary); font-size:14px; margin:0;"></p>
                </div>
                <div id="risk-badge-container"></div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin-bottom:24px;">
            
            <!-- GDP -->
            <div class="sg-data-card">
                <div style="padding:20px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <i data-lucide="dollar-sign" class="w-5 h-5 text-green-400"></i>
                        <span style="font-size:12px; color:var(--sg-text-secondary); font-weight:600; text-transform:uppercase;">GDP</span>
                    </div>
                    <div id="gdp-value" style="font-size:24px; font-weight:700; color:var(--sg-text-primary);"></div>
                </div>
            </div>

            <!-- Inflation -->
            <div class="sg-data-card">
                <div style="padding:20px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <i data-lucide="percent" class="w-5 h-5 text-orange-400"></i>
                        <span style="font-size:12px; color:var(--sg-text-secondary); font-weight:600; text-transform:uppercase;">Inflation</span>
                    </div>
                    <div id="inflation-value" style="font-size:24px; font-weight:700; color:var(--sg-text-primary);"></div>
                </div>
            </div>

            <!-- Population -->
            <div class="sg-data-card">
                <div style="padding:20px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                        <span style="font-size:12px; color:var(--sg-text-secondary); font-weight:600; text-transform:uppercase;">Population</span>
                    </div>
                    <div id="population-value" style="font-size:24px; font-weight:700; color:var(--sg-text-primary);"></div>
                </div>
            </div>

            <!-- Exchange Rate -->
            <div class="sg-data-card">
                <div style="padding:20px;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                        <i data-lucide="banknote" class="w-5 h-5 text-amber-400"></i>
                        <span style="font-size:12px; color:var(--sg-text-secondary); font-weight:600; text-transform:uppercase;">Exchange Rate</span>
                    </div>
                    <div id="exchange-rate-value" style="font-size:24px; font-weight:700; color:var(--sg-text-primary);"></div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px; margin-bottom:24px;">
            
            <!-- Left Column -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                
                <!-- Weather Card -->
                <div class="sg-data-card">
                    <div class="sg-data-head">
                        <div class="sg-data-head-left">
                            <i data-lucide="cloud-sun" class="w-5 h-5 text-blue-400"></i>
                            <h2 class="sg-data-title">Current Weather</h2>
                        </div>
                    </div>
                    <div id="weather-content" style="padding:20px;">
                        <p style="color:var(--sg-text-secondary); text-align:center;">No weather data</p>
                    </div>
                </div>

                <!-- Currency Info -->
                <div class="sg-data-card">
                    <div class="sg-data-head">
                        <div class="sg-data-head-left">
                            <i data-lucide="coins" class="w-5 h-5 text-yellow-400"></i>
                            <h2 class="sg-data-title">Currency</h2>
                        </div>
                    </div>
                    <div id="currency-content" style="padding:20px;">
                        <p style="color:var(--sg-text-secondary); text-align:center;">No currency data</p>
                    </div>
                </div>

                <!-- Risk Factor Breakdown -->
                <div class="sg-data-card">
                    <div class="sg-data-head">
                        <div class="sg-data-head-left">
                            <i data-lucide="bar-chart-2" class="w-5 h-5 text-orange-400"></i>
                            <h2 class="sg-data-title">Risk Factors</h2>
                        </div>
                    </div>
                    <div id="risk-factors-content" style="padding:20px;">
                        <p style="color:var(--sg-text-secondary); text-align:center;">No risk data</p>
                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                
                <!-- Interactive Map -->
                <div class="sg-data-card">
                    <div class="sg-data-head">
                        <div class="sg-data-head-left">
                            <i data-lucide="map" class="w-5 h-5 text-cyan-400"></i>
                            <h2 class="sg-data-title">Interactive Map</h2>
                        </div>
                    </div>
                    <div style="padding:20px;">
                        <div id="dashboard-map" style="height:300px; border-radius:12px; overflow:hidden; background:rgba(30, 41, 59, 0.5); display:flex; align-items:center; justify-content:center;">
                            <p style="color:var(--sg-text-secondary);">Select a country to view map</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- News Section -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="newspaper" class="w-5 h-5 text-purple-400"></i>
                    <h2 class="sg-data-title">Latest News</h2>
                </div>
            </div>
            <div id="news-content" style="padding:20px;">
                <p style="color:var(--sg-text-secondary); text-align:center;">No news data</p>
            </div>
        </div>

    </div>

    <!-- Empty State -->
    <div id="empty-state" class="sg-empty-state" style="padding:60px 24px;">
        <i data-lucide="globe" class="w-16 h-16 mx-auto mb-4" style="color:var(--sg-text-muted);"></i>
        <h3 style="font-size:20px; font-weight:600; color:var(--sg-text-primary); margin:0 0 8px 0;">Select a Country</h3>
        <p style="color:var(--sg-text-secondary); margin:0;">Choose a country from the dropdown above to view comprehensive supply chain intelligence.</p>
    </div>

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let map = null;
    let marker = null;

    // Load dashboard data
    document.getElementById('load-dashboard').addEventListener('click', function() {
        const countryId = document.getElementById('country-selector').value;
        
        if (!countryId) {
            alert('Please select a country first');
            return;
        }

        // Show loading skeleton
        document.getElementById('empty-state').style.display = 'none';
        document.getElementById('dashboard-content').style.display = 'none';
        document.getElementById('loading-skeleton').style.display = 'block';

        // Fetch data via AJAX
        fetch(`/countries/${countryId}/dashboard-data`)
            .then(response => response.json())
            .then(data => {
                // Hide loading skeleton
                document.getElementById('loading-skeleton').style.display = 'none';
                document.getElementById('dashboard-content').style.display = 'block';

                // Populate data
                populateDashboard(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading-skeleton').style.display = 'none';
                alert('Failed to load country data');
            });
    });

    function populateDashboard(data) {
        const country = data.country;
        const riskScore = data.risk_score;
        const weather = data.weather;
        const economy = data.economy;
        const currency = data.currency;
        const news = data.news;

        // Country header
        document.getElementById('country-flag').src = country.flag || `https://flagcdn.com/w80/${country.country_code.toLowerCase()}.png`;
        document.getElementById('country-flag').style.display = 'block';
        document.getElementById('country-name').textContent = country.country_name;
        document.getElementById('country-meta').textContent = `${country.capital} • ${country.region} • ${country.country_code}`;

        // Risk badge
        if (riskScore) {
            const level = riskScore.risk_level || 'Low';
            const colorClass = level.toLowerCase();
            document.getElementById('risk-badge-container').innerHTML = `
                <span class="sg-badge ${colorClass}" style="font-size:14px; padding:6px 16px;">
                    Risk Score: ${riskScore.total_score}/100 (${level})
                </span>
            `;
        } else {
            document.getElementById('risk-badge-container').innerHTML = '';
        }

        // Stats
        document.getElementById('gdp-value').textContent = economy ? `$${(economy.gdp / 1e9).toFixed(2)}B` : '—';
        document.getElementById('inflation-value').textContent = economy ? `${economy.inflation.toFixed(2)}%` : '—';
        document.getElementById('population-value').textContent = country.population ? Number(country.population).toLocaleString() : '—';
        document.getElementById('exchange-rate-value').textContent = currency ? currency.exchange_rate.toFixed(4) : '—';

        // Weather
        if (weather) {
            document.getElementById('weather-content').innerHTML = `
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px;">
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <i data-lucide="thermometer" class="w-5 h-5 text-orange-400 mx-auto mb-1"></i>
                        <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">${weather.temperature}°C</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary);">Temperature</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <i data-lucide="droplets" class="w-5 h-5 text-blue-400 mx-auto mb-1"></i>
                        <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">${weather.humidity}%</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary);">Humidity</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <i data-lucide="wind" class="w-5 h-5 text-cyan-400 mx-auto mb-1"></i>
                        <div style="font-size:20px; font-weight:700; color:var(--sg-text-primary);">${weather.wind_speed}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary);">Wind km/h</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <i data-lucide="cloud" class="w-5 h-5 text-slate-400 mx-auto mb-1"></i>
                        <div style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin-top:4px;">${weather.weather_condition || '—'}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary);">Condition</div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('weather-content').innerHTML = '<p style="color:var(--sg-text-secondary); text-align:center;">No weather data</p>';
        }

        // Currency
        if (currency) {
            document.getElementById('currency-content').innerHTML = `
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Currency Code</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">${currency.currency_code}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Buy Rate</span>
                        <span style="color:var(--sg-success); font-weight:600;">${currency.buy ? currency.buy.toFixed(4) : '—'}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Sell Rate</span>
                        <span style="color:var(--sg-danger); font-weight:600;">${currency.sell ? currency.sell.toFixed(4) : '—'}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Change</span>
                        <span style="color:${currency.change_percentage >= 0 ? 'var(--sg-success)' : 'var(--sg-danger)'}; font-weight:600;">
                            ${currency.change_percentage ? (currency.change_percentage >= 0 ? '+' : '') + currency.change_percentage.toFixed(2) + '%' : '—'}
                        </span>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('currency-content').innerHTML = '<p style="color:var(--sg-text-secondary); text-align:center;">No currency data</p>';
        }

        // Risk Factor Breakdown
        if (riskScore) {
            document.getElementById('risk-factors-content').innerHTML = `
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:12px;">
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <div style="font-size:20px; font-weight:700; color:${riskScore.weather_score >= 76 ? 'var(--sg-danger)' : (riskScore.weather_score >= 51 ? 'var(--accent-orange)' : (riskScore.weather_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)'))};">${riskScore.weather_score}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">🌤️ Weather (30%)</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <div style="font-size:20px; font-weight:700; color:${riskScore.inflation_score >= 76 ? 'var(--sg-danger)' : (riskScore.inflation_score >= 51 ? 'var(--accent-orange)' : (riskScore.inflation_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)'))};">${riskScore.inflation_score}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">📈 Inflation (25%)</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <div style="font-size:20px; font-weight:700; color:${riskScore.exchange_rate_score >= 76 ? 'var(--sg-danger)' : (riskScore.exchange_rate_score >= 51 ? 'var(--accent-orange)' : (riskScore.exchange_rate_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)'))};">${riskScore.exchange_rate_score}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">💱 Exchange Rate (20%)</div>
                    </div>
                    <div style="background:rgba(30, 41, 59, 0.5); padding:12px; border-radius:8px; text-align:center;">
                        <div style="font-size:20px; font-weight:700; color:${riskScore.news_sentiment_score >= 76 ? 'var(--sg-danger)' : (riskScore.news_sentiment_score >= 51 ? 'var(--accent-orange)' : (riskScore.news_sentiment_score >= 26 ? 'var(--sg-warning)' : 'var(--sg-success)'))};">${riskScore.news_sentiment_score}</div>
                        <div style="font-size:11px; color:var(--sg-text-secondary); margin-top:4px;">📰 News Sentiment (25%)</div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('risk-factors-content').innerHTML = '<p style="color:var(--sg-text-secondary); text-align:center;">No risk data</p>';
        }

        // News
        if (news && news.length > 0) {
            document.getElementById('news-content').innerHTML = `
                <div style="display:flex; flex-direction:column; gap:12px;">
                    ${news.map(item => `
                        <div style="background:rgba(30, 41, 59, 0.5); padding:16px; border-radius:8px; display:flex; gap:12px;">
                            ${item.image ? `<img src="${item.image}" style="width:80px; height:60px; object-fit:cover; border-radius:6px;" loading="lazy">` : ''}
                            <div style="flex:1;">
                                <h4 style="font-size:14px; font-weight:600; color:var(--sg-text-primary); margin:0 0 6px 0; line-height:1.4;">${item.title}</h4>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <span class="sg-badge ${item.sentiment === 'Positive' ? 'low' : (item.sentiment === 'Negative' ? 'critical' : 'medium')}" style="font-size:10px;">${item.sentiment || 'Neutral'}</span>
                                    <span style="font-size:11px; color:var(--sg-text-muted);">${item.source || '—'}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            document.getElementById('news-content').innerHTML = '<p style="color:var(--sg-text-secondary); text-align:center;">No news data</p>';
        }

        // Initialize map
        if (country.latitude && country.longitude) {
            initMap(country.latitude, country.longitude, country.country_name);
        }

        // Reinitialize Lucide icons
        lucide.createIcons();
    }

    function initMap(lat, lng, countryName) {
        // Remove existing map
        if (map) {
            map.remove();
        }

        const mapContainer = document.getElementById('dashboard-map');
        mapContainer.innerHTML = '';

        map = L.map('dashboard-map').setView([lat, lng], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup(countryName)
            .openPopup();
    }
});
</script>
