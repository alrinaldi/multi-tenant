<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

/**
 * Class TenantAuthController
 * Manages authentication for Customers and Staff within a specific Tenant's isolated database.
 */
class TenantAuthController extends Controller {

    /**
     * Display the login interface for the specific tenant store.
     * @return \Inertia\Response
     */
    public function showLogin() { 
        return Inertia::render('Tenant/Auth/Login'); 
    }

    /**
     * Display the customer registration interface.
     * @return \Inertia\Response
     */
    public function showRegister() { 
        return Inertia::render('Tenant/Auth/Register'); 
    }

    /**
     * Handle customer registration.
     * Automatically assigns the 'customer' role to ensure restricted access to store management.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request) {
        // Enforce data integrity and unique email constraints within this specific tenant database.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);
        
        // Persist the user with a default 'customer' role for security partitioning.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer' 
        ]);
        
        Auth::login($user);

        return redirect()->route('tenant.home');
    }

    /**
     * Authenticate tenant users and regenerate the session to mitigate session hijacking.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            // Security: Regenerates the session ID to prevent Session Fixation attacks.
            $request->session()->regenerate();
            
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Gagal login cuk.']);
    }

    /**
     * Terminate the user session and invalidate security tokens.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request) {
        Auth::logout(); 

        // Flush session data and reset the session state for the next visitor.
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();

        return redirect('/');
    }
}