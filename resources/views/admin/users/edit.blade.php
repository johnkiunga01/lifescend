@extends('layouts.dashboard')

@section('pageTitle', 'Edit User')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.users.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Users</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Edit User</h1>
    <p class="text-sm text-slate-500 mt-1">Update {{ $user->name }}'s account details and permissions.</p>
</div>

<div class="max-w-2xl">
    <form action="{{ route('manage.users.update', $user->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                <div class="h-14 w-14 rounded-full bg-gold-500 flex items-center justify-center font-bold text-white text-xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $user->email) }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                    <input id="password" name="password" type="password"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"
                        placeholder="Leave blank to keep current">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="customer" {{ old('role', $userRole) === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="staff" {{ old('role', $userRole) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role', $userRole) === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role', $userRole) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>

            @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update User
            </button>
            <a href="{{ route('manage.users.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
        </div>
    </form>
</div>
@endsection