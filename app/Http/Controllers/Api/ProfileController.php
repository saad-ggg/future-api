<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdatePasswordRequest; // Assuming you created the Request file

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * Update basic profile information and avatar.
     * Password update is handled in a separate method.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        /** * Validate profile data. 
         * 'sometimes' allows updating only specific fields sent in the request.
         */
        $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name'  => 'sometimes|required|string|max:255',
            'bio'        => 'sometimes|nullable|string',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'avatar'     => 'sometimes|required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /** * Update textual information.
         */
        $user->fill($request->only(['first_name', 'last_name', 'bio', 'email']));

        /** * Handle email verification reset if email is modified.
         */
        if ($request->has('email') && $user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        /** * Handle avatar upload and storage cleanup.
         */
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'user' => $user,
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null
        ]);
    }

    /**
     * Dedicated method for password updates with strict verification.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        /** * Check if the provided current password matches the database record.
         */
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The provided current password does not match our records.',
            ], 422);
        }

        /** * Update password and invalidate existing sessions/tokens.
         */
        $user->password = Hash::make($request->password);
        $user->tokens()->delete();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully. Please login again.',
        ]);
    }
}