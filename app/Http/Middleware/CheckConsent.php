<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckConsent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $org = Organization::first();
        if (!$org || !$org->settings) {
            return $next($request);
        }

        $settings = $org->settings;
        $currentTermsVersion = $settings['terms_version'] ?? null;
        $currentPrivacyVersion = $settings['privacy_version'] ?? null;

        $needsTermsConsent = $currentTermsVersion && $user->accepted_terms_version !== $currentTermsVersion;
        $needsPrivacyConsent = $currentPrivacyVersion && $user->accepted_privacy_version !== $currentPrivacyVersion;

        if ($needsTermsConsent || $needsPrivacyConsent) {
            $route = $request->route()?->getName();
            $consentRoutes = ['consent.show', 'consent.store', 'logout'];

            if (!in_array($route, $consentRoutes)) {
                return redirect()->route('consent.show');
            }
        }

        return $next($request);
    }
}
