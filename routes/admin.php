<?php

use App\Http\Controllers\Admin\ApiConfigController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PaymentChannelController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VoucherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('games', GameController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');

    Route::resource('payment-methods', PaymentMethodController::class)->except('show');
    Route::get('payment-methods/{paymentMethod}/channels/create', [PaymentChannelController::class, 'create'])->name('payment-methods.channels.create');
    Route::post('payment-methods/{paymentMethod}/channels', [PaymentChannelController::class, 'store'])->name('payment-methods.channels.store');
    Route::get('channels/{channel}/edit', [PaymentChannelController::class, 'edit'])->name('channels.edit');
    Route::put('channels/{channel}', [PaymentChannelController::class, 'update'])->name('channels.update');
    Route::delete('channels/{channel}', [PaymentChannelController::class, 'destroy'])->name('channels.destroy');

    Route::resource('banners', BannerController::class)->except('show');
    Route::resource('vouchers', VoucherController::class)->except('show');
    Route::resource('news', NewsController::class)->except('show');

    Route::get('/api-configs', [ApiConfigController::class, 'index'])->name('api-configs.index');
    Route::put('/api-configs', [ApiConfigController::class, 'update'])->name('api-configs.update');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});