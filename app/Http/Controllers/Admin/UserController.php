<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    // List users
    public function index()
    {
        return response()->json([
            'status' => true,
            'users'  => User::paginate(10)
        ]);
    }

    // Show user
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => true,
            'user'   => $user
        ]);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:users,email,' . $user->id,
            'phone'      => 'sometimes|string|max:20',
            'role'       => 'sometimes|in:user,admin',
            'password'   => 'sometimes|min:8',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully'
        ]);
    }

    //  Ban user
    public function ban($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'You cannot ban an admin'
            ], 403);
        }

        $user->update([
            'banned_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User banned successfully'
        ]);
    }

    //  Unban user
    public function unban($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'banned_at' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User unbanned successfully'
        ]);
    }
}
