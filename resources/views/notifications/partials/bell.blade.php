<div x-data="notificationBell()" x-init="init()" class="relative">
    <button @click="toggle()" class="relative p-2 rounded-lg transition-colors cursor-pointer"
        :class="open ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span x-show="count > 0"
            class="absolute -top-0.5 -right-0.5 h-4.5 min-w-[18px] flex items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-gold-600 px-1"
            x-text="count > 99 ? '99+' : count"></span>
    </button>

    <div x-show="open" @click.outside="close()" x-cloak
        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 z-50 max-h-[70vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
            <button x-show="count > 0" @click="markAllRead()"
                class="text-[11px] font-semibold text-gold-600 hover:text-gold-700 transition-colors cursor-pointer">Mark all read</button>
        </div>
        <div class="flex-1 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="py-10 text-center">
                    <svg class="h-10 w-10 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <p class="text-sm text-slate-400 font-medium">No notifications yet</p>
                </div>
            </template>
            <template x-for="n in notifications" :key="n.id">
                <div class="px-5 py-3.5 border-b border-slate-50 hover:bg-slate-50 transition-colors"
                    :class="n.read_at ? '' : 'bg-gold-50/50'">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 h-2 w-2 rounded-full shrink-0"
                            :class="n.read_at ? 'bg-transparent' : 'bg-gold-500'"></div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-slate-800 leading-relaxed">
                                <template x-if="n.data.action">
                                    <span x-text="n.data.description || n.data.label"></span>
                                </template>
                                <template x-if="!n.data.action && n.data.event_name">
                                    <span><strong x-text="n.data.user_name || 'Someone'"></strong> purchased tickets for <strong x-text="n.data.event_name"></strong></span>
                                </template>
                                <template x-if="!n.data.action && !n.data.event_name">
                                    <span class="text-xs text-slate-500">Notification</span>
                                </template>
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[10px] font-medium text-slate-400" x-text="timeAgo(n.created_at)"></span>
                                <button x-show="!n.read_at" @click="markRead(n.id)"
                                    class="text-[10px] font-semibold text-gold-600 hover:text-gold-700 cursor-pointer">Mark read</button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        @auth
        <a href="{{ url('/my/notifications') }}"
            class="block px-5 py-3 text-center text-xs font-semibold text-slate-500 hover:text-slate-700 border-t border-slate-100 hover:bg-slate-50 transition-colors rounded-b-2xl">
            View all notifications
        </a>
        @endauth
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        count: 0,
        notifications: [],
        timer: null,

        init() {
            this.fetch();
            this.timer = setInterval(() => this.fetch(), 30000);
        },

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },

        async fetch() {
            try {
                const res = await fetch('{{ route('notifications.unread') }}');
                const data = await res.json();
                if (res.ok) {
                    this.count = data.count;
                    this.notifications = data.notifications;
                }
            } catch (e) {}
        },

        async markAllRead() {
            await fetch('{{ route('notifications.markAllRead') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
            this.count = 0;
            this.notifications.forEach(n => n.read_at = new Date().toISOString());
        },

        async markRead(id) {
            const endpoint = '{{ url('/my/notifications') }}' + '/' + id + '/read';
            await fetch(endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
            const n = this.notifications.find(n => n.id === id);
            if (n) { n.read_at = new Date().toISOString(); this.count = Math.max(0, this.count - 1); }
        },

        timeAgo(date) {
            const now = new Date();
            const d = new Date(date);
            const sec = Math.floor((now - d) / 1000);
            if (sec < 60) return 'just now';
            if (sec < 3600) return Math.floor(sec / 60) + 'm ago';
            if (sec < 86400) return Math.floor(sec / 3600) + 'h ago';
            return Math.floor(sec / 86400) + 'd ago';
        },
    };
}
</script>
