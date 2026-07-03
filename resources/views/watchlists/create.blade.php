<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Tambah Watchlist
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())

                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>• {{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="bg-white shadow rounded-lg p-6">

                <form action="{{ route('watchlists.store') }}" method="POST">

                    @csrf

                    <div class="grid grid-cols-2 gap-6">

                        <div>

                            <label class="block mb-2 font-semibold">
                                Negara
                            </label>

                            <select
                                name="country_id"
                                class="w-full border rounded-lg px-4 py-2"
                                required>

                                <option value="">
                                    -- Pilih Negara --
                                </option>

                                @foreach($countries as $country)

                                    <option value="{{ $country->id }}">

                                        {{ $country->country_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Status
                            </label>

                            <select
                                name="is_active"
                                class="w-full border rounded-lg px-4 py-2">

                                <option value="1">
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>

                        </div>

                        <div class="col-span-2">

                            <label class="block mb-2 font-semibold">
                                Catatan
                            </label>

                            <textarea
                                name="notes"
                                rows="5"
                                class="w-full border rounded-lg px-4 py-2"
                                placeholder="Masukkan catatan..."></textarea>

                        </div>

                    </div>

                    <div class="mt-8">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                            Simpan

                        </button>

                        <a href="{{ route('watchlists.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg ml-2">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>