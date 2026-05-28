<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
    Route::get('/kategori/create', [CategoryController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{category}/edit', [CategoryController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/kasir', fn () => view('kasir.index'))->name('kasir');
    Route::get('/produk', fn () => view('produk.index'))->name('produk');
    Route::get('/riwayat', fn () => view('riwayat.index'))->name('riwayat');

});