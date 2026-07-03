<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl">
            Tambah Data Economy
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8">

        <div class="bg-white shadow rounded-lg p-6">

            <form action="{{ route('economy.store') }}" method="POST">

                @csrf

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <label>Country</label>

                        <select name="country_id" class="w-full border rounded p-2">

                            @foreach($countries as $country)

                            <option value="{{ $country->id }}">

                                {{ $country->country_name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div>
                        <label>GDP</label>
                        <input type="number" step="0.01" name="gdp" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Inflation</label>
                        <input type="number" step="0.01" name="inflation" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Exports</label>
                        <input type="number" step="0.01" name="exports" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Imports</label>
                        <input type="number" step="0.01" name="imports" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Population</label>
                        <input type="number" name="population" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Data Year</label>
                        <input type="number" name="data_year" class="w-full border rounded p-2">
                    </div>

                </div>

                <div class="mt-8">

                    <button class="bg-blue-600 text-white px-6 py-3 rounded">

                        Simpan

                    </button>

                    <a href="{{ route('economy.index') }}"
                       class="bg-gray-600 text-white px-6 py-3 rounded">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>