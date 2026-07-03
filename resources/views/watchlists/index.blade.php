<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Watchlist
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">

                <div class="flex justify-between mb-6">

                    <h1 class="text-2xl font-bold">
                        Data Watchlist
                    </h1>

                    <a href="{{ route('watchlists.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">

                        + Tambah Watchlist

                    </a>

                </div>

                <table class="min-w-full border">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Negara</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Catatan</th>
                            <th class="border px-4 py-2">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($watchlists as $watchlist)

                        <tr>

                            <td class="border px-4 py-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $watchlist->country->country_name }}
                            </td>

                            <td class="border px-4 py-2">

                                @if($watchlist->is_active)

                                    <span class="bg-green-500 text-white px-3 py-1 rounded">
                                        Active
                                    </span>

                                @else

                                    <span class="bg-red-500 text-white px-3 py-1 rounded">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="border px-4 py-2">
                                {{ $watchlist->notes }}
                            </td>

                            <td class="border px-4 py-2">

                                <a href="{{ route('watchlists.edit',$watchlist->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('watchlists.destroy',$watchlist->id) }}"
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

                            <td colspan="5" class="text-center py-5">

                                Belum ada data Watchlist.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>