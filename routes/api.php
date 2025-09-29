<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KampanyeController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/kampanye', [KampanyeController::class, 'index']);
Route::get('/kampanye/{id}', [KampanyeController::class, 'show']);

// Authentication routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);
    
    // User routes
    Route::get('/user/saldo', [UserController::class, 'saldo']);
    
    // Donasi routes
    Route::post('/donasi/create', [DonasiController::class, 'create']);
    Route::get('/donasi/riwayat', [DonasiController::class, 'riwayat']);
    Route::get('/donasi/status/{orderId}', [DonasiController::class, 'status']);
    Route::post('/donasi/confirm-payment', [DonasiController::class, 'confirmPayment']);
});
