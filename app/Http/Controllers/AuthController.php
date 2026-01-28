<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AuthController extends Controller
{
    /* =======================
     |  REGISTER
     ======================= */
    public function showRegisterForm()
    {
        $destinations = Destination::where('organisme_id', 158)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Auth/Register', [
            'destinations' => $destinations,
        ]);
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'destination_id' => ['required', 'exists:destinations,id'],
            'email'          => ['required', 'email', 'unique:users'],
            'password'       => ['required', 'confirmed'],
            'avatar'         => ['nullable', 'image', 'max:2048'],
        ]);

        $destination = Destination::findOrFail($request->destination_id);

        $fields['name'] = $destination->name;
        $fields['password'] = Hash::make($fields['password']);

        if ($request->hasFile('avatar')) {
            $fields['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($fields);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('greet', 'Welcome to DTN32!');
    }

    /* =======================
     |  LOGIN / LOGOUT
     ======================= */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->role) {
                'admin'    => redirect()->route('Annuaire.index'),
                'standard' => redirect()->route('standard.index'),
                default    => redirect()->route('dashboard'),
            };
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

        return redirect()->route('login');
    }

    /* =======================
     |  PROFILE
     ======================= */
    public function showProfile()
    {
        return Inertia::render('Profile', [
            'user' => auth()->user()->append('avatar_url'),
        ]);
    }
public function changeEmail(Request $request)
{
    $request->validate([
        'email' => ['required', 'email', 'unique:users,email'],
        'current_password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    $user->update([
        'email' => $request->email,
        'email_verified_at' => null, // optional but recommended
    ]);

    return back()->with('success', 'Email updated successfully!');
}

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    public function changeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update([
            'avatar' => $path,
        ]);

        return back()->with('success', 'Avatar updated successfully.');
    }
}
