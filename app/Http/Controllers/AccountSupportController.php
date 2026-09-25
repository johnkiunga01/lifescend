<?php

namespace App\Http\Controllers;

use App\Models\AccountSupportRequest;
use App\Mail\AdminNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AccountSupportController extends Controller
{
    public function index()
    {
        $requests = AccountSupportRequest::with('user')
            ->latest()
            ->paginate(20);
        return view('manage.account-support.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $existing = AccountSupportRequest::where('email', $data['email'])
            ->where('status', 'open')
            ->exists();

        if ($existing) {
            return redirect()->route('login')
                ->withErrors(['email' => 'You already have a pending account review request. An administrator will respond shortly.']);
        }

        $data['user_id'] = Auth::id();

        $supportRequest = AccountSupportRequest::create($data);

        $user = \App\Models\User::where('email', $data['email'])->first();
        if ($user) {
            $user->update([
                'status' => 'suspended',
                'suspended_until' => now()->addHours(24),
            ]);
        }

        $admins = \App\Models\User::whereHas('organizations', function ($q) {
            $q->whereIn('role', ['admin', 'manager']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\AccountSupportRequestNotification($supportRequest));
        }

        try {
            $notifyEmail = \App\Models\Organization::firstOrFail()->settings['notification_email'] ?? null;
            if ($notifyEmail) {
                Mail::to($notifyEmail)->send(new AdminNotificationMail(
                    subjectText: 'New Account Support Request',
                    body: "Email: {$data['email']}\nName: " . ($data['name'] ?? 'N/A') . "\n\nMessage:\n{$data['message']}",
                    actionUrl: route('manage.account-support.index'),
                    actionText: 'View Requests',
                ));
            }
        } catch (\Exception $e) {}

        return redirect()->route('login')
            ->with('status', 'Your request has been submitted. Your account has been temporarily suspended. An administrator will review your account shortly.');
    }

    public function show(AccountSupportRequest $accountSupport)
    {
        return view('manage.account-support.show', compact('accountSupport'));
    }

    public function resolve(Request $request, AccountSupportRequest $accountSupport)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:5000']);

        $accountSupport->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
        ]);

        return redirect()->route('manage.account-support.index')
            ->with('success', 'Support request marked as resolved.');
    }
}
