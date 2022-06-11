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
        'version' => '1.0.0',
        'build' => '1',
        'needsUpdate' => true,
        'showMessage' => true,
        'message' => 'لطفا در اسرع وقت نسبت به بروزرسانی اپلیکیشن اقدام فرمایید.'
    ]);
});
Route::middleware('throttle:6,1')->get('/app/latest.apk', function (Request $request) {
    return Storage::download('public/latest.apk', 'latest.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Disposition' => 'attachment; filename=latest.apk',
        'Status' => '200'
    ]);
});
require __DIR__ . '/mobileAuth.php';
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/update', [UserController::class, 'update']);
    Route::post('/user/add-admin/{id}', [UserController::class, 'create_substant_device_admin']);
    Route::get('/user/devices', [DeviceController::class, 'show']);
    Route::post('/user/check-device', [DeviceController::class, 'checkDevice']);
    Route::post('/user/add-new-device', [DeviceController::class, 'store']);
    Route::put('/user/update-device/{id}', [DeviceController::class, 'update']);
    Route::delete('/user/delete-device/{id}', [DeviceController::class, 'destroy']);
    Route::post('/user/device/{id}', [DeviceController::class, 'getSingleDevice']);
    Route::post('/user/device/{id}/data', [DeviceDataController::class, 'getLatestData']);
});
Route::post('/device/saveData', [DeviceDataController::class, 'store']);
