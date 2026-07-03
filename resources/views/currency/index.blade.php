<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Data Currency
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
                        Data Currency
                    </h1>

                    <a href="{{ route('currency.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        + Tambah Currency

                    </a>

                </div>

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Country</th>
                            <th class="border px-4 py-2">Currency Code</th>
                            <th class="border px-4 py-2">Currency Name</th>
                            <th class="border px-4 py-2">Base Currency</th>
                            <th class="border px-4 py-2">Exchange Rate</th>
                            <th class="border px-4 py-2">Change %</th>
                            <th class="border px-4 py-2">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($currency as $item)

                        <tr>

                            <td class="border px-4 py-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->country->country_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->currency_code }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->currency_name }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->base_currency }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->exchange_rate }}
                            </td>

                            <td class="border px-4 py-2">
                                {{ $item->change_percentage }} %
                            </td>

                            <td class="border px-4 py-2">

                                <a href="{{ route('currency.edit',$item->id) }}"
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                    Edit

                                </a>

                                <form action="{{ route('currency.destroy',$item->id) }}"
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

                            <td colspan="8" class="text-center py-5">

                                Belum ada data currency.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</x-app-layout>