<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $org = Organization::firstOrFail();
        return view('manage.settings.index', [
            'org' => $org,
            'settings' => $org->settings ?? [],
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(Request $request)
    {
        $org = Organization::firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'website' => 'sometimes|nullable|url|max:255',
            'address' => 'sometimes|nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,webp,ico|max:2048',
            'cover_image_width' => 'sometimes|required|integer|min:1600|max:1600',
            'cover_image_height' => 'sometimes|required|integer|min:900|max:900',
            'timezone' => 'sometimes|nullable|timezone',
            'terms_and_conditions' => 'sometimes|nullable|string',
            'privacy_policy' => 'sometimes|nullable|string',
            'notification_email' => 'sometimes|nullable|email|max:255',
        ]);

        if ($request->has('name')) {
            $org->name = $data['name'];
        }
        if ($request->has('email')) {
            $org->email = $data['email'];
        }
        if ($request->has('phone')) {
            $org->phone = $data['phone'];
        }
        if ($request->has('website')) {
            $org->website = $data['website'];
        }
        if ($request->has('address')) {
            $org->address = $data['address'];
        }

        $file = $request->file('logo');
        if ($file && $file->isValid() && $file->getPathname() && file_exists($file->getPathname())) {
            if ($org->logo && Storage::disk('public')->exists($org->logo)) {
                Storage::disk('public')->delete($org->logo);
            }
            $path = 'org/' . md5(uniqid()) . '.jpg';
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $org->logo = $path;
        }

        $file = $request->file('favicon');
        if ($file && $file->isValid() && $file->getPathname() && file_exists($file->getPathname())) {
            if ($org->favicon && Storage::disk('public')->exists($org->favicon)) {
                Storage::disk('public')->delete($org->favicon);
            }
            $path = 'org/' . md5(uniqid()) . '-favicon.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $org->favicon = $path;
        }

        $settings = $org->settings ?? [];
        if ($request->has('cover_image_width')) {
            $settings['cover_image_width'] = (int) $data['cover_image_width'];
        }
        if ($request->has('cover_image_height')) {
            $settings['cover_image_height'] = (int) $data['cover_image_height'];
        }
        if ($request->has('timezone') && $data['timezone']) {
            $settings['timezone'] = $data['timezone'];
        }

        if ($request->has('terms_and_conditions') && $request->input('terms_and_conditions') !== ($settings['terms_and_conditions'] ?? null)) {
            $settings['terms_version'] = $this->bumpVersion($settings['terms_version'] ?? null);
            $settings['terms_and_conditions'] = $request->input('terms_and_conditions') ?? '';
        }

        if ($request->has('privacy_policy') && $request->input('privacy_policy') !== ($settings['privacy_policy'] ?? null)) {
            $settings['privacy_version'] = $this->bumpVersion($settings['privacy_version'] ?? null);
            $settings['privacy_policy'] = $request->input('privacy_policy') ?? '';
        }

        if ($request->has('notification_email')) {
            $settings['notification_email'] = $request->input('notification_email');
        }

        $org->settings = $settings;
        $org->save();

        return redirect()->route('manage.settings.index')->with('success', 'Settings updated.');
    }

    private function bumpVersion(?string $currentVersion): string
    {
        if (!$currentVersion) {
            return '0.0.1001';
        }

        $parts = explode('.', $currentVersion);
        $last = (int) end($parts);
        $last++;
        $parts[count($parts) - 1] = $last;

        return implode('.', $parts);
    }
}
