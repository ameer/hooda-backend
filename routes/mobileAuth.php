<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PhoneNumberVerifyController;
use Illuminate\Support\Facades\Route;

/**
 * Mobile App Auth Routes
 */


Route::middleware(['api', 'guest'])->group(function () {
    Route::post('/auth/verify-phone', [RegisteredUserController::class, 'verifyPhone']);
    Route::post('/auth/verify-otp', [RegisteredUserController::class, 'verifyOTP']);
    Route::post('/auth/login', [AuthenticatedSessionController::class, 'generateSanctumToken']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1');
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'revokeToken'])
        ->name('mobileLogout');
});
