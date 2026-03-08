<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AdminDashboardController;

// Login
Route::post('/login', [AuthController::class , 'login']);

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/me', function () { return auth('api')->user();});

        // Admin routes
        Route::middleware(['role:super-admin|admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class , 'index']);

            // Roles and Permissions routes
            Route::apiResource('roles', RoleController::class);
            Route::get('all-permissions', [RoleController::class, 'getAllPermissions']);
            Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
            Route::apiResource('permissions', PermissionController::class);
            Route::apiResource('users', UserController::class);
        });
    });
});
