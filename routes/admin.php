<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceDataController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'abilities:users:manage'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/devices', [DeviceController::class, 'getAllDevices']);
});
Route::prefix('admin/auth')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'generateAdminToken'])
        ->middleware('guest');
});
