<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Destination;

class AuthController extends Controller
{
    // 🟩 Show register form
    public function showRegisterForm()
    {
        $destinations = Destination::where('organisme_id', 158)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Auth/Register', [
            'destinations' => $destinations,
        ]);
    }

    // 🟩 Handle registration
    public function register(Request $request)
    {
        $fields = $request->validate([
            'destination_id' => ['required', 'exists:destinations,id'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'avatar' => ['nullable', 'file', 'max:300'],
        ]);

        $destination = Destination::find($request->destination_id);
        $fields['name'] = $destination->name;

        if ($request->hasFile('avatar')) {
            $fields['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $fields['password'] = bcrypt($fields['password']);

        $user = User::create($fields);
        Auth::login($user);

        return redirect()->route('dashboard')->with('greet', 'Welcome to DTN32!');
    }

    // 🟩 Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('Annuaire.index');
                case 'standard':
                    return redirect()->route('standard.index');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // 🟩 Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

   public function showProfile()
{
    return inertia('Profile', [
        'user' => auth()->user(),
    ]);
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = $request->user();
    $user->update([
        'password' => bcrypt($request->password),
    ]);

    return back()->with('success', 'Password changed successfully!');
}

}
