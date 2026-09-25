<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventTypeManagementController extends Controller
{
    protected function uploadImage($file)
    {
        return $file->store('uploads/event-types', 'public');
    }

    public function index()
    {
        $types = EventType::withCount('events')->orderBy('is_system', 'desc')->orderBy('name')->get();
        return view('manage.event-types.index', compact('types'));
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('manage.event-types.index')->withErrors(['error' => 'Select event types to update.']);
        }

        $selected = EventType::whereIn('id', $ids)->where('is_system', false)->get();

        if ($selected->isEmpty()) {
            return redirect()->route('manage.event-types.index')->withErrors(['error' => 'No selectable event types found.']);
        }

        if ($action === 'transfer_delete') {
            $targetId = $request->input('transfer_to');
            if (!$targetId) {
                return redirect()->route('manage.event-types.index')->withErrors(['error' => 'Select a target event type to transfer events to.']);
            }
            $target = EventType::findOrFail($targetId);
            $transferCount = 0;
            $deleteCount = 0;
            foreach ($selected as $type) {
                $eventsMoved = \App\Models\Event::where('event_type', $type->slug)->update(['event_type' => $target->slug]);
                $transferCount += $eventsMoved;
                $type->delete();
                $deleteCount++;
            }
            return redirect()->route('manage.event-types.index')->with('success', "$transferCount event(s) transferred to \"{$target->name}\". $deleteCount event type(s) deleted.");
        }

        if ($action === 'delete') {
            $blocked = [];
            $deleted = 0;
            foreach ($selected as $type) {
                if ($type->events_count > 0) {
                    $blocked[] = $type->name;
                } else {
                    $type->delete();
                    $deleted++;
                }
            }
            $msg = "$deleted event type(s) deleted.";
            if (!empty($blocked)) {
                $msg .= ' Skipped (have events): ' . implode(', ', $blocked) . '. Transfer or delete their events first.';
            }
            return redirect()->route('manage.event-types.index')->with('success', $msg);
        }

        return redirect()->route('manage.event-types.index')->withErrors(['error' => 'Invalid action.']);
    }

    public function create()
    {
        return view('manage.event-types.create');
    }

    public function edit($id)
    {
        $type = EventType::findOrFail($id);

        if ($type->is_system) {
            return redirect()->route('manage.event-types.index')->withErrors(['error' => 'System event types cannot be edited.']);
        }

        return view('manage.event-types.edit', compact('type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name',
            'mode' => 'required|in:inhouse,online,hybrid',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'mode' => $data['mode'] ?? 'inhouse',
        ];

        if ($request->hasFile('image')) {
            $attrs['image'] = $this->uploadImage($request->file('image'));
        }

        EventType::create($attrs);

        return redirect()->route('manage.event-types.index')->with('success', 'Event type created.');
    }

    public function update(Request $request, $id)
    {
        $type = EventType::findOrFail($id);

        if ($type->is_system) {
            return redirect()->route('manage.event-types.index')->withErrors(['error' => 'System event types cannot be edited.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name,' . $type->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $attrs = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'mode' => $data['mode'] ?? $type->mode,
        ];

        if ($request->hasFile('image')) {
            $attrs['image'] = $this->uploadImage($request->file('image'));
        }

        $type->update($attrs);

        return redirect()->route('manage.event-types.index')->with('success', 'Event type updated.');
    }

    public function destroy($id)
    {
        $type = EventType::withCount('events')->findOrFail($id);

        if ($type->is_system) {
            return redirect()->route('manage.event-types.index')->withErrors(['error' => 'System event types cannot be deleted.']);
        }

        if ($type->events_count > 0) {
            return back()->withErrors(['error' => 'Cannot delete event type linked to events.']);
        }

        $type->delete();

        return redirect()->route('manage.event-types.index')->with('success', 'Event type deleted.');
    }
}
