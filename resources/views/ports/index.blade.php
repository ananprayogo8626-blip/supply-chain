<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Ports') }}
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
                        Data Pelabuhan
                    </h1>

                    <a href="{{ route('ports.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        + Tambah Pelabuhan

                    </a>

                </div>

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Negara</th>
                            <th class="border px-4 py-2">Nama Pelabuhan</th>
                            <th class="border px-4 py-2">Kode</th>
                            <th class="border px-4 py-2">Kota</th>
                            <th class="border px-4 py-2">Latitude</th>
                            <th class="border px-4 py-2">Longitude</th>
                            <th class="border px-4 py-2">Jenis</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($ports as $port)

                        <tr>

                            <td class="border px-4 py-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->country->country_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->port_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->port_code }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->city }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->latitude }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->longitude }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $port->port_type }}
                            </td>

                            <td class="border px-4 py-2">

                                @if($port->status == 'Active')

                                    <span class="bg-green-500 text-white px-2 py-1 rounded">
                                        Active
                                    </span>

                                @else

                                    <span class="bg-red-500 text-white px-2 py-1 rounded">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="border px-4 py-2">

                                <a href="{{ route('ports.edit',$port->id) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('ports.destroy',$port->id) }}"
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

                                Belum ada data pelabuhan.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</x-app-layout>s