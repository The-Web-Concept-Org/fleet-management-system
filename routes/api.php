<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardApiController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Google Gemini Vision OCR API endpoints for mobile & web
Route::prefix('ocr')->group(function (): void {
    Route::post('extract', [OcrController::class, 'extract']);
    Route::post('odometer', [OcrController::class, 'extractOdometer']);
    Route::post('meter', [OcrController::class, 'extractOdometer']);
    Route::post('fuel', [OcrController::class, 'extractFuel']);
});

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

    // Driver Operations Dashboard API: live stats, assigned bus, active shift, schedule & logs
    Route::get('dashboard', [DashboardApiController::class, 'index']);

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

    // Shifts & Meter logs API
    Route::get('shifts', [ShiftController::class, 'apiIndex']);
    Route::get('shifts/current', [ShiftController::class, 'current']);
    Route::post('shifts/start', [ShiftController::class, 'start']);
    Route::post('shifts/end', [ShiftController::class, 'end']);

    // Fuel records API: list recent fill-ups & store new fill-up
    Route::get('fuel', [FuelController::class, 'apiIndex']);
    Route::post('fuel', [FuelController::class, 'store']);

    // Maintenance records API: list service reports & store new work order
    Route::get('maintenance', [MaintenanceController::class, 'apiIndex']);
    Route::post('maintenance', [MaintenanceController::class, 'store']);

    // Trips & Transit Routes API
    Route::get('trips', [TripController::class, 'apiIndex']);
    Route::get('trips/current', [TripController::class, 'currentTrip']);
    Route::post('trips/{trip}/start', [TripController::class, 'startTrip'])->whereNumber('trip');
    Route::post('trips/{trip}/complete', [TripController::class, 'completeTrip'])->whereNumber('trip');
});