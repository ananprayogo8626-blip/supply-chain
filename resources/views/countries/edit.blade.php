<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Edit Negara
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto mt-8">

        <div class="bg-white rounded-lg shadow-lg p-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('countries.update', $country->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <label class="block mb-2 font-semibold">
                            Nama Negara
                        </label>

                        <input
                            type="text"
                            name="country_name"
                            value="{{ old('country_name', $country->country_name) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Kode Negara
                        </label>

                        <input
                            type="text"
                            name="country_code"
                            value="{{ old('country_code', $country->country_code) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Ibukota
                        </label>

                        <input
                            type="text"
                            name="capital"
                            value="{{ old('capital', $country->capital) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Region
                        </label>

                        <input
                            type="text"
                            name="region"
                            value="{{ old('region', $country->region) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Mata Uang
                        </label>

                        <input
                            type="text"
                            name="currency"
                            value="{{ old('currency', $country->currency) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Bahasa
                        </label>

                        <input
                            type="text"
                            name="language"
                            value="{{ old('language', $country->language) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Populasi
                        </label>

                        <input
                            type="number"
                            name="population"
                            value="{{ old('population', $country->population) }}"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            URL Bendera
                        </label>

                        <input
                            type="text"
                            name="flag"
                            value="{{ old('flag', $country->flag) }}"
                            class="w-full border rounded p-2">
                    </div>

                </div>

                <div class="mt-8">

                    <button
                        type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded">

                        Update Data

                    </button>

                    <a href="{{ route('countries.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded ml-2">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>