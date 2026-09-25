<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpeakerManagementController extends Controller
{
    protected function uploadPhoto(\Illuminate\Http\UploadedFile $file): string
    {
        return $file->store('uploads/speakers', 'public');
    }

    public function index()
    {
        $speakers = Speaker::withCount('events')->latest()->get();
        return view('manage.speakers.index', compact('speakers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(4),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
            'company' => $data['company'] ?? null,
            'job_title' => $data['job_title'] ?? null,
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $attrs['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $speaker = Speaker::create($attrs);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'speaker' => [
                    'id' => $speaker->id,
                    'name' => $speaker->name,
                    'company' => $speaker->company ?? '',
                ],
                'message' => 'Speaker created successfully.',
            ]);
        }

        return redirect()->route('manage.speakers.index')->with('success', 'Speaker created.');
    }

    public function update(Request $request, int $id)
    {
        $speaker = Speaker::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'email' => $data['email'] ?? $speaker->email,
            'phone' => $data['phone'] ?? $speaker->phone,
            'bio' => $data['bio'] ?? $speaker->bio,
            'company' => $data['company'] ?? $speaker->company,
            'job_title' => $data['job_title'] ?? $speaker->job_title,
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $attrs['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $speaker->update($attrs);

        return redirect()->route('manage.speakers.index')->with('success', 'Speaker updated.');
    }

    public function destroy(int $id)
    {
        $speaker = Speaker::findOrFail($id);
        $speaker->delete();

        return redirect()->route('manage.speakers.index')->with('success', 'Speaker deleted.');
    }
}
