<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientPageController;
use App\Http\Controllers\Api\PageThemeController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\PublicPageController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public page view
// Route::get('/pages/{slug}', [PublicPageController::class, 'show']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Page management
    Route::get('/my-page', [ClientPageController::class, 'show']);
    Route::put('/my-page', [ClientPageController::class, 'update']);
    Route::post('/my-page/logo', [ClientPageController::class, 'uploadLogo']);
    Route::post('/my-page/background', [ClientPageController::class, 'uploadBackground']);
    
    // Themes
    Route::get('/themes', [PageThemeController::class, 'index']);
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);
    Route::get('/analytics/export', [AnalyticsController::class, 'export']);
});