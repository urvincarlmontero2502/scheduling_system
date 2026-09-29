<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Public Health Check Endpoint (used by the dashboard status badges)
Route::get('/health', function () {
    $dbConnected = true;
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $dbConnected = false;
    }

    return response()->json([
        'api' => true,
        'database' => $dbConnected
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/dashboard/stats', [BookingController::class, 'stats']);

    // Resource routes
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::post('/resources', [ResourceController::class, 'store'])
        ->middleware('admin'); // Restricted to admins only

    Route::put('/resources/{resource}', [ResourceController::class, 'update'])
        ->middleware('admin');

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);

    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
        ->middleware('admin');

    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])
    ->middleware('admin');
});
