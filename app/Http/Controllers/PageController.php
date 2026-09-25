<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ContactMessage;
use App\Models\Organization;
use App\Models\Testimonial;
use App\Mail\AdminNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function home()
    {
        $events = Event::with('venue')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('start_date')
            ->take(6)
            ->get();

        $testimonials = Testimonial::whereIsApproved(true)
            ->with('user')
            ->latest()
            ->get();

        return view('welcome', compact('events', 'testimonials'));
    }

    public function events(Request $request)
    {
        $query = Event::with('venue')
            ->where('status', 'published')
            ->whereNotNull('published_at');

        if ($request->filled('type')) {
            $query->where('event_type', $request->type);
        }

        $status = $request->query('status', 'active');
        if ($status === 'active') {
            $query->where('end_date', '>=', now());
        } elseif ($status === 'past') {
            $query->where('end_date', '<', now());
        }

        $sort = $request->query('sort', 'date_asc');
        match ($sort) {
            'date_desc' => $query->latest('start_date'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->oldest('start_date'),
        };

        $events = $query->get();
        $eventTypes = \App\Models\EventType::orderBy('name', 'asc')->get();
        $viewMode = in_array($request->query('view'), ['grid', 'list']) ? $request->query('view') : 'grid';

        return view('events.index', compact('events', 'eventTypes', 'viewMode'));
    }

    public function about()
    {
        return view('about');
    }

    public function partners()
    {
        $partners = \App\Models\Partner::whereIsActive(true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partners', compact('partners'));
    }

    public function terms()
    {
        $org = Organization::firstOrFail();
        $content = $org->settings['terms_and_conditions'] ?? config('legal.default_terms');
        return view('terms', compact('content'));
    }

    public function privacy()
    {
        $org = Organization::firstOrFail();
        $content = $org->settings['privacy_policy'] ?? config('legal.default_privacy');
        return view('privacy', compact('content'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $data['name'] = strip_tags(trim($data['name']));
        $data['subject'] = strip_tags(trim($data['subject']));
        $data['message'] = strip_tags(trim($data['message']));
        $data['email'] = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);

        ContactMessage::create($data);

        try {
            $notifyEmail = Organization::firstOrFail()->settings['notification_email'] ?? null;
            if ($notifyEmail) {
                Mail::to($notifyEmail)->send(new AdminNotificationMail(
                    subjectText: 'New Contact Message - ' . $data['subject'],
                    body: "Name: {$data['name']}\nEmail: {$data['email']}\nSubject: {$data['subject']}\n\nMessage:\n{$data['message']}",
                    actionUrl: route('manage.contact-messages.index'),
                    actionText: 'View Messages',
                ));
            }
        } catch (\Exception $e) {}

        return redirect()->route('contact')->with('success', 'Your message has been sent. We will get back to you soon.');
    }
}
