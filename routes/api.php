<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Admin\UserController;

/*
|-------------------------------------------------------------------------- 
| Public Routes
|-------------------------------------------------------------------------- 
*/

// OTP and Authentication
Route::post('/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

/*
|-------------------------------------------------------------------------- 
| Authenticated User Routes
|-------------------------------------------------------------------------- 
*/
Route::middleware('auth:sanctum')->group(function () {

    // Account Session
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Profile Management
     * POST is used for /profile to support multipart/form-data (Avatar uploads)
     */
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']); 
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
});

/*
|-------------------------------------------------------------------------- 
| Administrative Routes
|-------------------------------------------------------------------------- 
*/
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {

        // Health check for admin middleware
        Route::get('/test', fn() => response()->json(['status' => true, 'message' => 'Admin access confirmed']));

        /**
         * Users CRUD
         */
        Route::apiResource('users', UserController::class);

        //  Ban/Unban
        Route::post('/users/{user}/ban', [UserController::class, 'ban']);
        Route::post('/users/{user}/unban', [UserController::class, 'unban']);
});