<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function show()
    {
        $org = Organization::firstOrFail();
        $settings = $org->settings ?? [];
        $user = auth()->user();

        $termsContent = $settings['terms_and_conditions'] ?? '';
        $privacyContent = $settings['privacy_policy'] ?? '';
        $termsVersion = $settings['terms_version'] ?? null;
        $privacyVersion = $settings['privacy_version'] ?? null;

        $needsTerms = $termsVersion && $user->accepted_terms_version !== $termsVersion;
        $needsPrivacy = $privacyVersion && $user->accepted_privacy_version !== $privacyVersion;

        if (!$needsTerms && !$needsPrivacy) {
            return redirect()->intended(route('dashboard'));
        }

        return view('consent', compact(
            'termsContent', 'privacyContent',
            'termsVersion', 'privacyVersion',
            'needsTerms', 'needsPrivacy'
        ));
    }

    public function store(Request $request)
    {
        $org = Organization::firstOrFail();
        $settings = $org->settings ?? [];
        $user = auth()->user();

        $termsVersion = $settings['terms_version'] ?? null;
        $privacyVersion = $settings['privacy_version'] ?? null;

        $needsTerms = $termsVersion && $user->accepted_terms_version !== $termsVersion;
        $needsPrivacy = $privacyVersion && $user->accepted_privacy_version !== $privacyVersion;

        $acceptedTerms = false;
        $acceptedPrivacy = false;

        if ($needsTerms) {
            $request->validate(['accept_terms' => 'accepted']);
            $acceptedTerms = true;
        }

        if ($needsPrivacy) {
            $request->validate(['accept_privacy' => 'accepted']);
            $acceptedPrivacy = true;
        }

        if ($acceptedTerms) {
            $user->accepted_terms_version = $termsVersion;
        }
        if ($acceptedPrivacy) {
            $user->accepted_privacy_version = $privacyVersion;
        }
        $user->save();

        return redirect()->intended(route('dashboard'))->with('success', 'Thank you for accepting the updated policies.');
    }
}
