<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Class TenantRegisterController
 * Handles the provisioning of new tenant instances, including database creation,
 * domain mapping, and administrative account synchronization.
 */
class TenantRegisterController extends Controller
{
    /**
     * Display the Central Dashboard for the authenticated Owner.
     * Fetches all registered tenants and their associated domains.
     * * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Central/Dashboard', [
            'user' => Auth::user(),
            // Eager load domains to prevent N+1 query issues during list rendering.
            'tenants' => Tenant::with('domains')->latest()->get()
        ]);
    }

    /**
     * Store and Provision a new Tenant.
     * This method triggers the automated tenant creation lifecycle:
     * 1. Database Creation | 2. Domain Mapping | 3. Admin Account Cloning
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // Strict validation to ensure subdomain uniqueness across the central platform.
        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'subdomain' => 'required|alpha_num|unique:tenants,id', 
        ]);

        // Double-check protection to verify ID availability before proceeding with creation.
        if (Tenant::find($data['subdomain'])) {
            throw ValidationException::withMessages([
                'subdomain' => 'This subdomain is already registered.',
            ]);
        }

        /**
         * 1. Tenant Creation
         * This triggers the tenancy package's automated database creation workflow.
         * The 'id' is mapped to the subdomain for easier identification.
         */
        $tenant = Tenant::create([
            'id' => $data['subdomain'], 
            'store_name' => $data['store_name'],
        ]);

        /**
         * 2. Domain Mapping
         * Associates the tenant with a specific subdomain string (e.g., fashion.localhost).
         */
        $tenant->domains()->create([
            'domain' => $data['subdomain'] . '.' . config('tenancy.central_domains')[0], 
        ]);

        /**
         * 3. Magic Cloning: Admin Account Provisioning
         * Executes a closure within the newly created tenant's database context.
         * Copies the Central Owner's credentials and assigns the 'admin' role locally.
         */
        $owner = Auth::user();

        $tenant->run(function () use ($owner) {
            User::create([
                'name' => $owner->name,
                'email' => $owner->email,
                'password' => $owner->password, // Password remains hashed from the Central DB.
                'role' => 'admin', // Grants full management access within this specific tenant.
            ]);
        });

        // 4. Redirect Logic
        // Construct the full URL for the new tenant instance.
        $protocol = request()->secure() ? 'https://' : 'http://';
        $domain = $data['subdomain'] . '.' . config('tenancy.central_domains')[0];
        $url = $protocol . $domain . ':8000'; 

        /**
         * Returns a redirect to the dashboard with the target URL flashed to the session.
         * Useful for handling cross-domain redirection in Inertia.js.
         */
        return redirect()->route('central.dashboard')->with('target_url', $url);
    }
}