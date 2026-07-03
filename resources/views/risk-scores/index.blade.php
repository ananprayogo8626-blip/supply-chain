<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Risk Score') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-md rounded-lg p-6">

                <div class="flex justify-between items-center mb-5">

                    <h1 class="text-2xl font-bold">
                        Data Risk Score
                    </h1>

                    <a href="{{ route('risk-scores.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        + Tambah Data

                    </a>

                </div>

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Country</th>
                            <th class="border px-4 py-2">Weather</th>
                            <th class="border px-4 py-2">Economy</th>
                            <th class="border px-4 py-2">Currency</th>
                            <th class="border px-4 py-2">News</th>
                            <th class="border px-4 py-2">Port</th>
                            <th class="border px-4 py-2">Total</th>
                            <th class="border px-4 py-2">Risk Level</th>
                            <th class="border px-4 py-2">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($scores as $score)

                        <tr>

                            <td class="border px-4 py-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->country->country_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->weather_score }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->economic_score }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->currency_score }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->news_score }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $score->port_score }}
                            </td>

                            <td class="border px-4 py-2 font-bold">
                                {{ $score->total_score }}
                            </td>

                            <td class="border px-4 py-2">

                                @if($score->risk_level=='Low')

                                    <span class="bg-green-500 text-white px-3 py-1 rounded">
                                        Low
                                    </span>

                                @elseif($score->risk_level=='Medium')

                                    <span class="bg-yellow-500 text-white px-3 py-1 rounded">
                                        Medium
                                    </span>

                                @else

                                    <span class="bg-red-600 text-white px-3 py-1 rounded">
                                        High
                                    </span>

                                @endif

                            </td>

                            <td class="border px-4 py-2">

                                <a href="{{ route('risk-scores.edit',$score->id) }}"
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('risk-scores.destroy',$score->id) }}"
                                      method="POST"
                                      class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        onclick="return confirm('Yakin ingin menghapus data ini?')"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">

                                        Hapus

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="10" class="text-center py-5">

                                Belum ada data Risk Score.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</x-app-layout>