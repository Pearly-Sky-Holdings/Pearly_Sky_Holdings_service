<?php

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


Route::group(['middleware' => ['auth:sanctum']], function () {

   
});