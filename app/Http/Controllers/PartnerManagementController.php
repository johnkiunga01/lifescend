<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerManagementController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('created_at', 'desc')->get();
        return view('manage.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('manage.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:partners,slug',
            'story' => 'nullable|string',
            'website' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . \Illuminate\Support\Str::random(4);
        }

        $file = $request->file('image');
        if ($file && $file->isValid() && $file->getPathname() && file_exists($file->getPathname())) {
            $path = 'partners/' . md5(uniqid()) . '.jpg';
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $validated['image'] = $path;
        }

        $partner = Partner::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'partner' => [
                    'id' => $partner->id,
                    'name' => $partner->name,
                ],
                'message' => 'Partner created successfully.',
            ]);
        }

        return redirect()->route('manage.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    public function edit(Partner $partner)
    {
        return view('manage.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:partners,slug,' . $partner->id,
            'story' => 'nullable|string',
            'website' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $file = $request->file('image');
        if ($file && $file->isValid() && $file->getPathname() && file_exists($file->getPathname())) {
            if ($partner->image) {
                Storage::disk('public')->delete($partner->image);
            }
            $path = 'partners/' . md5(uniqid()) . '.jpg';
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $validated['image'] = $path;
        }

        $partner->update($validated);

        return redirect()->route('manage.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->image) {
            Storage::disk('public')->delete($partner->image);
        }
        $partner->delete();

        return back()->with('success', 'Partner deleted successfully.');
    }
}