<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Mail\AdminNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Organization;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::whereIsApproved(true)
            ->with('user')
            ->latest()
            ->get();

        if (Auth::check()) {
            $myTestimonial = Testimonial::whereUserId(Auth::id())->first();
            return view('customer.testimonials', compact('testimonials', 'myTestimonial'));
        }

        return view('testimonials', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $existing = Testimonial::whereUserId($request->user()->id)->first();
        if ($existing) {
            return back()->withErrors(['content' => 'You have already submitted a testimonial.']);
        }

        Testimonial::create([
            'user_id' => $request->user()->id,
            'content' => strip_tags(trim($validated['content'])),
            'rating' => $validated['rating'],
            'is_approved' => false,
        ]);

        try {
            $notifyEmail = Organization::firstOrFail()->settings['notification_email'] ?? null;
            if ($notifyEmail) {
                $user = $request->user();
                Mail::to($notifyEmail)->send(new AdminNotificationMail(
                    subjectText: 'New Testimonial Submitted',
                    body: "{$user->name} ({$user->email}) submitted a new testimonial.\n\nRating: {$validated['rating']}/5\n\nMessage:\n{$validated['content']}",
                    actionUrl: route('manage.testimonials.index'),
                    actionText: 'Review Testimonials',
                ));
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Thank you! Your testimonial has been submitted for review.');
    }
}