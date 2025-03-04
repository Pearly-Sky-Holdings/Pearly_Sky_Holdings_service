<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/success/{orderId}/{method}', [App\Http\Controllers\PaymentController::class, 'handleSuccess'])->name('payment.success');
Route::get('/payment/cancel/{orderId}', [App\Http\Controllers\PaymentController::class, 'handleCancel'])->name('payment.cancel');

// Basic payment controller
Route::controller(App\Http\Controllers\PaymentController::class)->group(function () {
    Route::post('/payment/process', 'processPayment');
    Route::get('/payment/status/{orderId}', 'getPaymentStatus');
});