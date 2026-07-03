<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Tambah Data Pelabuhan
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

                <form action="{{ route('ports.store') }}" method="POST">

                    @csrf

                    <div class="grid grid-cols-2 gap-6">

                        <div>
                            <label class="font-semibold">Negara</label>

                            <select name="country_id" class="w-full border rounded p-2" required>

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
                                Nama Pelabuhan
                            </label>

                            <input
                                type="text"
                                name="port_name"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Kode Pelabuhan
                            </label>

                            <input
                                type="text"
                                name="port_code"
                                class="w-full border rounded p-2">

                        </div>

                        <div>

                            <label class="font-semibold">
                                Kota
                            </label>

                            <input
                                type="text"
                                name="city"
                                class="w-full border rounded p-2">

                        </div>

                        <div>

                            <label class="font-semibold">
                                Latitude
                            </label>

                            <input
                                type="number"
                                step="0.0000001"
                                name="latitude"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Longitude
                            </label>

                            <input
                                type="number"
                                step="0.0000001"
                                name="longitude"
                                class="w-full border rounded p-2"
                                required>

                        </div>

                        <div>

                            <label class="font-semibold">
                                Jenis Pelabuhan
                            </label>

                            <input
                                type="text"
                                name="port_type"
                                placeholder="Seaport / River Port"
                                class="w-full border rounded p-2">

                        </div>

                        <div>

                            <label class="font-semibold">
                                Status
                            </label>

                            <select name="status"
                                    class="w-full border rounded p-2">

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-6">

                        <label class="font-semibold">
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="w-full border rounded p-2"></textarea>

                    </div>

                    <div class="mt-8">

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                            Simpan

                        </button>

                        <a href="{{ route('ports.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded ml-2">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>