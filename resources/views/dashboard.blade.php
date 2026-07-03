<x-app-layout>

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            🌍 Global Supply Chain Risk Intelligence Platform
        </h1>

    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">

        <h2 class="text-3xl font-bold mb-3">
            Selamat Datang, {{ Auth::user()->name }}
        </h2>

        <p class="text-gray-600">

            Dashboard ini digunakan untuk memonitor kondisi Supply Chain berdasarkan
            data Cuaca, Ekonomi, Kurs Mata Uang, Pelabuhan dan Risk Score.

        </p>

    </div>

    <div class="grid grid-cols-5 gap-5 mb-8">

        <div class="bg-blue-500 text-white rounded-lg p-5">

            <h2 class="font-bold text-xl">

                Countries

            </h2>

            <h1 class="text-5xl font-bold mt-4">

                {{ $totalCountries }}

            </h1>

        </div>

        <div class="bg-green-500 text-white rounded-lg p-5">

            <h2 class="font-bold text-xl">

                Ports

            </h2>

            <h1 class="text-5xl font-bold mt-4">

                {{ $totalPorts }}

            </h1>

        </div>

        <div class="bg-yellow-500 text-white rounded-lg p-5">

            <h2 class="font-bold text-xl">

                Articles

            </h2>

            <h1 class="text-5xl font-bold mt-4">

                {{ $totalArticles }}

            </h1>

        </div>

        <div class="bg-purple-500 text-white rounded-lg p-5">

            <h2 class="font-bold text-xl">

                Watchlists

            </h2>

            <h1 class="text-5xl font-bold mt-4">

                {{ $totalWatchlists }}

            </h1>

        </div>

        <div class="bg-red-500 text-white rounded-lg p-5">

            <h2 class="font-bold text-xl">

                High Risk

            </h2>

            <h1 class="text-5xl font-bold mt-4">

                {{ $highRiskCountries }}

            </h1>

        </div>

    </div>

    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-2xl font-bold mb-4">

            Informasi Dashboard

        </h2>

        <ul class="list-disc pl-6 space-y-2">

            <li>Monitoring kondisi cuaca berbagai negara.</li>

            <li>Monitoring nilai tukar mata uang.</li>

            <li>Monitoring data ekonomi (GDP, Inflasi).</li>

            <li>Monitoring pelabuhan internasional.</li>

            <li>Analisis Risk Score Supply Chain.</li>

        </ul>

    </div>

</x-app-layout>