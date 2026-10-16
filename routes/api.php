<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

if (class_exists(OcrController::class)) {
    Route::post('ocr/extract', [OcrController::class, 'extract']);
}

// Direct endpoints and /auth prefix endpoints
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Driver password recovery (throttled to slow down brute-force attempts)
Route::middleware('throttle:6,1')->group(function (): void {
    Route::post('forgot-password', [PasswordResetController::class, 'forgot']);
    Route::post('reset-password', [PasswordResetController::class, 'reset']);
});

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('throttle:6,1')->group(function (): void {
        Route::post('forgot-password', [PasswordResetController::class, 'forgot']);
        Route::post('reset-password', [PasswordResetController::class, 'reset']);
    });

    Route::middleware('auth:sanctum')->get('me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->post('fcm-token', [NotificationController::class, 'registerToken']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    // Current authenticated user (Driver / Admin)
    Route::get('me', [AuthController::class, 'me']);

    // Driver self-service profile (email is not editable)
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::patch('profile', [ProfileController::class, 'update']);

    // Notification inbox
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('notifications/tokens', [NotificationController::class, 'tokens']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::delete('notifications/token', [NotificationController::class, 'removeToken']);
    Route::post('notifications/test', [NotificationController::class, 'test']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead'])->whereNumber('id');

    // Drivers API
    Route::get('drivers', [DriverController::class, 'index']);
    Route::post('drivers', [DriverController::class, 'store']);
    Route::put('drivers/{driver}', [DriverController::class, 'update']);
    Route::delete('drivers/{driver}', [DriverController::class, 'destroy']);

    // Shift meter logs: capture the meter photo, KM reading and fuel detail
    // when a vehicle leaves the depot and again when it returns.
    Route::post('shifts/start', [ShiftController::class, 'start']);
    Route::post('shifts/end', [ShiftController::class, 'end']);

    // Fuel records captured from the app: receipt/pump image, liters, fuel type,
    // price and the moment the fuel was added. Store only — no edit or delete.
    Route::post('fuel', [FuelController::class, 'store']);

    // Maintenance records captured from the app: bill/workshop invoice image,
    // price, note plus the vehicle, driver and trip. Store only — no edit or delete.
    Route::post('maintenance', [MaintenanceController::class, 'store']);
});