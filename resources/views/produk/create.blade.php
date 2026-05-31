@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-primary">Tambah Produk</h1>
        <p class="text-sm text-gray-500">Tambah produk baru</p>
    </div>

    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] p-6">

        <form action="{{ route('produk.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block mb-2 font-medium">Nama Produk</label>
                <input
                    type="text"
                    name="name"
                    class="w-full border rounded-lg px-4 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">Kategori</label>

                <select
                    name="category_id"
                    class="w-full border rounded-lg px-4 py-2"
                    required>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">Harga</label>
                <input
                    type="number"
                    name="price"
                    class="w-full border rounded-lg px-4 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium">Stok</label>
                <input
                    type="number"
                    name="stock"
                    class="w-full border rounded-lg px-4 py-2"
                    required>
            </div>

            <button
                type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Simpan Produk
            </button>

        </form>

    </div>

</div>

@endsection