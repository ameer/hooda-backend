<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/**
 * Mobile App Auth Routes
 */


Route::middleware(['api', 'guest', 'throttle:6,1'])->group(function () {
    Route::post('/auth/verify-phone', [RegisteredUserController::class, 'verifyPhone']);
    Route::post('/auth/verify-otp', [RegisteredUserController::class, 'verifyOTP']);
    Route::post('/auth/register', [RegisteredUserController::class, 'store']);
    Route::post('/auth/login', [AuthenticatedSessionController::class, 'generateSanctumToken']);
});
Route::middleware(['auth:sanctum', 'api'])->group(function () {
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'revokeToken'])
        ->name('mobileLogout');
});
