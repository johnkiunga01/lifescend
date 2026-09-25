<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialManagementController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('user')
            ->latest()
            ->get();

        return view('manage.testimonials.index', compact('testimonials'));
    }

    public function approve(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => true]);

        return back()->with('success', 'Testimonial approved.');
    }

    public function reject(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => false]);

        return back()->with('success', 'Testimonial rejected.');
    }

    public function destroy(Testimonial $testimonial)
    {
        Testimonial::destroy($testimonial->getKey());

        return back()->with('success', 'Testimonial deleted.');
    }
}