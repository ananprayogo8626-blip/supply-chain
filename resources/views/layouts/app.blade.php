<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Global Supply Chain Risk Intelligence Platform</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white h-16 flex items-center justify-between px-6 shadow">

        <div class="text-2xl font-bold">
            🌍 SupplyGuard
        </div>

        <div class="font-semibold">
            {{ Auth::user()->name }}
        </div>

    </nav>

    <div class="flex">

        <!-- Sidebar -->
        <aside class="w-64 min-h-screen bg-gray-900 text-white">

            <h2 class="text-3xl font-bold p-6">
                Menu
            </h2>

            <ul>

                <li>
                    <a href="{{ route('dashboard') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        📊 Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('countries.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        🌍 Countries
                    </a>
                </li>

                <li>
                    <a href="{{ route('weather.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        🌦 Weather
                    </a>
                </li>

                <li>
                    <a href="{{ route('economy.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        📈 Economy
                    </a>
                </li>

                <li>
                    <a href="{{ route('currency.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        💱 Currency
                    </a>
                </li>

                <li>
                    <a href="{{ route('ports.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        🚢 Ports
                    </a>
                </li>

                <li>
                    <a href="{{ route('risk-scores.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        ⚠️ Risk Score
                    </a>
                </li>

                <li>
                    <a href="{{ route('news.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        📰 News
                    </a>
                </li>

                <li>
                    <a href="{{ route('watchlists.index') }}"
                        class="block px-6 py-3 hover:bg-gray-700 transition">
                        ⭐ Watchlist
                    </a>
                </li>

            </ul>

        </aside>

        <!-- Content -->
        <main class="flex-1 p-8">

            @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot }}

        </main>

    </div>

</body>

</html>