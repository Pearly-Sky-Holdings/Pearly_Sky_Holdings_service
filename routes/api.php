<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceDetailsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\PackegesControllers;
use App\Http\Controllers\CustomerControllers;
use App\Http\Controllers\ServiceController;

Route::post('/login', [AuthControllers::class, 'login'])->name('login');
Route::get('/getPackege', [PackegesControllers::class, 'getDevices'])->name('getDevices');


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

//order api
Route::get('/getOrders', [OrderController::class, 'getAll'])->name('getAll');
Route::get('/searchOrder/{id}', [OrderController::class, 'search'])->name('search');
Route::post('/saveOrder', [OrderController::class, 'save'])->name('save');
Route::put('/updateOrder/{id}', [OrderController::class, 'update'])->name('update');
Route::delete('/deleteOrder/{id}', [OrderController::class, 'delete'])->name('delete');
Route::get('/getOrdersByCustomer/{customerId}', [OrderController::class, 'getByCustomerId'])->name('getByCustomerId');

//serviceDetails api
Route::post('/saveServiceDetails', [ServiceDetailsController::class, 'save'])->name('save');


Route::group(['middleware' => ['auth:sanctum']], function () {

   
});