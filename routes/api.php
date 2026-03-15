<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserManagement\RoleController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\PermissionController;
use App\Http\Controllers\UserManagement\WebsiteStructureController;

// Login
Route::post('/login', [AuthController::class , 'login']);

// Public Website Data
Route::get('/v1/public/hotel-website', [\App\Http\Controllers\Public\HotelWebsiteController::class, 'show']);

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:api'])->group(function () {

        // Admin routes
        Route::middleware(['role:super-admin|admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class , 'index']);
            
            // Merchant paths (Commonly used by admins too)
            Route::get('/merchants', [MerchantController::class, 'index']);
            Route::get('/merchants/{id}', [MerchantController::class, 'show']);
            Route::post('/merchants', [MerchantController::class, 'store']);
            Route::put('/merchants/{id}', [MerchantController::class, 'update']);
            Route::delete('/merchants/{id}', [MerchantController::class, 'destroy']);

            // Website Structure Management
            Route::get('/merchants/{id}/navigation', [WebsiteStructureController::class, 'getNavigation']);
            Route::post('/merchants/{id}/navigation', [WebsiteStructureController::class, 'updateNavigation']);
            Route::get('/merchants/{id}/sections', [WebsiteStructureController::class, 'getSections']);
            Route::post('/merchants/{id}/sections', [WebsiteStructureController::class, 'updateSections']);

            // User management
            Route::middleware(['role:super-admin'])->group(function () {
                Route::apiResource('roles', RoleController::class);
                Route::get('all-permissions', [RoleController::class, 'getAllPermissions']);
                Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
                Route::apiResource('permissions', PermissionController::class);
                Route::apiResource('users', UserController::class);
                Route::post('/roles/assign-permissions-to-user/{userId}', [RoleController::class, 'assignPermissionsToUser']);
            });
        });
    });
});
