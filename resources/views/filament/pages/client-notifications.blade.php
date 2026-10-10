<x-filament-panels::page>
    <div
        x-data="clientNotificationCenter()"
        x-init="init()"
        class="space-y-4"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Your latest portal notifications and alerts.
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
                <div class="p-6 text-sm text-gray-500 dark:text-gray-400">Loading notifications…</div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="p-8 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <x-filament::icon icon="heroicon-o-bell" class="h-5 w-5 text-gray-500" />
                    </div>
                    <p class="mt-3 text-sm font-medium text-gray-950 dark:text-white">No notifications</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You are all caught up.</p>
                </div>
            </template>

            <div x-show="!loading && notifications.length > 0" class="divide-y divide-gray-950/5 dark:divide-white/10">
                <template x-for="notification in notifications" :key="notification.id">
                    <button
                        type="button"
                        class="block w-full text-left transition hover:bg-gray-50 dark:hover:bg-white/5"
                        x-on:click="openNotification(notification)"
                    >
                        <div class="flex gap-4 px-5 py-4">
                            <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full"
                                x-bind:class="notification.read_at ? 'bg-gray-300 dark:bg-gray-700' : severityClass(notification.data.severity)">
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-950 dark:text-white" x-text="notification.data.title"></p>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300" x-text="notification.data.message"></p>
                                    </div>
                                    <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400" x-text="formatDate(notification.created_at)"></span>
                                </div>

                                <div x-show="notification.data.action_url" class="mt-3">
                                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400" x-text="notification.data.action_label || 'Open'"></span>
                                </div>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
            Notifications refresh automatically every 45 seconds.
        </div>

        <div
            x-show="toast"
            x-transition
            class="fixed bottom-5 right-5 z-50 rounded-lg bg-gray-950 px-4 py-3 text-sm text-white shadow-lg"
            x-text="toast"
        ></div>
    </div>

    <script>
        function clientNotificationCenter() {
            return {
                notifications: [],
                unreadCount: 0,
                loading: true,
                toast: '',
                timer: null,

                async init() {
                    await this.refresh();
                    this.timer = window.setInterval(() => this.refresh(), 45000);
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
