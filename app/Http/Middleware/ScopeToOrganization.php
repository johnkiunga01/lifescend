<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Organization;

class ScopeToOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For a single-organization model, resolve the first organization.
        // Wrapped in try/catch so pre-migration setup requests don't fatal.
        try {
            $organization = Organization::first();
        } catch (\Throwable) {
            $organization = null;
        }

        if ($organization) {
            app()->instance(Organization::class, $organization);
        }

        // Always share — even null — so views never get "Undefined variable $organization"
        view()->share('organization', $organization);

        return $next($request);
    }
}
