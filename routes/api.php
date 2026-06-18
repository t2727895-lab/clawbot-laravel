<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClawbotController;
use App\Http\Controllers\Api\PostApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication endpoint for Clawbot
Route::post('/auth/login', [AuthController::class, 'login']);

// Public Post Creation API (no authentication required)
Route::post('/posts/create', [PostApiController::class, 'store']);

// Protected Clawbot endpoints (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Submit a new post for approval
    // Route::post('/posts', [ClawbotController::class, 'store']);

    // Get post status
    // Route::get('/posts/{post}', [ClawbotController::class, 'show']);

    // // Publish result callback (Clawbot confirms publish attempt result)
    // Route::post('/posts/{post}/publish-result', [ClawbotController::class, 'publishResult']);
});
