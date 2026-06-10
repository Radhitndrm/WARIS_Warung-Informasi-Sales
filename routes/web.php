<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::post('/midtrans/notification', [PaymentNotificationController::class, 'handle'])->name('midtrans.notification');

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
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
    Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    Route::post('/kasir/payment-callback', [KasirController::class, 'paymentCallback'])->name('kasir.payment-callback');

    // Produk
    Route::get('/produk', [ProductController::class, 'index'])->name('produk');
    Route::get('/produk/create', [ProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProductController::class, 'store'])->name('produk.store');

    Route::get('/produk/{product}/edit', [ProductController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{product}', [ProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{product}', [ProductController::class, 'destroy'])->name('produk.destroy');

    // Riwayat
    Route::get('/riwayat', [ReportController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/export/pdf', [ReportController::class, 'exportPdf'])->name('riwayat.export.pdf');
    Route::get('/riwayat/export/excel', [ReportController::class, 'exportExcel'])->name('riwayat.export.excel');
    Route::get('/invoice/{order}', [ReportController::class, 'showInvoice'])->name('invoice.show');

    // Utang
    Route::get('/utang', [DebtController::class, 'index'])->name('utang');
    Route::get('/utang/{debt}', [DebtController::class, 'show'])->name('utang.show');
    Route::post('/utang/{debt}/bayar', [DebtController::class, 'storePayment'])->name('utang.bayar');
    Route::post('/utang/{debt}/payment-callback', [DebtController::class, 'paymentCallback'])->name('utang.payment-callback');
    Route::post('/utang/{debt}/payment-cancel', [DebtController::class, 'cancelPayment'])->name('utang.payment-cancel');

    Route::prefix('/chatbot')->name('chatbot.')->group(function () {
        Route::get('/', [ChatbotController::class, 'index'])->name('index');
        Route::post('/send', [ChatbotController::class, 'sendMessage'])->name('send');
        Route::get('/history', [ChatbotController::class, 'getHistory'])->name('history');
        Route::delete('/history', [ChatbotController::class, 'clearHistory'])->name('clear');
    });

    Route::post('/stt', [App\Http\Controllers\SttController::class, 'transcribe'])->name('stt.transcribe');

});
