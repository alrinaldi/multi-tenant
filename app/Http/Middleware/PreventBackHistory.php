<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PreventBackHistory
 * * Middleware to enforce strict browser cache control policies.
 * This is a critical security measure to prevent sensitive data from being cached 
 * and accessed via the browser's back button after a user has logged out.
 */
class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * * This method intercepts the outgoing response and injects specific HTTP headers 
     * to instruct the client's browser not to store local copies of the page content.
     * * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /**
         * Inject Cache-Control headers to ensure session isolation and privacy.
         * - 'no-cache, no-store': Prevents the browser from caching any part of the response.
         * - 'max-age=0': Forces immediate expiration of the cached content.
         * - 'Pragma/Expires': Legacy support for older HTTP/1.0 clients.
         */
        return $response->withHeaders([
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Sun, 02 Jan 1990 00:00:00 GMT',
        ]);
    }
}