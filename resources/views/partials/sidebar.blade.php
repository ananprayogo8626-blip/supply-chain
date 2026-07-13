<aside class="w-64 bg-gray-900 min-h-screen text-white">
    <div class="p-6 text-2xl font-bold">
        Menu
    </div>

    <ul>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('dashboard') }}">📊 Dashboard</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('countries.index') }}">🌍 Countries</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('weather.index') }}">🌦 Weather</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('economy.index') }}">📈 Economy</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('currency.index') }}">💱 Currency</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('ports.index') }}">🚢 Ports</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('risk-scores.index') }}">⚠️ Risk Score</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('news.index') }}">📰 News</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('watchlists.index') }}">⭐ Watchlist</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('map') }}">🗺️ Map Visualizer</a>
        </li>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="{{ route('comparison') }}">📊 Country Comparison</a>
        </li>
    </ul>
</aside>