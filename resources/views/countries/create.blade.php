<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Tambah Negara
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto mt-8">

        <div class="bg-white rounded-lg shadow-lg p-8">

            <form action="{{ route('countries.store') }}" method="POST">

                @csrf

                <div class="grid grid-cols-2 gap-6">

                    <div>
                        <label>Nama Negara</label>

                        <input
                            type="text"
                            name="country_name"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label>Kode Negara</label>

                        <input
                            type="text"
                            name="country_code"
                            class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label>Ibukota</label>

                        <input
                            type="text"
                            name="capital"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Region</label>

                        <input
                            type="text"
                            name="region"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Mata Uang</label>

                        <input
                            type="text"
                            name="currency"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Bahasa</label>

                        <input
                            type="text"
                            name="language"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>Populasi</label>

                        <input
                            type="number"
                            name="population"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>URL Bendera</label>

                        <input
                            type="text"
                            name="flag"
                            class="w-full border rounded p-2">
                    </div>

                </div>

                <div class="mt-8">

                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded">

                        Simpan

                    </button>

                    <a href="{{ route('countries.index') }}"
                       class="bg-gray-500 text-white px-6 py-3 rounded ml-2">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>