<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// PayPal Routes
Route::get('paypal/success', [App\Http\Controllers\PaymentController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('paypal/cancel', [App\Http\Controllers\PaymentController::class, 'paypalCancel'])->name('paypal.cancel');

// Stripe Routes
Route::get('stripe/success', [App\Http\Controllers\PaymentController::class, 'stripeSuccess'])->name('stripe.success');
Route::get('stripe/cancel', [App\Http\Controllers\PaymentController::class, 'stripeCancel'])->name('stripe.cancel');

// Payment Result Routes
Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'showSuccess'])->name('payment.success');
Route::get('payment/cancel', [App\Http\Controllers\PaymentController::class, 'showCancel'])->name('payment.cancel');
Route::get('payment/error', [App\Http\Controllers\PaymentController::class, 'showError'])->name('payment.error');
