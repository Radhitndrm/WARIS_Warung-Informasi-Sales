@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">Kategori</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola daftar kategori produk</p>
        </div>
        <a href="{{ route('kategori.create') }}"
            class="flex items-center gap-2 bg-sidebar text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-sidebar-hover transition-colors shadow-sm">
            <i class="fa-solid fa-plus"></i>
            Tambah Kategori
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
    <div class="bg-[#C1F2D0] border border-[#8C8A75] text-gray-800 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-2.5">
        <i class="fa-solid fa-circle-check text-green-600"></i>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-[#F7CDCD] border border-[#8C8A75] text-gray-800 px-5 py-3.5 rounded-xl text-sm font-medium flex items-center gap-2.5">
        <i class="fa-solid fa-circle-exclamation text-red-600"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-[#8C8A75]/30">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Jumlah Produk</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-56">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#8C8A75]/20">
                @forelse ($categories as $category)
                <tr class="hover:bg-[#E6E4CE]/40 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#DBC5E8] flex items-center justify-center text-gray-700 shrink-0">
                                <i class="fa-solid fa-tag"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $category->name }}</p>
                                <p class="text-xs text-gray-400">ID: #{{ $category->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center bg-[#BFDCDE] text-gray-800 text-xs font-bold px-3 py-1.5 rounded-full min-w-[32px]">
                            {{ $category->products_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('kategori.edit', $category) }}"
                                class="inline-flex items-center gap-1.5 bg-[#BFDCDE] text-gray-800 px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#A8C8CA] transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </a>
                            <button @click="$dispatch('open-modal', 'delete-{{ $category->id }}')"
                                class="inline-flex items-center gap-1.5 bg-[#F7CDCD] text-gray-800 px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#E8B8B8] transition-colors">
                                <i class="fa-solid fa-trash-can"></i>
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center text-gray-400">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fa-solid fa-folder-open text-2xl text-gray-300"></i>
                        </div>
                        <p class="font-semibold text-gray-500">Belum ada kategori</p>
                        <p class="text-sm mt-1">Klik "Tambah Kategori" untuk menambahkan kategori pertama.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Delete --}}
@foreach ($categories as $category)
<div x-data="{ open: false }"
    x-show="open"
    x-cloak
    @open-modal.window="if ($event.detail === 'delete-{{ $category->id }}') open = true"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 transition-opacity"></div>
    <div x-show="open" @click.outside="open = false"
        class="relative bg-[#F4F2DE] rounded-2xl border border-[#8C8A75] shadow-xl w-full max-w-md p-6 z-10 transition-all">
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-[#F7CDCD] flex items-center justify-center">
                <i class="fa-solid fa-trash-can text-red-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Hapus Kategori</h3>
            <p class="text-sm text-gray-500 mt-1">Apakah Anda yakin ingin menghapus kategori <strong>"{{ $category->name }}"</strong>?</p>
            @if ($category->products_count > 0)
            <p class="text-xs text-red-500 mt-2 font-medium">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                Kategori ini memiliki {{ $category->products_count }} produk terkait dan tidak dapat dihapus.
            </p>
            @endif
        </div>
        <div class="flex justify-center gap-3">
            <button type="button" @click="open = false"
                class="px-5 py-2 text-sm font-semibold text-gray-600 bg-white border border-[#8C8A75]/60 rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <form action="{{ route('kategori.destroy', $category) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" {{ $category->products_count > 0 ? 'disabled' : '' }}
                    class="px-5 py-2 text-sm font-semibold text-white {{ $category->products_count > 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600' }} rounded-xl transition-colors">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
