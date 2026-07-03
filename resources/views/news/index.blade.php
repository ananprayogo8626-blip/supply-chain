<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data News') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">

                <div class="flex justify-between items-center mb-5">

                    <h1 class="text-2xl font-bold">
                        Data News
                    </h1>

                    <a href="{{ route('news.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        + Tambah Berita

                    </a>

                </div>

                <table class="min-w-full border">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border p-2">No</th>
                            <th class="border p-2">Country</th>
                            <th class="border p-2">Title</th>
                            <th class="border p-2">Source</th>
                            <th class="border p-2">Category</th>
                            <th class="border p-2">Impact</th>
                            <th class="border p-2">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($news as $item)

                        <tr>

                            <td class="border p-2">{{ $loop->iteration }}</td>

                            <td class="border p-2">
                                {{ $item->country->country_name }}
                            </td>

                            <td class="border p-2">
                                {{ $item->title }}
                            </td>

                            <td class="border p-2">
                                {{ $item->source }}
                            </td>

                            <td class="border p-2">
                                {{ $item->category }}
                            </td>

                            <td class="border p-2">
                                {{ $item->impact_score }}
                            </td>

                            <td class="border p-2">

                                <a href="{{ route('news.edit',$item->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form
                                    action="{{ route('news.destroy',$item->id) }}"
                                    method="POST"
                                    class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        onclick="return confirm('Hapus data?')"
                                        class="bg-red-600 text-white px-3 py-1 rounded">

                                        Hapus

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="7" class="text-center py-5">

                                Belum ada data berita.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>