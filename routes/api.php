<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;

// Login
Route::post('/login', [AuthController::class , 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', function () { return auth('api')->user();});

    // Admin routes
    Route::middleware(['role:super-admin|admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class , 'index']);
    });
});
