<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserManagement\RoleController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\PermissionController;
use App\Http\Controllers\UserManagement\WebsiteStructureController;
use App\Http\Controllers\WebManagement\NavigationItemController;
use App\Http\Controllers\WebManagement\PageSectionController;
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
        Route::prefix('merchants/{merchant}')->group(function () {
 
                // Navigation items
                Route::get   ('navigation',         [NavigationItemController::class, 'index']);
                Route::post  ('navigation',         [NavigationItemController::class, 'store']);
                Route::patch ('navigation/reorder', [NavigationItemController::class, 'reorder']);
    
                // Page sections
                Route::get   ('sections',           [PageSectionController::class, 'index']);
                Route::post  ('sections',           [PageSectionController::class, 'store']);
                Route::patch ('sections/reorder',   [PageSectionController::class, 'reorder']);
            });
    
            // Single-resource routes (no merchant prefix needed — ID is in the model)
            Route::get   ('navigation/{navigationItem}',        [NavigationItemController::class, 'show']);
            Route::put   ('navigation/{navigationItem}',        [NavigationItemController::class, 'update']);
            Route::patch ('navigation/{navigationItem}/toggle', [NavigationItemController::class, 'toggle']);
            Route::delete('navigation/{navigationItem}',        [NavigationItemController::class, 'destroy']);
    
            Route::get   ('sections/{pageSection}',            [PageSectionController::class, 'show']);
            Route::put   ('sections/{pageSection}',            [PageSectionController::class, 'update']);
            Route::patch ('sections/{pageSection}/visibility', [PageSectionController::class, 'toggleVisibility']);
            Route::delete('sections/{pageSection}',            [PageSectionController::class, 'destroy']);

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
