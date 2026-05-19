@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-6">
    <h1 class="text-2xl font-bold text-primary">Dashboard</h1>
    <p class="text-muted">Selamat datang, {{ Auth::user()->name }}</p>
</div>

@endsection
