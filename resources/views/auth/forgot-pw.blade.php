@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')

<div class="min-h-screen flex items-center justify-center">

    <div class="bg-[#F5F4EF] w-full max-w-[420px] mx-4 rounded-2xl px-6 sm:px-10 py-10 sm:py-12 shadow-md">

        <h1 class="text-3xl font-bold text-center text-[#394766] mb-3">
            Forgot your password?
        </h1>

        <p class="text-center text-gray-500 text-sm mb-8">
            Enter your email so we can send your reset link
        </p>

        {{-- Pesan sukses setelah email terkirim --}}
        @if (session('status'))
            <div class="mb-6 bg-green-100 border border-green-300 text-green-600 text-sm rounded-xl px-4 py-3 text-center">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-600 text-sm rounded-xl px-4 py-3 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">

            @csrf

            <div class="mb-6">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') }}"
                    required
                    class="w-full border border-gray-400 rounded-full px-5 py-3 @error('email') border-red-400 @enderror"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#394766] text-white py-3 rounded-full font-semibold"
            >
                Send Email
            </button>

            <div class="text-center mt-5">
                <a href="{{ route('login') }}" class="text-sm text-gray-500">
                    Back to Login
                </a>
            </div>

        </form>
    </div>
</div>

@endsection