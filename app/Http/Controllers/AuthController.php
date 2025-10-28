<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // -------------------------------
    // Register User
    // -------------------------------
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }

    // -------------------------------
    // Login User
    // -------------------------------
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    // -------------------------------
    // Forgot Password - Generate 6-digit code & send email
    // -------------------------------
     // Request code
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }

        // Generate 6-digit code
        $code = rand(100000, 999999);

        // Save code in DB (password_resets table or new table)
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => now()]
        );

        // Send code via email (Mail::to($user->email)->send(...))
        return response()->json(['code' => $code]);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid code'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used code
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}

