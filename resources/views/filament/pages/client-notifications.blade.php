<x-filament-panels::page>
    <div
        x-data="clientNotificationCenter()"
        x-init="init()"
        class="space-y-6"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Your notifications</h2>
                    <span
                        x-show="unreadCount > 0"
                        x-cloak
                        class="inline-flex min-w-6 items-center justify-center rounded-full bg-primary-600 px-2 py-0.5 text-xs font-semibold text-white"
                        x-text="unreadCount"
                    ></span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Stay up to date with important portal notifications and alerts.
                </p>
            </div>

            <x-filament::button
                color="gray"
                size="sm"
                icon="heroicon-o-check"
                x-on:click="markAllRead()"
                x-bind:disabled="unreadCount === 0"
            >
                Mark all as read
            </x-filament::button>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <template x-if="loading">
                <div class="divide-y divide-gray-950/5 dark:divide-white/10">
                    <template x-for="i in 3" :key="i">
                        <div class="flex gap-4 px-5 py-5">
                            <div class="mt-1 h-10 w-10 shrink-0 animate-pulse rounded-full bg-gray-100 dark:bg-gray-800"></div>
                            <div class="min-w-0 flex-1 space-y-2">
                                <div class="h-4 w-2/5 animate-pulse rounded bg-gray-100 dark:bg-gray-800"></div>
                                <div class="h-4 w-4/5 animate-pulse rounded bg-gray-100 dark:bg-gray-800"></div>
                                <div class="h-3 w-1/5 animate-pulse rounded bg-gray-100 dark:bg-gray-800"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <x-filament::icon icon="heroicon-o-bell" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-950 dark:text-white">No notifications</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up.</p>
                </div>
            </template>

            <div x-show="!loading && notifications.length > 0" class="divide-y divide-gray-950/5 dark:divide-white/10">
                <template x-for="notification in notifications" :key="notification.id">
                    <button
                        type="button"
                        class="group relative block w-full text-left transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:hover:bg-white/[0.03]"
                        x-bind:class="notification.read_at ? '' : 'bg-primary-50/50 dark:bg-primary-950/20'"
                        x-on:click="openNotification(notification)"
                    >
                        <div class="flex gap-4 px-5 py-5 sm:px-6">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                x-bind:class="notification.read_at ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' : severityBackground(notification.data.severity)"
                            >
                                <x-filament::icon icon="heroicon-o-bell" class="h-5 w-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span
                                                x-show="!notification.read_at"
                                                class="h-2 w-2 shrink-0 rounded-full"
                                                x-bind:class="severityClass(notification.data.severity)"
                                            ></span>
                                            <p class="truncate text-sm font-semibold text-gray-950 dark:text-white" x-text="notification.data.title"></p>
                                        </div>
                                        <p class="mt-1.5 text-sm leading-6 text-gray-600 dark:text-gray-300" x-text="notification.data.message"></p>
                                    </div>

                                    <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400 sm:pt-0.5" x-text="formatDate(notification.created_at)"></span>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        x-bind:class="typeClass(notification.data.type)"
                                        x-text="formatType(notification.data.type)"
                                    ></span>

                                    <span
                                        x-show="notification.data.action_url"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-600 dark:text-primary-400"
                                    >
                                        <span x-text="notification.data.action_label || 'Open'">Open</span>
                                        <x-filament::icon icon="heroicon-m-arrow-right" class="h-3.5 w-3.5" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <x-filament::icon icon="heroicon-o-arrow-path" class="h-3.5 w-3.5" />
            <span>Notifications refresh automatically every 45 seconds.</span>
        </div>

        <div x-show="toast" x-transition x-cloak class="fixed bottom-5 right-5 z-50 rounded-lg bg-gray-950 px-4 py-3 text-sm font-medium text-white shadow-lg dark:bg-white dark:text-gray-950" x-text="toast"></div>
    </div>

    <script>
        function clientNotificationCenter() {
            return {
                notifications: [],
                unreadCount: 0,
                loading: true,
                toast: '',
                timer: null,
                initialized: false,

                async init() {
                    if (this.initialized) return;
                    this.initialized = true;

                    await this.refresh();
                    this.timer = window.setInterval(() => this.refresh(), 45000);
                },

                destroy() {
                    if (this.timer !== null) {
                        window.clearInterval(this.timer);
                        this.timer = null;
                    }
                },

                async refresh() {
                    try {
                        const response = await fetch('{{ route('notifications.index') }}', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) throw new Error('Notification request failed.');

                        const payload = await response.json();
                        this.notifications = payload.notifications || [];
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

                    if (notification.data.action_url) {
                        window.location.href = notification.data.action_url;
                    }
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

                        this.notifications.forEach(notification => notification.read_at = notification.read_at || new Date().toISOString());
                        this.unreadCount = 0;
                        this.showToast('All notifications marked as read.');
                    } catch (error) {
                        console.error(error);
                        this.showToast('Unable to update notifications.');
                    }
                },

                severityClass(severity) {
                    return {
                        info: 'bg-primary-500',
                        success: 'bg-success-500',
                        warning: 'bg-warning-500',
                        danger: 'bg-danger-500',
                    }[severity] || 'bg-primary-500';
                },

                severityBackground(severity) {
                    return {
                        info: 'bg-primary-100 text-primary-600 dark:bg-primary-950/60 dark:text-primary-400',
                        success: 'bg-success-100 text-success-600 dark:bg-success-950/60 dark:text-success-400',
                        warning: 'bg-warning-100 text-warning-600 dark:bg-warning-950/60 dark:text-warning-400',
                        danger: 'bg-danger-100 text-danger-600 dark:bg-danger-950/60 dark:text-danger-400',
                    }[severity] || 'bg-primary-100 text-primary-600 dark:bg-primary-950/60 dark:text-primary-400';
                },

                typeClass(type) {
                    return {
                        general: 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-white/10',
                        announcement: 'bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-950/40 dark:text-primary-300 dark:ring-primary-400/20',
                        maintenance: 'bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-950/40 dark:text-warning-300 dark:ring-warning-400/20',
                        alert: 'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-950/40 dark:text-danger-300 dark:ring-danger-400/20',
                        action_required: 'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-950/40 dark:text-success-300 dark:ring-success-400/20',
                    }[type] || 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-white/10';
                },

                formatType(type) {
                    return (type || 'general').replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
                },

                formatDate(value) {
                    if (!value) return '';
                    return new Intl.DateTimeFormat(undefined, {
                        dateStyle: 'medium',
                        timeStyle: 'short',
                    }).format(new Date(value));
                },

                showToast(message) {
                    this.toast = message;
                    window.setTimeout(() => this.toast = '', 2500);
                },
            };
        }
    </script>
</x-filament-panels::page>
