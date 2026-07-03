<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Edit Data Economy
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8">

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

            <form action="{{ route('economy.update', $economy->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <label class="font-semibold">Country</label>

                        <select name="country_id" class="w-full border rounded p-2" required>

                            @foreach($countries as $country)

                                <option value="{{ $country->id }}"
                                    {{ $economy->country_id == $country->id ? 'selected' : '' }}>

                                    {{ $country->country_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label class="font-semibold">
                            GDP
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="gdp"
                            value="{{ old('gdp', $economy->gdp) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Inflation (%)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="inflation"
                            value="{{ old('inflation', $economy->inflation) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Exports
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="exports"
                            value="{{ old('exports', $economy->exports) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Imports
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="imports"
                            value="{{ old('imports', $economy->imports) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Population
                        </label>

                        <input
                            type="number"
                            name="population"
                            value="{{ old('population', $economy->population) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Data Year
                        </label>

                        <input
                            type="number"
                            name="data_year"
                            value="{{ old('data_year', $economy->data_year) }}"
                            class="w-full border rounded p-2"
                            required>

                    </div>

                </div>

                <div class="mt-8">

                    <button
                        type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded">

                        Update

                    </button>

                    <a href="{{ route('economy.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded ml-2">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>