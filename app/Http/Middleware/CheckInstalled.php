<?php

namespace App\Http\Middleware;

use App\Services\InstallationStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = InstallationStatus::isInstalled();

        // Allow health check and webhook through regardless
        if ($request->is('up') || $request->is('stripe/webhook')) {
            return $next($request);
        }

        if (!$installed && !$request->routeIs('setup*')) {
            return redirect()->route('setup');
        }

        if ($installed && $request->routeIs('setup*') && !$request->routeIs('setup.success')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
