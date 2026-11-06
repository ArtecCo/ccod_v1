<style>
.ccod-cloud-loader{position:fixed;inset:0;z-index:99999;display:grid;place-items:center;padding:1.5rem;background:rgb(8 15 28 / 72%);-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);opacity:0;visibility:hidden;pointer-events:none;transition:opacity 180ms ease,visibility 180ms ease}
.ccod-cloud-loader.is-visible{opacity:1;visibility:visible;pointer-events:all}
.ccod-cloud-loader__panel{display:flex;width:min(100%,19rem);flex-direction:column;align-items:center;padding:2rem 2.25rem 1.75rem;border:1px solid rgb(148 163 184 / 25%);border-radius:1rem;background:#111c2e;box-shadow:0 24px 80px rgb(0 0 0 / 38%);color:#f8fafc;text-align:center}
.ccod-cloud-loader__cloud{position:relative;display:grid;width:7rem;height:6rem;place-items:center;margin-bottom:1.25rem}
.ccod-cloud-loader__cloud>svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible}
.ccod-cloud-loader__cloud>svg path{fill:#1e3a5f;stroke:#60a5fa;stroke-width:2.5}
.ccod-cloud-loader__cube{position:relative;width:1.35rem;height:1.35rem;margin-top:1rem;transform-style:preserve-3d;animation:ccod-cloud-cube-spin 2.2s linear infinite}
.ccod-cloud-loader__cube span{position:absolute;inset:0;border:1px solid #bfdbfe;background:rgb(59 130 246 / 78%);box-shadow:inset 0 0 10px rgb(191 219 254 / 25%)}
.ccod-cloud-loader__cube span:nth-child(1){transform:translateZ(.675rem)}
.ccod-cloud-loader__cube span:nth-child(2){transform:rotateY(180deg) translateZ(.675rem)}
.ccod-cloud-loader__cube span:nth-child(3){transform:rotateY(90deg) translateZ(.675rem)}
.ccod-cloud-loader__cube span:nth-child(4){transform:rotateY(-90deg) translateZ(.675rem)}
.ccod-cloud-loader__cube span:nth-child(5){transform:rotateX(90deg) translateZ(.675rem)}
.ccod-cloud-loader__cube span:nth-child(6){transform:rotateX(-90deg) translateZ(.675rem)}
.ccod-cloud-loader__title{font-size:1rem;font-weight:650;letter-spacing:.01em}
.ccod-cloud-loader__subtitle{margin-top:.35rem;color:#a8bad1;font-size:.8rem}
.ccod-cloud-loader__track{width:9rem;height:3px;overflow:hidden;margin-top:1.4rem;border-radius:99px;background:#26364d}
.ccod-cloud-loader__track span{display:block;width:42%;height:100%;border-radius:inherit;background:linear-gradient(90deg,#38bdf8,#60a5fa);animation:ccod-cloud-progress 1.15s ease-in-out infinite}
@keyframes ccod-cloud-cube-spin{from{transform:rotateX(-25deg) rotateY(0deg) rotateZ(0deg)}to{transform:rotateX(335deg) rotateY(360deg) rotateZ(360deg)}}
@keyframes ccod-cloud-progress{0%{transform:translateX(-115%)}100%{transform:translateX(245%)}}
@media(prefers-reduced-motion:reduce){.ccod-cloud-loader,.ccod-cloud-loader__cube,.ccod-cloud-loader__track span{animation:none!important;transition:none!important}}
</style>
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
                link.hasAttribute('download') ||
                link.hasAttribute('wire:navigate') ||
                link.hasAttribute('wire:click')
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
