<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Data Countries
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-8">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-3xl font-bold">
                    Daftar Negara
                </h2>

                <a href="{{ route('countries.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                    + Tambah Negara

                </a>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>

                            <th class="border px-4 py-2">Negara</th>

                            <th class="border px-4 py-2">Kode</th>

                            <th class="border px-4 py-2">Ibukota</th>

                            <th class="border px-4 py-2">Region</th>

                            <th class="border px-4 py-2">Mata Uang</th>

                            <th class="border px-4 py-2">Bahasa</th>

                            <th class="border px-4 py-2">Populasi</th>

                            <th class="border px-4 py-2">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($countries as $country)

                            <tr>

                                <td class="border px-4 py-2 text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->country_name }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->country_code }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->capital }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->region }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->currency }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ $country->language }}
                                </td>

                                <td class="border px-4 py-2">
                                    {{ number_format($country->population) }}
                                </td>

                                <td class="border px-4 py-2 text-center">

                                    <a href="{{ route('countries.edit',$country->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                        Edit

                                    </a>

                                    <form action="{{ route('countries.destroy',$country->id) }}"
                                        method="POST"
                                        class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Yakin ingin menghapus data?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">

                                            Hapus

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-6">

                                    Belum ada data negara.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>