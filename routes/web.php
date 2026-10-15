<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/buses', [BusController::class, 'index'])->name('buses');
    Route::post('/buses', [BusController::class, 'store'])->name('buses.store');
    Route::put('/buses/{bus}', [BusController::class, 'update'])->name('buses.update');
    Route::delete('/buses/{bus}', [BusController::class, 'destroy'])->name('buses.destroy');

    Route::get('/trips', [TripController::class, 'index'])->name('trips');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::get('/shifts', function () {
        return view('shifts', [
            'activeBuses' => \App\Models\Bus::where('status', 'active')->count(),
        ]);
    })->name('shifts');

    Route::get('/drivers', [\App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
    Route::post('/drivers', [\App\Http\Controllers\DriverController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{driver}', [\App\Http\Controllers\DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [\App\Http\Controllers\DriverController::class, 'destroy'])->name('drivers.destroy');
});

Route::get('/storage/{path}', function (string $path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $filePath = Storage::disk('public')->path($path);
    $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
    ]);
})->where('path', '.*');

Route::get('/share/{token}', function (string $token) {
    $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/');
    return redirect("{$frontendUrl}/share/{$token}");
});
