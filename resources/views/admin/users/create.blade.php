@extends('layouts.dashboard')

@section('pageTitle', 'Add User')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.users.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Users</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Add New User</h1>
    <p class="text-sm text-slate-500 mt-1">Create a new user account and assign their role.</p>
</div>

<div class="max-w-2xl">
    <form action="{{ route('manage.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create User
            </button>
            <a href="{{ route('manage.users.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
        </div>
    </form>
</div>
@endsection