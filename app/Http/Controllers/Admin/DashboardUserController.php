<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardUserController extends Controller
{
    /**
     * Display a paginated listing of users in the dashboard.
     */
    public function index()
    {
        // Fetch latest users with pagination for the admin view
        $users = User::latest()->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate mandatory input and the avatar file
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Hash password
        $password = bcrypt($request->password);

        // 3. Create user instance manually
        $user = new User();
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->password   = $password;
        
        // Handle optional text fields
        $user->phone      = $request->phone ?? ''; 
        $user->bio        = $request->bio ?? '';

        // 4. Handle Avatar Upload Logic
        if ($request->hasFile('avatar')) {
            // Stores in storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully with avatar.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user's information.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 1. Validate data (ignoring current user for email unique check)
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'bio'        => 'nullable|string',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. Handle Avatar Update Logic
        if ($request->hasFile('avatar')) {
            // Delete the old avatar from storage if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store the new one
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // 3. Update other fields manually to ensure consistency
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->phone      = $request->phone ?? $user->phone;
        $user->bio        = $request->bio ?? $user->bio;

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete the avatar file from disk before deleting user record
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }

    /**
     * Ban the specified user.
     */
    public function ban($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => true]);

        return redirect()->back()->with('success', 'User has been banned successfully.');
    }

    /**
     * Unban the specified user.
     */
    public function unban($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => false]);

        return redirect()->back()->with('success', 'User has been activated successfully.');
    }
}