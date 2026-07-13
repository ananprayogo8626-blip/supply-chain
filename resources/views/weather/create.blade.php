<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Tambah Data Cuaca
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">

                <form action="{{ route('weather.store') }}" method="POST">

                    @csrf

                    <div class="grid grid-cols-2 gap-6">

                        <div>
                            <label class="font-semibold">Negara</label>

                            <select name="country_id"
                                    class="w-full border rounded p-2"
                                    required>

                                <option value="">-- Pilih Negara --</option>

                                @foreach($countries as $country)

                                    <option value="{{ $country->id }}">
                                        {{ $country->country_name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Suhu (°C)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="temperature"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Kecepatan Angin (km/h)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="wind_speed"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Curah Hujan (mm)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="rainfall"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Kelembapan (%)
                            </label>

                            <input
                                type="number"
                                name="humidity"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Cloud Cover (%)
                            </label>

                            <input
                                type="number"
                                name="cloud"
                                min="0"
                                max="100"
                                class="w-full border rounded p-2">

                        </div>

                        <div>

                            <label class="font-semibold">
                                Pressure (hPa)
                            </label>

                            <input
                                type="number"
                                step="0.1"
                                name="pressure"
                                class="w-full border rounded p-2">

                        </div>

                        <div>

                            <label class="font-semibold">
                                Kondisi Cuaca
                            </label>

                            <input
                                type="text"
                                name="weather_condition"
                                class="w-full border rounded p-2"
                                placeholder="Sunny / Rainy / Cloudy"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Storm Risk (0 - 100)
                            </label>

                            <input
                                type="number"
                                name="storm_risk"
                                min="0"
                                max="100"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                    </div>

                    <div class="mt-8">

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                            Simpan

                        </button>

                        <a href="{{ route('weather.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded ml-2">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>