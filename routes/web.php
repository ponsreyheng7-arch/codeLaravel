<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Temporary test route for registration
Route::get('/test-register', function (Request $request) {
    // Example test user data
    $request->merge([
        'name' => 'Heng',
        'email' => 'heng@example.com',
        'password' => '123456',
        // 'comform password' => '123456'
    ]);

    $controller = new AuthController();
    return $controller->register($request);
});

// Temporary test route for login
Route::get('/test-login', function (Request $request) {
    $request->merge([
        'email' => 'heng@example.com',
        'password' => '123456'
    ]);

    $controller = new AuthController();
    return $controller->login($request);
});

// Temporary test route for payment (simulate logged-in user)
Route::get('/test-payment', function () {
    $user = (object)[
        'id' => 1,
        'name' => 'Heng',
        'email' => 'heng@example.com',
    ];

    $amount = 50; // example amount
    $aba_response = [
        'status' => 'success',
        'transactionID' => 'TEST-' . time()
    ];

    return response()->json([
        'user_id' => $user->id,
        'amount' => $amount,
        'aba_response' => $aba_response
    ]);
});
// In web.php
Route::get('/test-forgot-password', function () {
    $request = new \Illuminate\Http\Request();
    $request->merge(['email' => 'heng@example.com']);

    $controller = new \App\Http\Controllers\AuthController();
    return $controller->forgotPassword($request);
});

Route::get('/test-reset', function () {
    $email = 'heng@example.com';
    $controller = new \App\Http\Controllers\AuthController();

    // Step 1: Generate forgot password token
    $requestForgot = new \Illuminate\Http\Request();
    $requestForgot->merge(['email' => $email]);
    $responseForgot = $controller->forgotPassword($requestForgot);

    // Get token from response
    $token = $responseForgot->getData()->token;

    // Step 2: Reset password using the token
    $requestReset = new \Illuminate\Http\Request();
    $requestReset->merge([
        'email' => $email,
        'token' => $token,
        'password' => '123456',
        'password_confirmation' => '123456'
    ]);

    return $controller->resetPassword($requestReset);
});





