<?php

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

require __DIR__ . '/mobileAuth.php';
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/devices', [DeviceController::class, 'show']);
    Route::post('/user/add-new-device', [DeviceController::class, 'store']);
    Route::post('/user/device/{id}', [DeviceController::class, 'getSingleDevice']);
});
Route::post('/device/saveData', [DeviceDataController::class, 'store']);