<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kategori
    Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
    Route::get('/kategori/create', [CategoryController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{category}/edit', [CategoryController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

    // Kasir
    Route::get('/kasir', fn () => view('kasir.index'))->name('kasir');

    // Produk
    Route::get('/produk', [ProductController::class, 'index'])->name('produk');
    Route::get('/produk/create', [ProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProductController::class, 'store'])->name('produk.store');

    Route::get('/produk/{product}/edit', [ProductController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{product}', [ProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{product}', [ProductController::class, 'destroy'])->name('produk.destroy');

    // Riwayat
    Route::get('/riwayat', fn () => view('riwayat.index'))->name('riwayat');

});