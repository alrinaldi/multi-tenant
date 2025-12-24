<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CheckTenantAdmin
 * * Middleware to enforce Role-Based Access Control (RBAC) within the tenant context.
 * It restricts access to administrative routes, ensuring only authorized personnel 
 * can manage tenant-specific resources.
 */
class CheckTenantAdmin
{
    /**
     * Handle an incoming request.
     * * This gatekeeper verifies the user's authentication state and evaluates 
     * their assigned role before granting access to protected management routes.
     * * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** * Verify if the user is authenticated and possesses the 'admin' role.
         * This check is performed against the isolated tenant database.
         */
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Permission granted: Proceed to the next middleware or controller.
            return $next($request);
        }

        /**
         * Access Denied: Redirect unauthorized users back to the storefront.
         * Includes an error message to provide feedback on the failed authorization attempt.
         */
        return redirect('/')->withErrors(['email' => 'Access Denied: Administrative privileges required.']);
    }
}