<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueManagementController extends Controller
{
    protected function uploadImage($file)
    {
        return $file->store('uploads/venues', 'public');
    }

    public function index()
    {
        $venues = Venue::withCount('events')->latest()->get();
        return view('manage.venues.index', compact('venues'));
    }

    public function create()
    {
        return view('manage.venues.create');
    }

    public function edit($id)
    {
        $venue = Venue::findOrFail($id);
        return view('manage.venues.edit', compact('venue'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $venue = Venue::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'venue' => [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'city' => $venue->city,
                ],
                'message' => 'Venue created successfully.',
            ]);
        }

        return redirect()->route('manage.venues.index')->with('success', 'Venue created.');
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $venue->update($data);

        return redirect()->route('manage.venues.index')->with('success', 'Venue updated.');
    }

    public function destroy($id)
    {
        $venue = Venue::withCount('events')->findOrFail($id);

        if ($venue->events_count > 0) {
            return back()->withErrors(['error' => 'Cannot delete venue with linked events.']);
        }

        $venue->delete();

        return redirect()->route('manage.venues.index')->with('success', 'Venue deleted.');
    }
}
