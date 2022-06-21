<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('throttle:6,1')->get('/app/latest', function (Request $request) {
    $version = '1.0.9';
    return Storage::download('public/latest.apk', "latest-$version.apk", [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Disposition' => "attachment; filename=latest-$version.apk",
        'Status' => '200'
    ]);
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// require __DIR__.'/auth.php';
