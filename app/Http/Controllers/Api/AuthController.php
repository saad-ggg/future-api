<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OTP Configuration (Mock)
    |--------------------------------------------------------------------------
    */
    private function otp()
    {
        return '1234';
    }

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'password'   => 'required|min:8',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'password'   => Hash::make($data['password']),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User registered. Please verify your account using the OTP sent to your email.',
            'data'    => ['email' => $user->email]
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | OTP Management (Send & Verify)
    |--------------------------------------------------------------------------
    */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email |exists:users,email']);

        Mail::raw(
            'Your OTP code is: ' . $this->otp(),
            function ($message) use ($request) {
                $message->to($request->email)->subject('Your OTP Code');
            }
        );

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent to your email',
            'data'    => null
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string',
        ]);

        if ($request->otp !== $this->otp()) { 
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP',
                'data'    => null
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Account verified successfully',
            'data'    => [
                'token' => $token,
                'user'  => $user
            ]
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication (Login & Logout)
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
                'data'    => null
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Logged in successfully',
            'data'    => [
                'token' => $token,
                'user'  => $user
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
            'data'    => null
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | User Profile & Password Reset
    |--------------------------------------------------------------------------
    */
    public function me(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'User data',
            'data'    => ['user' => $request->user()]
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8',
            'otp'      => 'required|string'
        ]);

        if ($request->otp !== $this->otp()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP',
                'data'    => null
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'status'  => true,
            'message' => 'Password reset successfully',
            'data'    => null
        ]);
    }
}