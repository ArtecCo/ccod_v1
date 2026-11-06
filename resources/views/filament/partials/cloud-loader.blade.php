<div
    id="ccod-cloud-loader"
    class="ccod-cloud-loader"
    role="status"
    aria-live="polite"
    aria-label="Loading cloud operations"
    aria-hidden="true"
>
    <div class="ccod-cloud-loader__panel">
        <div class="ccod-cloud-loader__cloud" aria-hidden="true">
            <svg viewBox="0 0 96 72" fill="none">
                <path d="M25 55h46a15 15 0 0 0 1-30 24 24 0 0 0-46-3A17 17 0 0 0 25 55Z" />
            </svg>
            <div class="ccod-cloud-loader__cube">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <div class="ccod-cloud-loader__title">Connecting to the cloud</div>
        <div class="ccod-cloud-loader__subtitle">Gathering your operations data</div>
        <div class="ccod-cloud-loader__track"><span></span></div>
    </div>
</div>

<script>
(() => {
    const initCloudLoader = () => {
        const overlay = document.getElementById('ccod-cloud-loader');

        if (!overlay || overlay.dataset.initialized === 'true' || ! window.Livewire) {
            return;
        }

        overlay.dataset.initialized = 'true';

        let activeRequests = 0;
        let showTimer = null;

        const show = () => {
            activeRequests++;

            if (activeRequests === 1) {
                showTimer = window.setTimeout(() => {
                    overlay.classList.add('is-visible');
                    overlay.setAttribute('aria-hidden', 'false');
                }, 120);
            }
        };

        const hide = () => {
            activeRequests = Math.max(0, activeRequests - 1);

            if (activeRequests === 0) {
                window.clearTimeout(showTimer);
                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');
            }
        };

        window.Livewire.hook('request', ({ succeed, fail }) => {
            show();
            succeed(() => hide());
            fail(() => hide());
        });

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');

            if (
                ! link ||
                event.defaultPrevented ||
                event.button !== 0 ||
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey ||
                link.target === '_blank' ||
                link.hasAttribute('download')
            ) {
                return;
            }

            const destination = new URL(link.href, window.location.href);

            if (
                destination.origin === window.location.origin &&
                (destination.pathname !== window.location.pathname || destination.search !== window.location.search)
            ) {
                show();
            }
        });

        window.addEventListener('pageshow', () => {
            activeRequests = 0;
            window.clearTimeout(showTimer);
            overlay.classList.remove('is-visible');
            overlay.setAttribute('aria-hidden', 'true');
        });
    };

    document.addEventListener('livewire:init', initCloudLoader);
    document.addEventListener('DOMContentLoaded', initCloudLoader);
    initCloudLoader();
})();
</script>
