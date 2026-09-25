<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\TrainingRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Models\EventType;
use App\Models\Sponsor;
use App\Models\Speaker;
use App\Models\ActivityLog;
use App\Models\User;
use App\Mail\AdminNotificationMail;
use App\Mail\TrainingRequestAcceptedMail;
use App\Notifications\OperationNotification;
use Illuminate\Support\Str;

class TrainingRequestController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($role === 'customer') {
            $trainingRequests = TrainingRequest::with('user', 'event')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            $trainingRequests = TrainingRequest::with('user', 'event')
                ->latest()
                ->get();
        }

        return view('training.index', compact('trainingRequests'));
    }

    public function create()
    {
        $events = \App\Models\Event::where('status', '=', 'published', 'and')->orderBy('name', 'asc')->get();
        return view('training.create', compact('events'));
    }

    public function edit(string $slug)
    {
        $trainingRequest = TrainingRequest::where('slug', '=', $slug, 'and')
            ->orWhere('id', $slug)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($role === 'customer' && $trainingRequest->user_id !== $user->id) {
            abort(403);
        }

        if ($trainingRequest->status !== 'pending') {
            return redirect()->route('training.show', $trainingRequest->slug)
                ->withErrors(['error' => 'Only pending requests can be edited.']);
        }

        $events = Event::where('status', '=', 'published', 'and')->get();
        return view('training.edit', compact('trainingRequest', 'events'));
    }

    public function update(Request $request, string $slug)
    {
        $trainingRequest = TrainingRequest::where('slug', '=', $slug, 'and')
            ->orWhere('id', $slug)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($role === 'customer' && $trainingRequest->user_id !== $user->id) {
            abort(403);
        }

        if ($trainingRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be edited.']);
        }

        if ($request->filled('website')) {
            return redirect()->route('training.show', $trainingRequest->slug);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'training_type' => 'required|in:online,inhouse',
            'estimated_attendees' => 'nullable|integer|min:1|max:10000',
            'preferred_date' => 'required|date|after:today',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $data['title'] = strip_tags(trim($data['title']));
        $data['description'] = strip_tags(trim($data['description']));

        $trainingRequest->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_id' => $trainingRequest->event_id,
            'action' => 'training_updated',
            'model_type' => TrainingRequest::class,
            'model_id' => $trainingRequest->id,
            'metadata' => [
                'title' => $trainingRequest->title,
                'training_type' => $data['training_type'],
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('training.show', $trainingRequest->slug)
            ->with('success', 'Training request updated successfully.');
    }

    public function show(string $slug)
    {
        $trainingRequest = TrainingRequest::with('user', 'event')
            ->where('slug', '=', $slug, 'and')
            ->orWhere('id', $slug)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($role === 'customer' && $trainingRequest->user_id !== $user->id) {
            abort(403);
        }

        $venues = Venue::orderBy('name', 'asc')->get();
        $eventTypes = EventType::orderBy('name', 'asc')->get();
        $sponsors = \App\Models\Sponsor::orderBy('name', 'asc')->get();
        $speakers = \App\Models\Speaker::orderBy('name', 'asc')->get();

        return view('training.show', compact('trainingRequest', 'venues', 'eventTypes', 'sponsors', 'speakers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'message' => 'required|string|max:2000',
            'training_type' => 'required|in:online,inhouse',
            'estimated_attendees' => 'nullable|integer|min:1|max:10000',
            'preferred_date' => 'required|date|after:today',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $data['title'] = strip_tags(trim($request->title));
        $data['description'] = strip_tags(trim($request->description . "\n\n---\n" . $request->message));
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        $data['training_type'] = $request->training_type;
        $data['preferred_date'] = $request->preferred_date;
        $data['estimated_attendees'] = $request->estimated_attendees;
        $data['event_id'] = $request->event_id;
        $data['contact_name'] = strip_tags(trim($request->contact_name));
        $data['contact_email'] = strip_tags(trim($request->contact_email));
        $data['contact_phone'] = $request->contact_phone ? strip_tags(trim($request->contact_phone)) : null;

        $trainingRequest = TrainingRequest::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'training_requested',
            'model_type' => TrainingRequest::class,
            'model_id' => $trainingRequest->id,
            'metadata' => [
                'title' => $trainingRequest->title,
                'training_type' => $data['training_type'],
            ],
            'ip_address' => $request->ip(),
        ]);

        try {
            $notifyEmail = \App\Models\Organization::firstOrFail()->settings['notification_email'] ?? null;
            if ($notifyEmail) {
                Mail::to($notifyEmail)->send(new AdminNotificationMail(
                    subjectText: 'New Training Request - ' . $trainingRequest->title,
                    body: "Title: {$trainingRequest->title}\nType: {$trainingRequest->training_type}\nPreferred Date: {$trainingRequest->preferred_date->format('M d, Y')}\nContact: {$trainingRequest->contact_name} ({$trainingRequest->contact_email})\nEstimated Attendees: " . ($trainingRequest->estimated_attendees ?? 'N/A') . "\n\nDescription:\n{$trainingRequest->description}",
                    actionUrl: route('training.show', $trainingRequest->slug),
                    actionText: 'View Request',
                ));
            }
        } catch (\Exception $e) {}

        return redirect()->route('dashboard')->with('success', 'Training request submitted successfully!');
    }

    public function accept(Request $request, string $slug)
    {
        $trainingRequest = TrainingRequest::with('user')
            ->where('slug', '=', $slug, 'and')
            ->orWhere('id', $slug)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if (!in_array($role, ['admin', 'manager', 'staff'])) {
            abort(403);
        }

        if ($trainingRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'This request has already been ' . $trainingRequest->status . '.']);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $data['message'] = strip_tags(trim($data['message']));

        $trainingRequest->update(['status' => 'approved']);

        try {
            Mail::to($trainingRequest->user->email)->send(
                new TrainingRequestAcceptedMail($trainingRequest, $data['message'])
            );
        } catch (\Exception $e) {
            // Email failed but request is still accepted
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'event_id' => $trainingRequest->event_id,
            'action' => 'training_request_accepted',
            'model_type' => TrainingRequest::class,
            'model_id' => $trainingRequest->id,
            'metadata' => [
                'title' => $trainingRequest->title,
            ],
            'ip_address' => $request->ip(),
        ]);

        Notification::send(
            User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
            new OperationNotification('training_approved', 'Training Approved', "{$trainingRequest->title} was approved", route('training.show', $trainingRequest->slug))
        );

        return redirect()->route('training.show', $trainingRequest->slug)
            ->with('success', 'Training request approved and notification sent.');
    }

    public function schedule(Request $request, string $slug)
    {
        $trainingRequest = TrainingRequest::with('user')
            ->where('slug', '=', $slug, 'and')
            ->orWhere('id', $slug)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $role = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if (!in_array($role, ['admin', 'manager', 'staff'])) {
            abort(403);
        }

        if ($trainingRequest->status !== 'approved') {
            return back()->withErrors(['error' => 'This request must be approved before scheduling.']);
        }

        if ($trainingRequest->event_id) {
            return back()->withErrors(['error' => 'This request has already been scheduled.']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'timezone' => 'nullable|string|max:50',
            'venue_type' => 'required|in:online,inhouse',
            'venue_id' => 'nullable|exists:venues,id',
            'meeting_place' => 'required_if:venue_type,inhouse|nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:2048',
            'event_type' => 'required|string|max:100',
            'visibility' => 'required|in:public,private',
            'capacity' => 'nullable|integer|min:1',
            'payment_type' => 'required|in:stripe',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'pricing' => 'nullable|array',
            'pricing.*.name' => 'nullable|string|max:255',
            'pricing.*.description' => 'nullable|string|max:1000',
            'pricing.*.price' => 'nullable|numeric|min:0',
            'pricing.*.discount_price' => 'nullable|numeric|min:0',
            'pricing.*.quantity' => 'nullable|integer|min:1',
            'pricing.*.highlights' => 'nullable|string|max:500',
            'topics' => 'nullable|array',
            'topics.*.name' => 'nullable|string|max:255',
            'topics.*.description' => 'nullable|string|max:1000',
            'keynotes' => 'nullable|array',
            'keynotes.*.speaker_id' => 'nullable|exists:speakers,id',
            'keynotes.*.speak_time' => 'nullable|string|max:20',
            'sponsor_ids' => 'nullable|array',
            'sponsor_ids.*' => 'exists:sponsors,id',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event = Event::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(4),
            'description' => $request->input('description', $trainingRequest->description),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'timezone' => $data['timezone'] ?? 'UTC',
            'venue_id' => $data['venue_id'] ?? null,
            'meeting_place' => $data['meeting_place'] ?? null,
            'platform' => $data['platform'] ?? null,
            'meeting_link' => $data['meeting_link'] ?? null,
            'event_type' => $data['event_type'],
            'payment_type' => 'stripe',
            'capacity' => $data['capacity'] ?? $trainingRequest->estimated_attendees,
            'cover_image' => $coverPath,
            'status' => $data['visibility'] === 'public' ? 'published' : 'draft',
            'published_at' => $data['visibility'] === 'public' ? now() : null,
            'created_by' => $user->id,
        ]);

        $trainingRequest->update([
            'event_id' => $event->id,
            'visibility' => $data['visibility'],
        ]);

        if ($request->has('sponsor_ids')) {
            \App\Models\Sponsor::where('event_id', '=', $event->id, 'and')->whereNotIn('id', $request->sponsor_ids)->update(['event_id' => null]);
            \App\Models\Sponsor::whereIn('id', $request->sponsor_ids, 'and', false)->update(['event_id' => $event->id]);
        }

        if ($request->has('keynotes')) {
            foreach ($request->keynotes as $kn) {
                if (!empty($kn['speaker_id'])) {
                    $event->speakers()->attach((int) $kn['speaker_id'], [
                        'featured' => true,
                        'speak_time' => $kn['speak_time'] ?? null,
                    ]);
                }
            }
        }

        if ($request->has('topics')) {
            $order = 0;
            foreach ($request->topics as $topic) {
                if (!empty($topic['name'])) {
                    $event->topics()->create([
                        'name' => $topic['name'],
                        'description' => $topic['description'] ?? '',
                        'sort_order' => $order++,
                    ]);
                }
            }
        }

        if ($request->has('pricing')) {
            $order = 0;
            foreach ($request->pricing as $pkg) {
                if (!empty($pkg['name'])) {
                    $event->ticketTypes()->create([
                        'name' => $pkg['name'],
                        'price' => $pkg['price'] ?? 0,
                        'discount_price' => !empty($pkg['discount_price']) ? $pkg['discount_price'] : null,
                        'description' => $pkg['description'] ?? '',
                        'highlights' => !empty($pkg['highlights']) ? array_map('trim', explode(',', $pkg['highlights'])) : null,
                        'quantity' => $pkg['quantity'] ?? 100,
                        'currency' => 'USD',
                        'is_active' => true,
                        'is_paid' => true,
                    ]);
                }
            }
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'action' => 'training_request_scheduled',
            'model_type' => TrainingRequest::class,
            'model_id' => $trainingRequest->id,
            'metadata' => [
                'title' => $trainingRequest->title,
                'visibility' => $data['visibility'],
                'event_name' => $event->name,
            ],
            'ip_address' => $request->ip(),
        ]);

        Notification::send(
            User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
            new OperationNotification('training_scheduled', 'Training Scheduled', "{$trainingRequest->title} was scheduled as {$event->name}", route('training.show', $trainingRequest->slug))
        );

        return redirect()->route('training.show', $trainingRequest->slug)
            ->with('success', 'Training has been scheduled successfully.');
    }
}
