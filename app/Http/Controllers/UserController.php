<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // DELETE a user
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['message' => "You can't delete yourself."]);
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }

    // UPDATE user role
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:user,admin,standard'],
        ]);

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Role updated successfully.');
    }
}
