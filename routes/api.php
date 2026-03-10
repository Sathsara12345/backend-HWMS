<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AccessControl\RoleController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\AccessControl\PermissionController;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MerchantManagement\MerchantController;

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

        Route::middleware(['auth:api', 'role:super-admin'])->prefix('super-admin')->group(function () {
            Route::get('/merchants', [MerchantController::class, 'index']);
            Route::get('/merchants/{id}', [MerchantController::class, 'show']);
            Route::post('/merchants', [MerchantController::class, 'store']);
            Route::put('/merchants/{id}', [MerchantController::class, 'update']);
            Route::delete('/merchants/{id}', [MerchantController::class, 'destroy']);
        });
    });
});
