<?php

use App\Http\Controllers\CustomerMassageControllers;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\JobApplicationControllers;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceDetailsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\PackegesControllers;
use App\Http\Controllers\CustomerControllers;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReStockingChecklistControllers;
use App\Http\Controllers\TranslationController;


Route::post('/login', [AuthControllers::class, 'login'])->name('login');
// New password reset routes
Route::post('/forgot_password', [AuthControllers::class, 'forgotPassword']);
Route::post('/forgot_password_With_out_user', [AuthControllers::class, 'forgotPasswordWithotUser']);
Route::post('/verify_otp', [AuthControllers::class, 'verifyOtp']);
Route::post('/reset_password', [AuthControllers::class, 'verifyOtpAndResetPassword']);

//packages
Route::get('/service_with_packages/{id}/', [PackegesControllers::class, 'getPackagesByService'])->name('getPackagesByService');

//re_stocking_checklists
Route::get('/re_stocking_checklists', [ReStockingChecklistControllers::class, 'getAll'])->name('getAll');

//JobApplication api
Route::post('/saveJobApplication', [JobApplicationControllers::class, 'save'])->name('save');

//customer api
Route::get('/getCustomers', [CustomerControllers::class, 'getAll'])->name('getAll');
Route::get('/searchCustomer/{id}', [CustomerControllers::class, 'search'])->name('search');
Route::post('/saveCustomer', [CustomerControllers::class, 'save'])->name('save');
Route::put('/updateCustomer/{id}', [CustomerControllers::class, 'update'])->name('update');
Route::delete('/deleteCustomer/{id}', [CustomerControllers::class, 'delete'])->name('delete');

// service api
Route::get('/getAllService', [ServiceController::class, 'getAll'])->name('getAll');
Route::post('/saveService', [ServiceController::class, 'save'])->name('save');
Route::get('/searchService/{id}', [ServiceController::class, 'search'])->name('search');
Route::put('/updateService/{id}', [ServiceController::class, 'update'])->name('update');
Route::delete('/deleteService/{id}', [ServiceController::class, 'destroy'])->name('destroy');

//feedback api
Route::get('/getFeedback', [FeedbackController::class, 'getAll'])->name('getAll');
Route::post('/saveFeedback', [FeedbackController::class, 'save'])->name('save');

Route::post('/saveMassage', [CustomerMassageControllers::class, 'save'])->name('save');

//order api
Route::get('/getOrders', [OrderController::class, 'getAll'])->name('getAll');
Route::get('/searchOrder/{id}', [OrderController::class, 'search'])->name('search');
Route::post('/saveOrder', [OrderController::class, 'save'])->name('save');
Route::put('/updateOrder/{id}', [OrderController::class, 'update'])->name('update');
Route::delete('/deleteOrder/{id}', [OrderController::class, 'delete'])->name('delete');
Route::get('/getOrdersByCustomer/{customerId}', [OrderController::class, 'getByCustomerId'])->name('getByCustomerId');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

//serviceDetails api
Route::post('/saveServiceDetails', [ServiceDetailsController::class, 'save'])->name('save');

Route::post('/translate', [TranslationController::class, 'translate']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    //packeges api
    Route::get('/getPackege', [PackegesControllers::class, 'getDevices'])->name('getDevices');
});