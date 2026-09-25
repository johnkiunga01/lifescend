<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\EventType;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\Partner;
use App\Models\ActivityLog;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\CheckIn;
use App\Models\CouponUsage;
use App\Models\User;
use App\Notifications\OperationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventManagementController extends Controller
{
    protected function uploadImage(UploadedFile $file, string $subdir = 'events'): string
    {
        $path = "uploads/$subdir/" . md5(uniqid()) . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
        return $path;
    }

    protected function uploadLogo(UploadedFile $file): string
    {
        return $this->uploadImage($file, 'events');
    }

    /**
     * Store an image selected from the Quill event-description editor.
     */
    public function uploadDescriptionImage(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $disk = Storage::disk('public');
        $directory = 'uploads/events/descriptions';

        try {
            if (!$disk->exists($directory) && !$disk->makeDirectory($directory)) {
                return response()->json([
                    'message' => 'Image storage could not be prepared. Please check storage permissions.',
                ], 500);
            }

            $path = $disk->putFile($directory, $data['image'], ['visibility' => 'public']);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Image could not be saved. Please check storage permissions.',
            ], 500);
        }

        if (!$path) {
            return response()->json([
                'message' => 'Image could not be saved. Please check storage permissions.',
            ], 500);
        }

        return response()->json([
            // A relative URL works on localhost (including custom ports) and production.
            'url' => '/storage/' . ltrim($path, '/'),
        ]);
    }

    public function index(Request $request)
    {
        $events = Event::with('venue', 'speakers')
            ->withCount([
                'tickets as tickets_sold_count',
                'attendees as attendees_count',
                'checkIns as checkins_count',
                'orders as orders_count' => fn($q) => $q->where('status', 'completed'),
            ])
            ->withSum('ticketTypes', 'sold_count')
            ->withSum('ticketTypes', 'quantity')
            ->latest()
            ->get();

        $events->each(function ($event) {
            $event->revenue = $event->orders()->where('status', 'completed')->sum('total_amount');
            $event->available = ($event->ticket_types_sum_quantity ?? 0) - ($event->ticket_types_sum_sold_count ?? 0);
        });

        $viewMode = in_array($request->query('view'), ['grid', 'list']) ? $request->query('view') : 'list';
        return view('manage.events.index', compact('events', 'viewMode'));
    }

    public function create()
    {
        $venues = Venue::orderBy('name', 'asc')->get();
        $eventTypes = EventType::orderBy('name', 'asc')->get();
        $speakers = Speaker::orderBy('name', 'asc')->get();
        $sponsors = \App\Models\Sponsor::orderBy('name', 'asc')->get();
        $partners = Partner::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('manage.events.create', compact('venues', 'eventTypes', 'speakers', 'sponsors', 'partners'));
    }

    public function show(string $slug)
    {
        $event = Event::with([
            'venue', 'ticketTypes', 'speakers', 'sponsors', 'topics',
            'attendees.user', 'attendees.ticket.ticketType', 'attendees.ticket.checkIns',
            'orders' => fn($q) => $q->with('user', 'tickets', 'items.ticketType')->latest(),
            'checkIns' => fn($q) => $q->with('user', 'ticket', 'checkedInBy')->latest()->take(50),
        ])->where('slug', $slug)->firstOrFail();

        $stats = [
            'total_tickets' => $event->tickets()->count(),
            'checked_in' => $event->checkIns()->count(),
            'pending' => $event->attendees()->where('status', 'registered')->count(),
            'revenue' => $event->orders()->where('status', 'completed')->sum('total_amount'),
        ];

        return view('manage.events.show', compact('event', 'stats'));
    }

    public function checkInAttendee(Request $request, string $slug)
    {
        $event = Event::whereSlug($slug)->firstOrFail();

        $data = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = Ticket::with('user')
            ->where('qr_code', $data['qr_code'])
            ->where('event_id', $event->id)
            ->first();

        if (!$ticket) {
            return back()->withErrors(['qr_code' => 'Ticket not found for this event.']);
        }

        if ($ticket->status === 'used' || $ticket->checked_in_at) {
            return back()->withErrors(['qr_code' => 'Ticket already used.']);
        }

        if ($ticket->status === 'cancelled') {
            return back()->withErrors(['qr_code' => 'Ticket cancelled.']);
        }

        $ticket->update(['checked_in_at' => now(), 'status' => 'used']);
        Attendee::whereTicketId($ticket->id)->update(['status' => 'checked-in']);

        $checkIn = CheckIn::create([
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'checked_in_by' => Auth::id(),
            'device_info' => ['user_agent' => $request->userAgent(), 'ip' => $request->ip()],
            'checked_in_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'action' => 'attendee_checked_in',
            'model_type' => CheckIn::class,
            'model_id' => $checkIn->id,
            'metadata' => ['ticket_code' => $ticket->qr_code, 'user_name' => $ticket->user->name],
            'ip_address' => $request->ip(),
        ]);

        Notification::send(
            User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
            new OperationNotification('attendee_checked_in', 'Check-In', "{$ticket->user->name} checked in for {$event->name}", route('manage.events.show', $event->slug), ['ticket_code' => $ticket->qr_code, 'user_name' => $ticket->user->name])
        );

        return redirect()->route('manage.events.show', $event->slug)
            ->with('success', "{$ticket->user->name} checked in successfully!");
    }

    public function edit(string $slug)
    {
        $event = Event::with(['speakers', 'sponsors', 'topics', 'ticketTypes' => fn ($query) => $query->where('is_active', true)])->where('slug', $slug)->firstOrFail();
        $venues = Venue::orderBy('name', 'asc')->get();
        $eventTypes = EventType::orderBy('name', 'asc')->get();
        $speakers = Speaker::orderBy('name', 'asc')->get();
        $sponsors = \App\Models\Sponsor::orderBy('name', 'asc')->get();
        $partners = Partner::where('is_active', true)->orderBy('name', 'asc')->get();
        $eventSpeakerIds = $event->speakers->pluck('id')->toArray();
        return view('manage.events.edit', compact('event', 'venues', 'eventTypes', 'speakers', 'sponsors', 'partners', 'eventSpeakerIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'timezone' => 'nullable|string|max:50',
            'venue_id' => 'nullable|exists:venues,id',
            'capacity' => 'nullable|integer|min:1',
            'payment_type' => 'required|in:stripe',
            'status' => 'required|in:draft,published,archived',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'pricing' => 'nullable|array',
            'pricing.*.name' => 'required|string|max:255',
            'pricing.*.price' => 'required|numeric|min:0',
            'pricing.*.price_eur' => 'required|numeric|min:0',
            'pricing.*.discount_price' => 'nullable|numeric|min:0',
            'pricing.*.discount_price_eur' => 'nullable|numeric|min:0',
            'pricing.*.quantity' => 'required|integer|min:1',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        unset($data['pricing']);
        $data['timezone'] = $data['timezone'] ?? 'UTC';
        $data['created_by'] = $request->user()->id;

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadLogo($request->file('logo'));
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->uploadImage($request->file('cover_image'), 'events/covers');
        }

        $event = Event::create($data);

        if ($request->has('speaker_ids')) {
            $event->speakers()->sync($request->speaker_ids);
        }

        if ($request->has('sponsor_ids')) {
            \App\Models\Sponsor::whereEventId($event->id)->whereNotIn('id', $request->sponsor_ids, 'and')->update(['event_id' => null]);
            \App\Models\Sponsor::whereIn('id', $request->sponsor_ids, 'and', false)->update(['event_id' => $event->id]);
        }

        if ($request->has('keynotes')) {
            $allSpeakerIds = $event->speakers()->pluck('speakers.id')->toArray();
            foreach ($request->keynotes as $kn) {
                if (!empty($kn['speaker_id'])) {
                    $sid = (int) $kn['speaker_id'];
                    if (in_array($sid, $allSpeakerIds)) {
                        $event->speakers()->updateExistingPivot($sid, ['featured' => true, 'speak_time' => $kn['speak_time'] ?? null]);
                    } else {
                        $event->speakers()->attach($sid, ['featured' => true, 'speak_time' => $kn['speak_time'] ?? null]);
                    }
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
                        'price_eur' => $pkg['price_eur'],
                        'discount_price' => !empty($pkg['discount_price']) ? $pkg['discount_price'] : null,
                        'discount_price_eur' => !empty($pkg['discount_price_eur']) ? $pkg['discount_price_eur'] : null,
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
            'user_id' => $request->user()->id,
            'action' => 'event_created',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'metadata' => ['name' => $event->name],
            'ip_address' => $request->ip(),
        ]);

        Notification::send(
            User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
            new OperationNotification('event_created', 'New Event', "{$event->name} was created", route('manage.events.show', $event->slug))
        );

        return redirect()->route('manage.events.index')->with('success', "Event '{$event->name}' created.");
    }

    public function update(Request $request, string $slug)
    {
        $event = Event::whereSlug($slug)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'timezone' => 'nullable|string|max:50',
            'venue_id' => 'nullable|exists:venues,id',
            'capacity' => 'nullable|integer|min:1',
            'payment_type' => 'required|in:stripe',
            'status' =>  'required|in:draft,published,archived',
            'logo' =>  'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_cover_image' => 'nullable|in:0,1,true,false,on,off,yes,no',
            'pricing' => 'nullable|array',
            'pricing.*.id' => ['nullable', 'integer', Rule::exists('ticket_types', 'id')->where('event_id', $event->id)],
            'pricing.*.name' => 'required|string|max:255',
            'pricing.*.price' => 'required|numeric|min:0',
            'pricing.*.price_eur' => 'nullable|numeric|min:0',
            'pricing.*.discount_price' => 'nullable|numeric|min:0',
            'pricing.*.discount_price_eur' => 'nullable|numeric|min:0',
            'pricing.*.quantity' => 'required|integer|min:1',
        ]);

        $data['timezone'] = $data['timezone'] ?? 'UTC';
        unset($data['pricing']);

        $paidCurrencies = [];
        foreach ($request->input('pricing', []) as $pkg) {
            if (empty($pkg['id']) && !isset($pkg['price_eur'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'pricing' => 'New ticket packages require an exact EUR fee.',
                ]);
            }
            if (($pkg['price'] ?? 0) > 0 || ($pkg['price_eur'] ?? 0) > 0) {
                $paidCurrencies[] = isset($pkg['price_eur']) ? 'EUR' : 'USD';
            }
        }
        if (count(array_unique($paidCurrencies)) > 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pricing' => 'Enter exact EUR fees for all paid packages before enabling EUR checkout for this event.',
            ]);
        }

        if ($data['status'] === 'published' && !$event->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadLogo($request->file('logo'));
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->uploadImage($request->file('cover_image'), 'events/covers');
        } elseif ($request->boolean('remove_cover_image')) {
            if ($event->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->cover_image);
            }
            $data['cover_image'] = null;
        }

        $event->update($data);

        if ($request->has('remove_speaker_ids')) {
            $event->speakers()->detach($request->remove_speaker_ids);
        }

        if ($request->has('sponsor_ids')) {
            \App\Models\Sponsor::whereEventId($event->id)->whereNotIn('id', $request->sponsor_ids, 'and')->update(['event_id' => null]);
            \App\Models\Sponsor::whereIn('id', $request->sponsor_ids, 'and', false)->update(['event_id' => $event->id]);
        }

        if ($request->has('keynotes')) {
            $allSpeakerIds = $event->speakers()->pluck('speakers.id')->toArray();
            $keynoteIds = [];

            foreach ($request->keynotes as $kn) {
                if (!empty($kn['speaker_id'])) {
                    $sid = (int) $kn['speaker_id'];
                    $keynoteIds[] = $sid;
                    if (in_array($sid, $allSpeakerIds)) {
                        $event->speakers()->updateExistingPivot($sid, ['featured' => true, 'speak_time' => $kn['speak_time'] ?? null]);
                    } else {
                        $event->speakers()->attach($sid, ['featured' => true, 'speak_time' => $kn['speak_time'] ?? null]);
                    }
                }
            }

            $featured = $event->speakers()->wherePivot('featured', true)->get();
            foreach ($featured as $s) {
                if (!in_array($s->id, $keynoteIds)) {
                    $event->speakers()->updateExistingPivot($s->id, ['featured' => false]);
                }
            }
        }

        if ($request->has('topics')) {
            $event->topics()->delete();
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
            $retainedIds = [];
            foreach ($request->pricing as $pkg) {
                if (!empty($pkg['name'])) {
                    $attributes = [
                        'name' => $pkg['name'],
                        'price' => $pkg['price'] ?? 0,
                        'price_eur' => $pkg['price_eur'] ?? null,
                        'discount_price' => !empty($pkg['discount_price']) ? $pkg['discount_price'] : null,
                        'discount_price_eur' => !empty($pkg['discount_price_eur']) ? $pkg['discount_price_eur'] : null,
                        'description' => $pkg['description'] ?? '',
                        'highlights' => !empty($pkg['highlights']) ? array_map('trim', explode(',', $pkg['highlights'])) : null,
                        'quantity' => $pkg['quantity'] ?? 100,
                        'currency' => 'USD',
                        'is_active' => true,
                        'is_paid' => true,
                    ];
                    if (!empty($pkg['id'])) {
                        $ticketType = $event->ticketTypes()->findOrFail($pkg['id']);
                        $ticketType->update($attributes);
                    } else {
                        $ticketType = $event->ticketTypes()->create($attributes);
                    }
                    $retainedIds[] = $ticketType->id;
                }
            }
            $event->ticketTypes()->whereNotIn('id', $retainedIds)->update(['is_active' => false]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'event_id' => $event->id,
            'action' => 'event_updated',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'metadata' => ['name' => $event->name],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manage.events.index')->with('success', "Event '{$event->name}' updated.");
    }

    public function destroy(Request $request, string $slug)
    {
        $event = Event::whereSlug($slug)->firstOrFail();
        $name = $event->name;
        $event->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'event_deleted',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'metadata' => ['name' => $name],
            'ip_address' => $request->ip(),
        ]);

        Notification::send(
            User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
            new OperationNotification('event_deleted', 'Event Deleted', "{$name} was deleted")
        );

        return redirect()->route('manage.events.index')->with('success', "Event '{$name}' deleted.");
    }
}
