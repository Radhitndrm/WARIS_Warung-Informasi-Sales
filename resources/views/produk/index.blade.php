@extends('layouts.app')

@section('title', 'Produk')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-primary">Produk</h1>
            <p class="text-sm text-gray-500">Kelola daftar produk</p>
        </div>

        <a href="{{ route('produk.create') }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            + Tambah Produk
        </a>
    </div>

    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] p-6">

        <h2 class="font-semibold text-lg mb-4">Daftar Produk</h2>

        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3">Nama Produk</th>
                    <th class="text-left py-3">Kategori</th>
                    <th class="text-left py-3">Harga</th>
                    <th class="text-left py-3">Stok</th>
                    <th class="text-left py-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b">
                        <td class="py-3">{{ $product->name }}</td>

                        <td class="py-3">
                            {{ $product->category->name ?? '-' }}
                        </td>

                        <td class="py-3">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        <td class="py-3">
                            {{ $product->stock }}
                        </td>

                        <td class="py-3 flex gap-2">

                            <a href="{{ route('produk.edit', $product->id) }}"
                                class="px-3 py-1 bg-yellow-500 text-white rounded">
                                Edit
                            </a>

                            <form action="{{ route('produk.destroy', $product->id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus produk ini?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="px-3 py-1 bg-red-600 text-white rounded">
                                    Hapus
                                </button>

                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            Belum ada data produk
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

@endsection