<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetOTPController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/**
 * Mobile App Auth Routes
 */


Route::middleware(['api', 'guest', 'throttle:6,1'])->group(function () {
    Route::post('/auth/verify-phone', [RegisteredUserController::class, 'verifyPhone'])->middleware('throttle:4,1');
    Route::post('/auth/verify-otp', [RegisteredUserController::class, 'verifyOTP']);
    Route::post('/auth/register', [RegisteredUserController::class, 'store']);
    Route::post('/auth/login', [AuthenticatedSessionController::class, 'generateSanctumToken']);
    Route::post('/auth/forgot-password', [PasswordResetOTPController::class, 'handlePasswordResetRequest']);
    Route::post('/auth/forgot-password/verify-otp', [PasswordResetOTPController::class, 'verifyOTP']);
    Route::post('/auth/reset-user-password', [PasswordResetOTPController::class, 'resetUserPassword']);
});
Route::middleware(['auth:sanctum', 'api'])->group(function () {
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'revokeToken'])
        ->name('mobileLogout');
});
