<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Risk Score
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

                <form action="{{ route('risk-scores.update',$risk_score->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-6">

                        <div>

                            <label class="block mb-2 font-semibold">
                                Negara
                            </label>

                            <select name="country_id"
                                    class="w-full border rounded-lg px-4 py-2">

                                @foreach($countries as $country)

                                    <option value="{{ $country->id }}"
                                        {{ $risk_score->country_id == $country->id ? 'selected' : '' }}>

                                        {{ $country->country_name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Weather Score
                            </label>

                            <input
                                type="number"
                                name="weather_score"
                                value="{{ old('weather_score',$risk_score->weather_score) }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Economy Score
                            </label>

                            <input
                                type="number"
                                name="economic_score"
                                value="{{ old('economic_score',$risk_score->economic_score) }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Currency Score
                            </label>

                            <input
                                type="number"
                                name="currency_score"
                                value="{{ old('currency_score',$risk_score->currency_score) }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                News Score
                            </label>

                            <input
                                type="number"
                                name="news_score"
                                value="{{ old('news_score',$risk_score->news_score) }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Port Score
                            </label>

                            <input
                                type="number"
                                name="port_score"
                                value="{{ old('port_score',$risk_score->port_score) }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                        <div class="col-span-2">

                            <label class="block mb-2 font-semibold">
                                Recommendation
                            </label>

                            <textarea
                                name="recommendation"
                                rows="4"
                                class="w-full border rounded-lg px-4 py-2">{{ old('recommendation',$risk_score->recommendation) }}</textarea>

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Calculated At
                            </label>

                            <input
                                type="datetime-local"
                                name="calculated_at"
                                value="{{ $risk_score->calculated_at ? $risk_score->calculated_at->format('Y-m-d\TH:i') : '' }}"
                                class="w-full border rounded-lg px-4 py-2">

                        </div>

                    </div>

                    <div class="mt-8">

                        <button
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg">

                            Update

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