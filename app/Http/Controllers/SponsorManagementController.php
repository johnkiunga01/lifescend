<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SponsorManagementController extends Controller
{
    protected function uploadImage(\Illuminate\Http\UploadedFile $file): string
    {
        return $file->store('uploads/sponsors', 'public');
    }

    public function index()
    {
        $sponsors = Sponsor::query()->latest()->get();
        return view('manage.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('manage.sponsors.create');
    }

    public function edit(int $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return view('manage.sponsors.edit', compact('sponsor'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(4),
            'website' => $data['website'] ?? null,
            'description' => $data['description'] ?? null,
            'level' => $data['level'],
        ];

        if ($request->hasFile('image')) {
            $attrs['image'] = $this->uploadImage($request->file('image'));
        }

        Sponsor::create($attrs);

        return redirect()->route('manage.sponsors.index')->with('success', 'Sponsor created.');
    }

    public function update(Request $request, int $id)
    {
        $sponsor = Sponsor::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(4),
            'website' => $data['website'] ?? null,
            'description' => $data['description'] ?? null,
            'level' => $data['level'],
        ];

        if ($request->hasFile('image')) {
            $attrs['image'] = $this->uploadImage($request->file('image'));
        }

        $sponsor->update($attrs);

        return redirect()->route('manage.sponsors.index')->with('success', 'Sponsor updated.');
    }

    public function destroy(int $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->delete();
        return redirect()->route('manage.sponsors.index')->with('success', 'Sponsor deleted.');
    }
}
