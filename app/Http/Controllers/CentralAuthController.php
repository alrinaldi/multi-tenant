<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

/**
 * Class CentralAuthController
 * Handles authentication for Tenant Owners within the Central Database context.
 */
class CentralAuthController extends Controller
{
    /**
     * Display the registration view for new Tenant Owners.
     * @return \Inertia\Response
     */
    public function showRegister() {
        return Inertia::render('Central/Auth/Register');
    }

    /**
     * Display the login view for existing platform administrators/owners.
     * @return \Inertia\Response
     */
    public function showLogin() {
        return Inertia::render('Central/Auth/Login');
    }

    /**
     * Handle an incoming registration request for a new platform user.
     * Encrypts sensitive data and establishes an initial authenticated session.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request) {
        // Enforce strict validation rules for account integrity and password strength.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Securely persist the user to the Central Database.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // One-way hashing for credential security.
        ]);

        Auth::login($user);

        return redirect()->route('central.dashboard');
    }

    /**
     * Authenticate the user and regenerate the session to prevent Session Fixation attacks.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Security: Prevent session hijacking by refreshing the session ID upon login.
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Salah email atau password cuk.']);
    }

    /**
     * Destroy the authenticated session and clear security tokens.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        Auth::logout();

        // Flush all session data and regenerate the CSRF token for the next guest session.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}