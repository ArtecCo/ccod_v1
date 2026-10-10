<script data-navigate-once>
    (() => {
        if (window.__ccodNotificationPoller) {
            return;
        }

        const interval = 10000;
        let timer = null;
        let requestInFlight = false;

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

                window.dispatchEvent(new CustomEvent('ccod:notifications-updated', {
                    detail: payload,
                }));

                window.dispatchEvent(new CustomEvent('refresh-sidebar'));
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
