<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        if ($request->hasFile('avatar')) {
            Storage::disk('public')->put('avatars',$request->avatar);
        }
       // dd('ok');

        // Validate input
        $fields = $request->validate([
            'avatar'=>['file','nullable','max:300'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);
        if ($request->hasFile('avatar')) {
            $fields['avatar']= Storage::disk('public')->put('avatars',$request->avatar);
        }
        // Register the user with hashed password
        $user = User::create($fields);

        // Log the user in
        Auth::login($user);

        // Redirect to home
        return redirect()->route('dashboard')->with('greet', 'welcom in DTN32');
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
