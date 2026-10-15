<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OcrController;
use Illuminate\Support\Facades\Route;

if (class_exists(OcrController::class)) {
    Route::post('ocr/extract', [OcrController::class, 'extract']);
}

// Direct endpoints and /auth prefix endpoints
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth:sanctum')->get('me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->post('fcm-token', [NotificationController::class, 'registerToken']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    // Current authenticated user (Driver / Admin)
    Route::get('me', [AuthController::class, 'me']);

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
});