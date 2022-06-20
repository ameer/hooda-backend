<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('throttle:6,1')->get('/app/version', function (Request $request) {
    return response()->json([
        'version' => '1.0.7',
        'build' => '1',
        'needsUpdate' => true,
        'showMessage' => false,
        'message' => 'لطفا در اسرع وقت نسبت به بروزرسانی اپلیکیشن اقدام فرمایید.'
    ]);
});
require __DIR__ . '/mobileAuth.php';
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/update', [UserController::class, 'update']);
    Route::post('/user/add-admin/{id}', [UserController::class, 'create_substant_device_admin']);
    Route::post('/user/remove-admin/{id}', [UserController::class, 'remove_substant_device_admin']);
    Route::get('/user/devices', [DeviceController::class, 'show']);
    Route::post('/user/check-device', [DeviceController::class, 'checkDevice']);
    Route::post('/user/add-new-device', [DeviceController::class, 'store']);
    Route::put('/user/update-device/{id}', [DeviceController::class, 'update']);
    Route::post('/user/change-device-password/{id}', [DeviceController::class, 'change_device_password']);
    Route::delete('/user/delete-device/{id}', [DeviceController::class, 'destroy']);
    Route::post('/user/device/reset-factory/{id}', [DeviceController::class, 'reset_factory']);
    Route::post('/user/device/{id}', [DeviceController::class, 'getSingleDevice']);
    Route::post('/user/device/{id}/data', [DeviceDataController::class, 'getLatestData']);
});
Route::post('/device/saveData', [DeviceDataController::class, 'store']);
