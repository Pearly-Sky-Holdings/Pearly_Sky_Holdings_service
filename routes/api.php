<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\PackegesControllers;

Route::post('/login', [AuthControllers::class, 'login'])->name('login');
Route::get('/getPackege', [PackegesControllers::class, 'getDevices'])->name('getDevices');


Route::group(['middleware' => ['auth:sanctum']], function () {

   
});