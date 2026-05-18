@extends('layouts.app')

@section('title', 'Register')

@section('content')

<div class="min-h-screen flex items-center justify-center">

    <div class="bg-[#F5F4EF] w-[420px] rounded-2xl px-10 py-12 relative shadow-md">

        <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-[#E4DFB5] px-10 py-2 rounded-xl">
            <h1 class="text-2xl font-bold text-[#394766]">
                Register
            </h1>
        </div>

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="mt-6 mb-2 bg-red-100 border border-red-300 text-red-600 text-sm rounded-xl px-4 py-3 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-8">

            @csrf

            <div class="mb-4">
                <input
                    type="text"
                    name="name"
                    placeholder="Username"
                    value="{{ old('name') }}"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 @error('name') border-red-400 @enderror"
                >
            </div>

            <div class="mb-4">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') }}"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 @error('email') border-red-400 @enderror"
                >
            </div>

            <div class="mb-4">
                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 @error('password') border-red-400 @enderror"
                >
            </div>

            {{-- Password confirmation yang sebelumnya kurang --}}
            <div class="mb-6">
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#394766] text-white py-3 rounded-full font-semibold"
            >
                Sign Up
            </button>

            <p class="text-center text-sm mt-5 text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-[#394766]">
                    Login
                </a>
            </p>

        </form>
    </div>
</div>

@endsection