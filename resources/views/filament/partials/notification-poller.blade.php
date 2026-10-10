<script data-navigate-once>
    (() => {
        if (window.__ccodNotificationPoller) {
            return;
        }

        const interval = 10000;
        let timer = null;
        let requestInFlight = false;

        const applyToNotificationsPage = (payload) => {
            const root = document.querySelector('[x-data="clientNotificationCenter"]');

            if (! root || ! window.Alpine) {
                return;
            }

            try {
                const component = Alpine.$data(root);

                if (component?.timer !== null) {
                    window.clearInterval(component.timer);
                    component.timer = null;
                }

                const incoming = payload.notifications || [];
                const currentRead = component.notifications.filter(notification => notification.read_at);
                const incomingIds = new Set(incoming.map(notification => notification.id));

                component.notifications = [...incoming, ...currentRead.filter(notification => !incomingIds.has(notification.id))]
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                component.unreadCount = payload.unread_count || 0;
                component.loading = false;
            } catch (error) {
                console.error(error);
            }
        };

        const updateSidebarBadge = (unreadCount) => {
            const notificationUrl = new URL('{{ \App\Filament\Pages\ClientNotifications::getUrl() }}', window.location.origin);
            const links = document.querySelectorAll('a[href]');
            const notificationLink = Array.from(links).find(link => {
                try {
                    return new URL(link.href, window.location.origin).pathname === notificationUrl.pathname;
                } catch (error) {
                    return false;
                }
            });

            if (! notificationLink) {
                return;
            }

            const sidebarItem = notificationLink.closest('.fi-sidebar-item') || notificationLink;
            let badge = sidebarItem.querySelector('[data-ccod-notification-badge]');

            if (! badge) {
                badge = sidebarItem.querySelector('.fi-sidebar-item-badge');
            }

            if (! badge && unreadCount > 0) {
                badge = document.createElement('span');
                badge.className = 'fi-sidebar-item-badge';
                badge.dataset.ccodNotificationBadge = 'true';
                notificationLink.appendChild(badge);
            }

            if (! badge) {
                return;
            }

            badge.dataset.ccodNotificationBadge = 'true';
            badge.textContent = unreadCount > 0 ? String(unreadCount) : '';
            badge.hidden = unreadCount <= 0;
        };

        const refresh = async () => {
            if (requestInFlight || document.visibilityState === 'hidden') {
                return;
            }

            requestInFlight = true;

            try {
                const response = await fetch('{{ route('notifications.index') }}', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) {
                    throw new Error('Notification request failed.');
                }

                const payload = await response.json();
                applyToNotificationsPage(payload);
                updateSidebarBadge(payload.unread_count || 0);

                window.dispatchEvent(new CustomEvent('ccod:notifications-updated', {
                    detail: payload,
                }));
            } catch (error) {
                console.error(error);
            } finally {
                requestInFlight = false;
            }
        };

        const start = () => {
            if (timer !== null) {
                window.clearInterval(timer);
            }

            refresh();
            timer = window.setInterval(refresh, interval);
        };

        const stop = () => {
            if (timer !== null) {
                window.clearInterval(timer);
                timer = null;
            }
        };

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                refresh();
            }
        });

        window.addEventListener('pagehide', stop, { once: true });

        window.__ccodNotificationPoller = { refresh, start, stop };
        start();
    })();
</script>
