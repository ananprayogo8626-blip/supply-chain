<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Data Weather
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
                        Data Cuaca
                    </h1>

                    <a href="{{ route('weather.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        + Tambah Data

                    </a>

                </div>

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Negara</th>
                            <th class="border px-4 py-2">Suhu</th>
                            <th class="border px-4 py-2">Angin</th>
                            <th class="border px-4 py-2">Curah Hujan</th>
                            <th class="border px-4 py-2">Kelembapan</th>
                            <th class="border px-4 py-2">Kondisi</th>
                            <th class="border px-4 py-2">Storm Risk</th>
                            <th class="border px-4 py-2">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($weather as $item)

                        <tr>

                            <td class="border px-4 py-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->country->country_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->temperature }} °C
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->wind_speed }} km/h
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->rainfall }} mm
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->humidity }} %
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->weather_condition }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->storm_risk }}
                            </td>

                            <td class="border px-4 py-2">

                                <a href="{{ route('weather.edit',$item->id) }}"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('weather.destroy',$item->id) }}"
                                      method="POST"
                                      class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        onclick="return confirm('Yakin ingin menghapus data?')"
                                        class="bg-red-600 text-white px-3 py-1 rounded">

                                        Hapus

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="text-center py-5">

                                Belum ada data cuaca.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</x-app-layout>