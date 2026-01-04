<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the administrative login page.
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard immediately
        // Note: Using 'role' column as per your database structure
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Process the admin login request.
     */
    public function login(Request $request)
    {
        // Validate incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. Check if the user exists in the database by email
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found in our database.']);
        }

        // 2. Attempt to authenticate with provided credentials
        if (Auth::attempt($credentials)) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // 3. Verify Admin privileges using the 'role' column
            if ($user->role === 'admin') {
                return redirect()->intended('admin');
            }

            // Authentication passed but the user does not have admin role
            Auth::logout();
            return back()->withErrors(['email' => 'Access Denied: Your account is not authorized as Admin.']);
        }

        // 4. Handle failed authentication (wrong password)
        return back()->withErrors(['email' => 'Invalid password. Please try again.']);
    }

    /**
     * Handle admin logout and session destruction.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate current session and regenerate CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}