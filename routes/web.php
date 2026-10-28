<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TelemetryController;
use App\Http\Controllers\TransitRouteController;
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
    Route::get('/telemetry/live', [TelemetryController::class, 'getLiveBuses'])->name('telemetry.live');

    // Transit Routes Management (Dedicated Admin Page)
    Route::get('/routes', [TransitRouteController::class, 'index'])->name('routes.index');
    Route::post('/routes', [TransitRouteController::class, 'store'])->name('routes.store');
    Route::put('/routes/{route}', [TransitRouteController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{route}', [TransitRouteController::class, 'destroy'])->name('routes.destroy');
    Route::post('/routes/{route}/assign', [TransitRouteController::class, 'assignBuses'])->name('routes.assign');

    Route::get('/buses', [BusController::class, 'index'])->name('buses');
    Route::post('/buses', [BusController::class, 'store'])->name('buses.store');
    Route::put('/buses/{bus}', [BusController::class, 'update'])->name('buses.update');
    Route::post('/buses/{bus}/route', [BusController::class, 'updateRoute'])->whereNumber('bus')->name('buses.route');
    Route::delete('/buses/{bus}', [BusController::class, 'destroy'])->name('buses.destroy');

    Route::get('/trips', [TripController::class, 'index'])->name('trips');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts');
    Route::get('/shifts/{shift}', [ShiftController::class, 'show'])
        ->whereNumber('shift')
        ->name('shifts.show');
    Route::post('/shifts/{shift}/verify', [ShiftController::class, 'verify'])
        ->whereNumber('shift')
        ->name('shifts.verify');
    Route::post('/shifts/{shift}/flag', [ShiftController::class, 'flag'])
        ->whereNumber('shift')
        ->name('shifts.flag');
    Route::post('/shifts/{shift}/status', [ShiftController::class, 'updateStatus'])
        ->whereNumber('shift')
        ->name('shifts.status');
    Route::post('/shifts/{shift}/mileage', [ShiftController::class, 'adjustMileage'])
        ->whereNumber('shift')
        ->name('shifts.mileage');

    Route::get('/fuel', [FuelController::class, 'index'])->name('fuel');
    Route::post('/fuel/{fuelLog}/status', [FuelController::class, 'updateStatus'])
        ->whereNumber('fuelLog')
        ->name('fuel.status');

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance/{maintenanceLog}/status', [MaintenanceController::class, 'updateStatus'])
        ->whereNumber('maintenanceLog')
        ->name('maintenance.status');

    Route::get('/drivers', [\App\Http\Controllers\DriverController::class, 'index'])->name('drivers');
    Route::post('/drivers', [\App\Http\Controllers\DriverController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{driver}', [\App\Http\Controllers\DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [\App\Http\Controllers\DriverController::class, 'destroy'])->name('drivers.destroy');

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/broadcast', [\App\Http\Controllers\NotificationController::class, 'broadcast'])->name('notifications.broadcast');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->whereNumber('id')->name('notifications.read');

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');
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
