@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="min-h-screen flex items-center justify-center">

    <div class="bg-[#F5F4EF] p-10 rounded-2xl shadow-md w-[500px] text-center">

        <h1 class="text-4xl font-bold text-[#394766] mb-4">
            Dashboard WARIS
        </h1>

        <p class="text-gray-600 mb-8">
            Selamat datang, {{ Auth::user()->name }}
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                class="bg-red-500 text-white px-6 py-3 rounded-full"
            >
                Logout
            </button>
        </form>

    </div>

</div>

@endsection