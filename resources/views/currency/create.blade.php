<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Tambah Data Currency
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

                <form action="{{ route('currency.store') }}" method="POST">

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
                            <label class="font-semibold">Currency Code</label>

                            <input type="text"
                                   name="currency_code"
                                   class="w-full border rounded p-2"
                                   required>
                        </div>

                        <div>
                            <label class="font-semibold">Currency Name</label>

                            <input type="text"
                                   name="currency_name"
                                   class="w-full border rounded p-2"
                                   required>
                        </div>

                        <div>
                            <label class="font-semibold">Base Currency</label>

                            <input type="text"
                                   name="base_currency"
                                   value="USD"
                                   class="w-full border rounded p-2"
                                   required>
                        </div>

                        <div>
                            <label class="font-semibold">Exchange Rate</label>

                            <input type="number"
                                   step="0.000001"
                                   name="exchange_rate"
                                   class="w-full border rounded p-2"
                                   required>
                        </div>

                        <div>
                            <label class="font-semibold">Change Percentage</label>

                            <input type="number"
                                   step="0.01"
                                   name="change_percentage"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="font-semibold">Last Updated</label>

                            <input type="datetime-local"
                                   name="last_updated"
                                   class="w-full border rounded p-2">
                        </div>

                    </div>

                    <div class="mt-8">

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                            Simpan

                        </button>

                        <a href="{{ route('currency.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded ml-2">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>