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

Route::prefix('api/v1')->group(function () {
    // Route::get('/register', [RegisteredUserController::class, 'create'])
    //     ->middleware('guest')
    //     ->name('register');

    Route::post('/auth/register', [RegisteredUserController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1']);

    Route::get('/auth/user', [RegisteredUserController::class, 'showUserData'])
        ->middleware(['auth:sanctum', 'throttle:6,1']);

    Route::post('/auth/verify-phone', [RegisteredUserController::class, 'verifyPhone'])
        ->middleware('guest');

    Route::post('/auth/verify-otp', [RegisteredUserController::class, 'verifyOTP'])
        ->middleware('guest');

    // Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    //     ->middleware('guest')
    //     ->name('login');

    Route::post('/auth/login', [AuthenticatedSessionController::class, 'generateSanctumToken'])
        ->middleware('guest');

    // Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    //     ->middleware('guest')
    //     ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    // Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    //     ->middleware('guest')
    //     ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    // Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    //     ->middleware('auth')
    //     ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->middleware('auth')
        ->name('password.confirm');

    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'revokeToken'])
        ->middleware('auth:sanctum');
    // Route::get('/verify-phone', [PhoneNumberVerifyController::class, 'create'])->name('phoneverification.show');
    // Route::post('/verify-phone', [PhoneNumberVerifyController::class, 'verify'])->name('phoneverification.verify');
});
