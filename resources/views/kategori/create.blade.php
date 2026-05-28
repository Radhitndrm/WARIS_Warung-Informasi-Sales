@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="max-w-2xl mx-auto">
    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm p-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('kategori') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Tambah Kategori</h1>
                <p class="text-sm text-gray-500 mt-0.5">Buat kategori baru untuk produk</p>
            </div>
        </div>

        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-3 bg-white border border-[#8C8A75]/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sidebar/40 focus:border-sidebar/50 placeholder:text-gray-400 transition-colors"
                    placeholder="Masukkan nama kategori">
                @error('name')
                <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('kategori') }}"
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
