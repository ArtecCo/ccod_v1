<x-filament-panels::page>
    <style>
        .ccod-notifications { color: rgb(17 24 39); font-family: inherit; font-size: 1rem; line-height: 1.5rem; }
        .dark .ccod-notifications { color: rgb(243 244 246); }
        .ccod-notifications__header { display:flex; align-items:flex-end; justify-content:space-between; gap:1.5rem; margin-bottom:1.25rem; }
        .ccod-notifications__eyebrow { margin:0; font-size:1rem; font-weight:600; line-height:1.5rem; }
        .ccod-notifications__description { margin:.25rem 0 0; color:rgb(107 114 128); font-size:.875rem; line-height:1.25rem; }
        .dark .ccod-notifications__description { color:rgb(156 163 175); }
        .ccod-notifications__count { display:inline-flex; min-width:1.5rem; height:1.5rem; align-items:center; justify-content:center; margin-left:.4rem; padding:0 .4rem; border-radius:9999px; background:rgb(245 158 11); color:white; font-size:.75rem; font-weight:700; line-height:1; vertical-align:middle; }
        .ccod-notifications__mark-read { border:1px solid rgb(209 213 219); border-radius:.5rem; background:white; padding:.5rem .75rem; color:rgb(55 65 81); font-size:.875rem; font-weight:600; line-height:1.25rem; cursor:pointer; }
        .ccod-notifications__mark-read:disabled { cursor:not-allowed; opacity:.5; }
        .dark .ccod-notifications__mark-read { border-color:rgb(55 65 81); background:rgb(31 41 55); color:rgb(229 231 235); }
        .ccod-notifications__panel { overflow:hidden; border:1px solid rgb(229 231 235); border-radius:.75rem; background:white; box-shadow:0 1px 2px rgba(0,0,0,.04); }
        .dark .ccod-notifications__panel { border-color:rgb(55 65 81); background:rgb(17 24 39); }
        .ccod-notifications__loading,.ccod-notifications__empty { padding:3.5rem 1.5rem; text-align:center; }
        .ccod-notifications__empty-icon { display:flex; width:3rem; height:3rem; align-items:center; justify-content:center; margin:0 auto .875rem; border-radius:9999px; background:rgb(243 244 246); color:rgb(107 114 128); }
        .dark .ccod-notifications__empty-icon { background:rgb(31 41 55); color:rgb(156 163 175); }
        .ccod-notifications__empty-title { margin:0; font-size:.875rem; font-weight:700; }
        .ccod-notifications__empty-text { margin:.25rem 0 0; color:rgb(107 114 128); font-size:.875rem; }
        .dark .ccod-notifications__empty-text { color:rgb(156 163 175); }
        .ccod-notifications__row { display:flex; width:100%; gap:.875rem; padding:1rem 1.125rem; border:0; border-bottom:1px solid rgb(229 231 235); background:transparent; color:inherit; text-align:left; cursor:pointer; }
        .ccod-notifications__row:last-child { border-bottom:0; }
        .ccod-notifications__row:hover { background:rgb(249 250 251); }
        .dark .ccod-notifications__row { border-color:rgb(55 65 81); }
        .dark .ccod-notifications__row:hover { background:rgba(255,255,255,.035); }
        .ccod-notifications__row--unread { background:rgba(245,158,11,.055); }
        .dark .ccod-notifications__row--unread { background:rgba(245,158,11,.07); }
        .ccod-notifications__icon { display:flex; width:2.5rem; height:2.5rem; flex:0 0 2.5rem; align-items:center; justify-content:center; border-radius:.625rem; background:rgb(243 244 246); color:rgb(107 114 128); }
        .ccod-notifications__icon--info { background:rgb(239 246 255); color:rgb(37 99 235); }
        .ccod-notifications__icon--success { background:rgb(240 253 244); color:rgb(22 163 74); }
        .ccod-notifications__icon--warning { background:rgb(255 251 235); color:rgb(217 119 6); }
        .ccod-notifications__icon--danger { background:rgb(254 242 242); color:rgb(220 38 38); }
        .dark .ccod-notifications__icon { background:rgb(31 41 55); color:rgb(156 163 175); }
        .ccod-notifications__body { min-width:0; flex:1; }
        .ccod-notifications__top { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; }
        .ccod-notifications__title-wrap { display:flex; min-width:0; align-items:center; gap:.45rem; }
        .ccod-notifications__unread-dot { width:.4rem; height:.4rem; flex:0 0 .4rem; border-radius:9999px; background:rgb(245 158 11); }
        .ccod-notifications__title { overflow:hidden; margin:0; font-size:1rem; font-weight:700; line-height:1.5rem; text-overflow:ellipsis; white-space:nowrap; }
        .ccod-notifications__date { flex:0 0 auto; color:rgb(107 114 128); font-size:.75rem; line-height:1.25rem; }
        .dark .ccod-notifications__date { color:rgb(156 163 175); }
        .ccod-notifications__message { margin:.25rem 0 0; color:rgb(75 85 99); font-size:.875rem; line-height:1.5rem; }
        .dark .ccod-notifications__message { color:rgb(209 213 219); }
        .ccod-notifications__meta { display:flex; align-items:center; gap:.5rem; margin-top:.625rem; }
        .ccod-notifications__type { display:inline-flex; align-items:center; border-radius:.375rem; padding:.2rem .45rem; background:rgb(243 244 246); color:rgb(75 85 99); font-size:.75rem; font-weight:700; line-height:1rem; }
        .dark .ccod-notifications__type { background:rgb(31 41 55); color:rgb(209 213 219); }
        .ccod-notifications__action { color:rgb(217 119 6); font-size:.875rem; font-weight:700; }
        .dark .ccod-notifications__action { color:rgb(251 191 36); }
        .ccod-notifications__footer { display:flex; align-items:center; gap:.4rem; margin-top:.75rem; color:rgb(107 114 128); font-size:.75rem; }
        .dark .ccod-notifications__footer { color:rgb(156 163 175); }
        @media (max-width:640px) { .ccod-notifications__header { align-items:stretch; flex-direction:column; } .ccod-notifications__mark-read { align-self:flex-start; } .ccod-notifications__top { flex-direction:column; gap:.15rem; } }
    </style>

    <div x-data="clientNotificationCenter" x-init="init()" class="ccod-notifications">
        <div class="ccod-notifications__header">
            <div>
                <p class="ccod-notifications__eyebrow">
                    Your notifications
                    <span x-show="unreadCount > 0" x-cloak class="ccod-notifications__count" x-text="unreadCount"></span>
                </p>
                <p class="ccod-notifications__description">Stay up to date with important portal notifications and alerts.</p>
            </div>
            <button type="button" class="ccod-notifications__mark-read" x-on:click="markAllRead()" x-bind:disabled="unreadCount === 0">Mark all as read</button>
        </div>

        <div class="ccod-notifications__panel">
            <template x-if="loading">
                <div class="ccod-notifications__loading"><p class="ccod-notifications__empty-title">Loading notifications…</p></div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="ccod-notifications__empty">
                    <div class="ccod-notifications__empty-icon"><x-filament::icon icon="heroicon-o-bell" class="h-5 w-5" /></div>
                    <p class="ccod-notifications__empty-title">No notifications</p>
                    <p class="ccod-notifications__empty-text">You're all caught up.</p>
                </div>
            </template>

            <div x-show="!loading && notifications.length > 0">
                <template x-for="notification in notifications" :key="notification.id">
                    <button type="button" class="ccod-notifications__row" x-bind:class="notification.read_at ? '' : 'ccod-notifications__row--unread'" x-on:click="openNotification(notification)">
                        <div class="ccod-notifications__icon" x-bind:class="notification.read_at ? '' : 'ccod-notifications__icon--' + (notification.data.severity || 'info')">
                            <x-filament::icon icon="heroicon-o-bell" class="h-5 w-5" />
                        </div>
                        <div class="ccod-notifications__body">
                            <div class="ccod-notifications__top">
                                <div class="ccod-notifications__title-wrap">
                                    <span x-show="!notification.read_at" class="ccod-notifications__unread-dot"></span>
                                    <p class="ccod-notifications__title" x-text="notification.data.title"></p>
                                </div>
                                <span class="ccod-notifications__date" x-text="formatDate(notification.created_at)"></span>
                            </div>
                            <p class="ccod-notifications__message" x-text="notification.data.message"></p>
                            <div class="ccod-notifications__meta">
                                <span class="ccod-notifications__type" x-text="formatType(notification.data.type)"></span>
                                <span x-show="notification.data.action_url" class="ccod-notifications__action" x-text="(notification.data.action_label || 'Open') + ' →'"></span>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <div class="ccod-notifications__footer">
            <x-filament::icon icon="heroicon-o-arrow-path" class="h-3.5 w-3.5" />
            <span>Notifications refresh automatically every 45 seconds.</span>
        </div>
    </div>

    @script
    <script>
        Alpine.data('clientNotificationCenter', () => ({
            notifications: [],
            unreadCount: 0,
            loading: true,
            initialized: false,
            timer: null,
            cleanupBound: false,

            async init() {
                if (this.initialized) return;
                this.initialized = true;
                await this.refresh();
                this.timer = window.setInterval(() => this.refresh(), 45000);
                this.bindCleanup();
            },

            destroy() {
                if (this.timer !== null) {
                    window.clearInterval(this.timer);
                    this.timer = null;
                }
            },

            bindCleanup() {
                if (this.cleanupBound) return;
                this.cleanupBound = true;
                this.handleNavigation = () => this.destroy();
                this.handlePageHide = () => this.destroy();
                document.addEventListener('livewire:navigating', this.handleNavigation, { once: true });
                window.addEventListener('pagehide', this.handlePageHide, { once: true });
            },

            async refresh() {
                try {
                    const response = await fetch('{{ route('notifications.index') }}', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error('Notification request failed.');
                    const payload = await response.json();
                    const incoming = payload.notifications || [];
                    const currentRead = this.notifications.filter(notification => notification.read_at);
                    const incomingIds = new Set(incoming.map(notification => notification.id));
                    this.notifications = [...incoming, ...currentRead.filter(notification => !incomingIds.has(notification.id))]
                        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    this.unreadCount = payload.unread_count || 0;
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },

            async openNotification(notification) {
                if (notification.read_at === null) {
                    try {
                        const response = await fetch(`{{ url('/notifications') }}/${notification.id}/read`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            credentials: 'same-origin',
                        });
                        if (response.ok) {
                            notification.read_at = new Date().toISOString();
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }
                if (notification.data.action_url) window.location.href = notification.data.action_url;
            },

            async markAllRead() {
                if (this.unreadCount === 0) return;
                try {
                    const response = await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) throw new Error('Unable to mark notifications as read.');
                    const readAt = new Date().toISOString();
                    this.notifications.forEach(notification => notification.read_at = notification.read_at || readAt);
                    this.unreadCount = 0;
                } catch (error) {
                    console.error(error);
                }
            },

            formatType(type) {
                return (type || 'general').replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
            },

            formatDate(value) {
                if (!value) return '';
                return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
            },
        }));
    </script>
    @endscript
</x-filament-panels::page>
