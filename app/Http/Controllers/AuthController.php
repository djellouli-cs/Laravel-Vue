<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Destination;
class AuthController extends Controller
{

public function register(Request $request)
{
    // Validate inputs
    $fields = $request->validate([
        'destination_id' => ['required', 'exists:destinations,id'],
        'email' => ['required', 'email', 'unique:users'],
        'password' => ['required', 'confirmed'],
        'avatar' => ['nullable', 'file', 'max:300'],
    ]);

    // Get destination name from destination_id
    $destination = Destination::find($request->destination_id);
    $fields['name'] = $destination->name;

    // Store avatar (if any)
    if ($request->hasFile('avatar')) {
        $fields['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    // Hash password
    $fields['password'] = bcrypt($fields['password']);

    // Create user
    $user = User::create($fields);

    Auth::login($user);

    return redirect()->route('dashboard')->with('greet', 'Welcome to DTN32!');
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials,$request->remember)) {
            $request->session()->regenerate();
 
            return redirect()->intended(route('Annuaire.index'));
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
 
    $request->session()->invalidate();
 
    $request->session()->regenerateToken();
 
    return redirect('/');
    }
}
