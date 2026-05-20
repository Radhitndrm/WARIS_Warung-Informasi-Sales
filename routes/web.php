<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/kasir', fn () => view('kasir.index'))->name('kasir');
    Route::get('/produk', fn () => view('produk.index'))->name('produk');
    Route::get('/riwayat', fn () => view('riwayat.index'))->name('riwayat');

});