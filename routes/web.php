<?php

use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\GameController as CustomerGameController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\VoucherValidationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/games/{slug}', [CustomerGameController::class, 'show'])->name('games.show');
Route::get('/checkout/{game}/{product}', [CheckoutController::class, 'create'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/invoice/{invoiceNumber}', [InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/cek-status', function () {
    return Inertia\Inertia::render('Public/Status/Index');
})->name('status.check');
Route::post('/cek-status', function (Request $request) {
    $request->validate(['invoice_number' => ['required', 'string', 'max:255']]);

    return redirect()->route('invoice.show', $request->input('invoice_number'));
})->name('status.check.post');
Route::post('/voucher/validate', [VoucherValidationController::class, 'validate'])->name('voucher.validate');
Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
Route::get('/riwayat-transaksi', [App\Http\Controllers\Customer\OrderController::class, 'tracking'])->name('orders.tracking');

Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
