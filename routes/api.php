<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile']); // Fetch user
    Route::post('/profile/update', [ProfileController::class, 'update']); // Update user
});

// Protected routes (require JWT token)
Route::middleware('auth:api')->group(function () {
    Route::post('/payment', [PaymentController::class, 'makePayment']);
});