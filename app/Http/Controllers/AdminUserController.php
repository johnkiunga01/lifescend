<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    protected function currentUserRole()
    {
        return request()->user()->organizations()->first()?->pivot?->role ?? 'customer';
    }

    protected function allowedRoles(): array
    {
        return $this->currentUserRole() === 'admin'
            ? ['customer', 'staff', 'manager', 'admin']
            : ['customer', 'staff'];
    }

    public function index()
    {
        $isManager = $this->currentUserRole() !== 'admin';

        $users = User::with('organizations')
            ->when($isManager, function ($q) {
                $q->whereDoesntHave('organizations', function ($q) {
                    $q->where('role', 'admin');
                });
            })
            ->latest()
            ->get();

        $organization = Organization::query()->first();

        return view('admin.users.index', compact('users', 'organization', 'isManager'));
    }

    public function create()
    {
        $organization = Organization::query()->first();
        return view('admin.users.create', compact('organization'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->allowedRoles();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
        ]);

        $organization = Organization::query()->first();
        if ($organization) {
            $organization->users()->attach($user->id, [
                'role' => $data['role'],
                'status' => 'active',
                'invited_by' => $request->user()->id,
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email, 'role' => $data['role']],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manage.users.index')->with('success', "User {$user->name} created successfully.");
    }

    public function edit(int $id)
    {
        $user = User::with('organizations')->findOrFail($id);
        $organization = Organization::query()->first();
        $userRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        return view('admin.users.edit', compact('user', 'organization', 'userRole'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $allowedRoles = $this->allowedRoles();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:50',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? $user->phone,
            'status' => $data['status'],
        ]);

        if ($data['password']) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $organization = Organization::query()->first();
        if ($organization) {
            $organization->users()->syncWithoutDetaching([
                $user->id => ['role' => $data['role']],
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email, 'role' => $data['role'], 'status' => $data['status']],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('manage.users.index')->with('success', "User {$user->name} updated successfully.");
    }

    public function block(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $selfRole = $this->currentUserRole();
        $targetRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'You cannot block yourself.']);
        }

        if ($selfRole !== 'admin' && in_array($targetRole, ['admin', 'manager'])) {
            return back()->withErrors(['error' => 'You can only block customer and staff accounts.']);
        }

        $user->update(['status' => 'suspended']);

        $organization = Organization::query()->first();
        if ($organization) {
            $organization->users()->syncWithoutDetaching([
                $user->id => ['status' => 'suspended'],
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_blocked',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "User {$user->name} has been blocked.");
    }

    public function unblock(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $selfRole = $this->currentUserRole();
        $targetRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($selfRole !== 'admin' && in_array($targetRole, ['admin', 'manager'])) {
            return back()->withErrors(['error' => 'You can only unblock customer and staff accounts.']);
        }

        $user->update(['status' => 'active', 'suspended_until' => null]);

        $organization = Organization::query()->first();
        if ($organization) {
            $organization->users()->syncWithoutDetaching([
                $user->id => ['status' => 'active'],
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_unblocked',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "User {$user->name} has been unblocked.");
    }

    public function manualVerify(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return back()->withErrors(['error' => 'User is already verified.']);
        }

        $user->markEmailAsVerified();

        \App\Models\EmailVerificationOtp::query()->where('user_id', $user->id)->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_manual_verify',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "{$user->name} has been manually verified.");
    }

    public function resetVerification(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $user->update(['email_verified_at' => null]);

        \App\Models\EmailVerificationOtp::query()->where('user_id', $user->id)->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_verification_reset',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $user->email],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Verification status reset for {$user->name}. They will need to verify their email again.");
    }

    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $selfRole = $this->currentUserRole();
        $targetRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }

        if ($selfRole !== 'admin' && in_array($targetRole, ['admin', 'manager'])) {
            return back()->withErrors(['error' => 'You can only delete customer and staff accounts.']);
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_deleted',
            'model_type' => User::class,
            'model_id' => $user->id,
            'metadata' => ['email' => $userEmail, 'role' => $targetRole],
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "User {$userName} has been deleted.");
    }
}
