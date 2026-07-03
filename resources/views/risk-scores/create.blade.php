<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Risk Score
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())

                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>• {{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="bg-white shadow rounded-lg p-6">

                <form action="{{ route('risk-scores.store') }}" method="POST">

                    @csrf

                    <div class="grid grid-cols-2 gap-6">

                        <div>

                            <label class="block mb-2 font-semibold">
                                Negara
                            </label>

                            <select name="country_id"
                                    class="w-full border rounded-lg px-4 py-2"
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

                            <label class="block mb-2 font-semibold">
                                Weather Score
                            </label>

                            <input type="number"
                                   name="weather_score"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Economy Score
                            </label>

                            <input type="number"
                                   name="economic_score"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Currency Score
                            </label>

                            <input type="number"
                                   name="currency_score"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                News Score
                            </label>

                            <input type="number"
                                   name="news_score"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Port Score
                            </label>

                            <input type="number"
                                   name="port_score"
                                   class="w-full border rounded-lg px-4 py-2"
                                   required>

                        </div>

                        <div class="col-span-2">

                            <label class="block mb-2 font-semibold">
                                Recommendation
                            </label>

                            <textarea
                                name="recommendation"
                                rows="4"
                                class="w-full border rounded-lg px-4 py-2"></textarea>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Calculated At
                            </label>

                            <input type="datetime-local"
                                   name="calculated_at"
                                   class="w-full border rounded-lg px-4 py-2">

                        </div>

                    </div>

                    <div class="mt-8">

                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                            Simpan

                        </button>

                        <a href="{{ route('risk-scores.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg ml-2">

                            Kembali

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>