<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserManagement\RoleController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\PermissionController;
use App\Http\Controllers\WebManagement\NavigationItemController;
use App\Http\Controllers\WebManagement\PageSectionController;
use App\Http\Controllers\WebManagement\SectionContentController;

// ── Public (no auth) ──────────────────────────────────────────

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('v1/public')->group(function () {
    Route::get('hotel-website',         [\App\Http\Controllers\Public\HotelWebsiteController::class, 'show']);
    Route::get('hotel-website/nav',     [\App\Http\Controllers\Public\HotelWebsiteController::class, 'navigation']);
    Route::get('hotel-website/sitemap', [\App\Http\Controllers\Public\HotelWebsiteController::class, 'sitemap']);
});

// ── Authenticated ─────────────────────────────────────────────

Route::prefix('v1')->middleware(['auth:api'])->group(function () {

    Route::middleware(['role:super-admin|admin'])->prefix('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Merchants
        Route::get   ('/merchants',      [MerchantController::class, 'index']);
        Route::get   ('/merchants/{id}', [MerchantController::class, 'show']);
        Route::post  ('/merchants',      [MerchantController::class, 'store']);
        Route::put   ('/merchants/{id}', [MerchantController::class, 'update']);
        Route::delete('/merchants/{id}', [MerchantController::class, 'destroy']);

        // ── Scoped under merchant ─────────────────────────────

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

        // ── Single navigation item ────────────────────────────

        Route::get   ('navigation/{navigationItem}',          [NavigationItemController::class, 'show']);
        Route::put   ('navigation/{navigationItem}',          [NavigationItemController::class, 'update']);
        Route::patch ('navigation/{navigationItem}/toggle',   [NavigationItemController::class, 'toggle']);
        Route::post  ('navigation/{navigationItem}/og-image', [NavigationItemController::class, 'uploadOgImage']);
        Route::delete('navigation/{navigationItem}/og-image', [NavigationItemController::class, 'deleteOgImage']);
        Route::delete('navigation/{navigationItem}',          [NavigationItemController::class, 'destroy']);

        // ── Single page section ───────────────────────────────

        Route::get   ('sections/{pageSection}',            [PageSectionController::class, 'show']);
        Route::put   ('sections/{pageSection}',            [PageSectionController::class, 'update']);
        Route::patch ('sections/{pageSection}/visibility', [PageSectionController::class, 'toggleVisibility']);
        Route::delete('sections/{pageSection}',            [PageSectionController::class, 'destroy']);

        // ── Section contents ──────────────────────────────────

        Route::get   ('sections/{pageSection}/contents',             [SectionContentController::class, 'index']);
        Route::put   ('sections/{pageSection}/contents',             [SectionContentController::class, 'updateFields']);
        Route::post  ('sections/{pageSection}/contents/media',       [SectionContentController::class, 'uploadMedia']);
        Route::delete('sections/{pageSection}/contents/media/{key}', [SectionContentController::class, 'deleteMedia']);

        // ── Super admin only ──────────────────────────────────

        Route::middleware(['role:super-admin'])->group(function () {
            Route::apiResource('roles', RoleController::class);
            Route::get ('all-permissions',                           [RoleController::class, 'getAllPermissions']);
            Route::post('roles/{role}/assign-permissions',           [RoleController::class, 'assignPermissions']);
            Route::post('roles/assign-permissions-to-user/{userId}', [RoleController::class, 'assignPermissionsToUser']);
            Route::apiResource('permissions', PermissionController::class);
            Route::apiResource('users',       UserController::class);
        });
    });
});