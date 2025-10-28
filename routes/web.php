<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// -------------------------------
// 1. Test Register (GET browser version)
// -------------------------------
Route::get('/test-register', function () {
    $controller = new AuthController();

    $request = new Request([
        'name' => 'Heng',
        'email' => 'heng@example.com',
        'password' => '123456',
        'password_confirmation' => '123456'
    ]);

    return $controller->register($request);
});

// -------------------------------
// 2. Test Login
// -------------------------------
Route::get('/test-login', function () {
    $controller = new AuthController();

    $request = new Request([
        'email' => 'heng@example.com',
        'password' => '123456'
    ]);

    return $controller->login($request);
});

// -------------------------------
// 3. Test Forgot Password
// -------------------------------
Route::get('/test-forgot', function (Request $request) {
    $email = $request->query('email', 'heng@example.com');

    $controller = new AuthController();
    $req = new Request(['email' => $email]);

    return $controller->forgotPassword($req);
});

// -------------------------------
// 4. Test Reset Password
// -------------------------------
Route::get('/test-reset', function (Request $request) {
    $email = $request->query('email', 'heng@example.com');
    $controller = new AuthController();

    // Step 1: Generate code
    $controller->forgotPassword(new Request(['email' => $email]));

    // Step 2: Get latest code
    $code = DB::table('password_resets')
              ->where('email', $email)
              ->latest('created_at')
              ->value('token');

    if (!$code) {
        return response()->json(['error' => 'Code not found']);
    }

    // Step 3: Reset password
    $req = new Request([
        'email' => $email,
        'token' => $code,
        'password' => '123456',
        'password_confirmation' => '123456'
    ]);

    return $controller->resetPassword($req);
});

// -------------------------------
// 5. Test Payment
// -------------------------------
Route::get('/test-payment', function () {
    $user = (object)[
        'id' => 1,
        'name' => 'Heng',
        'email' => 'heng@example.com',
    ];

    $aba_response = [
        'status' => 'success',
        'transactionID' => 'TEST-' . time()
    ];

    return response()->json([
        'user_id' => $user->id,
        'amount' => 50,
        'aba_response' => $aba_response
    ]);
});
