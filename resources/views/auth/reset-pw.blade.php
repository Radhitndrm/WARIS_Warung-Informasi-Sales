@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')

<div class="min-h-screen flex items-center justify-center">

    {{-- Styling card diseragamkan dengan halaman auth lainnya --}}
    <div class="bg-[#F5F4EF] w-[420px] rounded-2xl px-10 py-12 shadow-md">

        <h1 class="text-3xl font-bold text-center text-[#394766] mb-8">
            Reset Password
        </h1>

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-600 text-sm rounded-xl px-4 py-3 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div class="mb-4">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="{{ request('email') }}"
                    required
                    autofocus
                    class="w-full border border-gray-400 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-[#394766] @error('email') border-red-400 @enderror"
                >
            </div>

            <div class="mb-4">
                <input
                    type="password"
                    name="password"
                    placeholder="Password Baru"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-[#394766] @error('password') border-red-400 @enderror"
                >
            </div>

            <div class="mb-6">
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-[#394766]"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#394766] text-white py-3 rounded-full font-semibold hover:opacity-90 transition"
            >
                Reset Password
            </button>

        </form>

    </div>

</div>

@endsection