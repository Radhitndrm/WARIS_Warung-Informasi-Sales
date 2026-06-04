@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="max-w-2xl mx-auto">
    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('produk') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Tambah Produk</h1>
                <p class="text-sm text-gray-500 mt-0.5">Tambah produk baru</p>
            </div>
        </div>

        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Produk</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 placeholder:text-gray-400 transition-colors"
                    placeholder="Masukkan nama produk">
                @error('name')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                <select id="category_id" name="category_id" required
                    class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 transition-colors">
                    <option value="">Pilih kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-1.5">Harga Beli</label>
                    <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required
                        class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 placeholder:text-gray-400 transition-colors"
                        placeholder="Harga beli (modal)">
                    @error('purchase_price')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">Harga Jual</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" required
                        class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 placeholder:text-gray-400 transition-colors"
                        placeholder="Masukkan harga jual">
                    @error('price')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="stock" class="block text-sm font-semibold text-gray-700 mb-1.5">Stok</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}" required
                    class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 placeholder:text-gray-400 transition-colors"
                    placeholder="Masukkan jumlah stok">
                @error('stock')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-semibold text-gray-700 mb-1.5">Gambar Produk</label>
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sidebar file:text-white file:text-sm file:font-semibold hover:file:bg-sidebar-hover transition-colors">
                <p class="mt-1.5 text-xs text-gray-400">Format: JPEG, PNG, WebP. Maks: 2MB</p>
                @error('image')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('produk') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-[#8C8A75]/60 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-sidebar rounded-xl hover:bg-sidebar-hover transition-colors">
                    <i class="fa-solid fa-check mr-1.5"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
