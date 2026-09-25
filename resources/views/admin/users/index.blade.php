@extends('layouts.dashboard')

@section('pageTitle', 'Manage Users')

@section('content')
<div x-data="{
    addUserModal: {{ old('_form') === 'add' ? 'true' : 'false' }},
    editUserModal: {{ old('_form') === 'edit' ? 'true' : 'false' }},
    editUser: {
        id: {{ old('_form') === 'edit' ? (old('edit_id') ?: 'null') : 'null' }},
        name: '{{ old('_form') === 'edit' ? addslashes(old('name', '')) : '' }}',
        email: '{{ old('_form') === 'edit' ? addslashes(old('email', '')) : '' }}',
        phone: '{{ old('_form') === 'edit' ? addslashes(old('phone', '')) : '' }}',
        role: '{{ old('_form') === 'edit' ? addslashes(old('role', 'customer')) : 'customer' }}',
        status: '{{ old('_form') === 'edit' ? addslashes(old('status', 'active')) : 'active' }}'
    },
    showAddPassword: false,
    showEditPassword: false,
    openEdit(user) {
        this.editUser = { ...user };
        this.editUserModal = true;
    }
}">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manage Users</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $users->count() }} total users in the system.</p>
        </div>
        <button @@click="addUserModal = true"
            class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add User
        </button>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any() && !session('success'))
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Role</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Verified</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    @php
                    $role = $user->organizations()->first()?->pivot?->role ?? 'customer';
                    $roleColors = ['admin' => 'bg-purple-100 text-purple-700', 'manager' => 'bg-blue-100 text-blue-700', 'staff' => 'bg-gold-100 text-gold-700', 'customer' => 'bg-slate-100 text-slate-600'];
                    $statusColors = ['active' => 'bg-emerald-100 text-emerald-700', 'inactive' => 'bg-slate-100 text-slate-500', 'suspended' => 'bg-red-100 text-red-700'];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-gold-500 flex items-center justify-center font-bold text-white text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-3 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleColors[$role] ?? $roleColors['customer'] }}">
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td class="px-3 py-4">
                            @if($user->suspended_until && $user->suspended_until->isFuture())
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-red-100 text-red-700">Suspended</span>
                            <p class="text-[10px] text-red-500 mt-0.5">Until {{ $user->suspended_until->format('M d, H:i') }}</p>
                            @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$user->status] ?? $statusColors['inactive'] }}">
                                {{ ucfirst($user->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-3 py-4">
                            @if($user->hasVerifiedEmail())
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">Yes</span>
                            @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">No</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-3 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$isManager || !in_array($role, ['admin', 'manager']))
                                <button @@click="openEdit({{ json_encode([
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'phone' => $user->phone ?? '',
                                        'role' => $role,
                                        'status' => $user->status,
                                    ]) }})"
                                    class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">
                                    Edit
                                </button>
                                @unless($user->hasVerifiedEmail())
                                <form action="{{ route('manage.users.manual-verify', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Manually verify {{ $user->name }}? This will mark their email as verified.');">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer">
                                        Mark Verified
                                    </button>
                                </form>
                                @endunless
                                @endif
                                @if($user->status === 'suspended')
                                <form action="{{ route('manage.users.unblock', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer">
                                        Unblock
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('manage.users.block', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Block {{ $user->name }}? They will lose access to the platform.');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                        Block
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('manage.users.destroy', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete {{ $user->name }}? This will deactivate their account and prevent future access. This action is not reversible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-sm text-slate-500">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add User Modal --}}
    <div x-show="addUserModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="addUserModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @@click="addUserModal = false"
                class="fixed inset-0 bg-black/60"></div>

            <div x-show="addUserModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                @@click.away="addUserModal = false"
                class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Add New User</h2>
                        <p class="text-sm text-slate-500 mt-1">Create a new user account and assign their role.</p>
                    </div>
                    <button @@click="addUserModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('manage.users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="add">

                    <div class="space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="au_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input id="au_name" name="name" type="text" required placeholder="e.g. Juan Dela Cruz"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                            <div>
                                <label for="au_email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input id="au_email" name="email" type="email" autocomplete="email" required placeholder="e.g. juan@example.com"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="au_password" class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input id="au_password" name="password" type="password" required placeholder="Min. 8 characters" autocomplete="new-password"
                                        x-bind:type="showAddPassword ? 'text' : 'password'"
                                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm pr-10">
                                    <button type="button" @@click="showAddPassword = !showAddPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <svg x-show="!showAddPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                        <svg x-show="showAddPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="au_phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                                <input id="au_phone" name="phone" type="text" placeholder="e.g. +63 912 345 6789"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="au_role" class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                                <select id="au_role" name="role" required
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    <option value="customer">Customer</option>
                                    <option value="staff">Staff</option>
                                    @if(!$isManager)
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="au_status" class="block text-sm font-medium text-slate-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                                <select id="au_status" name="status" required
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="addUserModal = false"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div x-show="editUserModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="editUserModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @@click="editUserModal = false"
                class="fixed inset-0 bg-black/60"></div>

            <div x-show="editUserModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                @@click.away="editUserModal = false"
                class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Edit User</h2>
                        <p class="text-sm text-slate-500 mt-1" x-text="'Updating ' + editUser.name"></p>
                    </div>
                    <button @@click="editUserModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form x-bind:action="`/manage/users/${editUser.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="edit_id" x-model="editUser.id">

                    <div class="space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="eu_name" class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input id="eu_name" name="name" type="text" required x-model="editUser.name" placeholder="e.g. Juan Dela Cruz"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                            <div>
                                <label for="eu_email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input id="eu_email" name="email" type="email" autocomplete="email" required x-model="editUser.email" placeholder="e.g. juan@example.com"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="eu_password" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                                <div class="relative">
                                    <input id="eu_password" name="password" type="password" placeholder="Leave blank to keep current" autocomplete="new-password"
                                        x-bind:type="showEditPassword ? 'text' : 'password'"
                                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm pr-10">
                                    <button type="button" @@click="showEditPassword = !showEditPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <svg x-show="!showEditPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                        <svg x-show="showEditPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="eu_phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                                <input id="eu_phone" name="phone" type="text" x-model="editUser.phone" placeholder="e.g. +63 912 345 6789"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="eu_role" class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                                <select id="eu_role" name="role" required x-model="editUser.role"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    <option value="customer">Customer</option>
                                    <option value="staff">Staff</option>
                                    @if(!$isManager)
                                    <option value="manager">Manager</option>
                                    <option value="admin">Admin</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="eu_status" class="block text-sm font-medium text-slate-700 mb-1">Account Status <span class="text-red-500">*</span></label>
                                <select id="eu_status" name="status" required x-model="editUser.status"
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="editUserModal = false"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection