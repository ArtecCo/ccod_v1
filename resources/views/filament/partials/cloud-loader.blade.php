<style>
.ccod-livewire-placeholder{display:flex;min-height:13rem;width:100%;align-items:center;justify-content:center;padding:2rem 1rem;color:var(--primary-600,var(--primary-500,#64748b))}
.ccod-livewire-placeholder__content{display:flex;flex-direction:column;align-items:center;gap:.75rem;color:var(--gray-600,#4b5563);font-size:.875rem;font-weight:500}
.dark .ccod-livewire-placeholder__content{color:var(--gray-300,#d1d5db)}
.ccod-livewire-placeholder__cloud{position:relative;display:grid;width:4.4rem;height:3.5rem;place-items:center;color:var(--primary-600,var(--primary-500,#64748b))}
.ccod-livewire-placeholder__cloud svg{position:absolute;inset:0;width:100%;height:100%;fill:color-mix(in srgb,currentColor 12%,transparent);stroke:currentColor;stroke-width:2.5}
.ccod-livewire-placeholder__cube{position:relative;width:.8rem;height:.8rem;margin-top:.4rem;transform-style:preserve-3d;animation:ccod-tab-cube-spin 1.8s linear infinite}
.ccod-livewire-placeholder__cube span{position:absolute;inset:0;border:1px solid currentColor;background:color-mix(in srgb,currentColor 55%,transparent)}
.ccod-livewire-placeholder__cube span:nth-child(1){transform:translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(2){transform:rotateY(180deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(3){transform:rotateY(90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(4){transform:rotateY(-90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(5){transform:rotateX(90deg) translateZ(.4rem)}
.ccod-livewire-placeholder__cube span:nth-child(6){transform:rotateX(-90deg) translateZ(.4rem)}
@media(prefers-reduced-motion:reduce){.ccod-livewire-placeholder__cube{animation:none!important}}
</style>
<style>
/* The loader lives inside the active Filament tab panel, never over the whole page. */
.ccod-tab-loader-host{position:relative!important;isolation:isolate;min-height:12rem}
.ccod-tab-loader{position:absolute;inset:0;z-index:40;display:flex;align-items:flex-start;justify-content:center;padding:3rem 1rem 1rem;background:color-mix(in srgb,var(--gray-50,#f9fafb) 82%,transparent);opacity:0;visibility:hidden;pointer-events:none;transition:opacity 160ms ease,visibility 160ms ease}
.dark .ccod-tab-loader{background:color-mix(in srgb,var(--gray-950,#030712) 82%,transparent)}
.ccod-tab-loader.is-visible{opacity:1;visibility:visible}
.ccod-tab-loader__content{display:flex;flex-direction:column;align-items:center;gap:.65rem;color:var(--gray-600,#4b5563);font-size:.85rem;font-weight:500;text-align:center}
.dark .ccod-tab-loader__content{color:var(--gray-300,#d1d5db)}
.ccod-tab-loader__cloud{position:relative;width:3.8rem;height:3rem;display:grid;place-items:center;color:var(--primary-600,var(--primary-500,#64748b))}
.ccod-tab-loader__cloud svg{position:absolute;inset:0;width:100%;height:100%;fill:color-mix(in srgb,currentColor 12%,transparent);stroke:currentColor;stroke-width:2.5}
.ccod-tab-loader__cube{position:relative;width:.72rem;height:.72rem;margin-top:.35rem;transform-style:preserve-3d;animation:ccod-tab-cube-spin 1.8s linear infinite}
.ccod-tab-loader__cube span{position:absolute;inset:0;border:1px solid currentColor;background:color-mix(in srgb,currentColor 55%,transparent);opacity:.9}
.ccod-tab-loader__cube span:nth-child(1){transform:translateZ(.36rem)}
.ccod-tab-loader__cube span:nth-child(2){transform:rotateY(180deg) translateZ(.36rem)}
.ccod-tab-loader__cube span:nth-child(3){transform:rotateY(90deg) translateZ(.36rem)}
.ccod-tab-loader__cube span:nth-child(4){transform:rotateY(-90deg) translateZ(.36rem)}
.ccod-tab-loader__cube span:nth-child(5){transform:rotateX(90deg) translateZ(.36rem)}
.ccod-tab-loader__cube span:nth-child(6){transform:rotateX(-90deg) translateZ(.36rem)}
.ccod-tab-loader__caption{color:inherit}
@keyframes ccod-tab-cube-spin{from{transform:rotateX(-25deg) rotateY(0) rotateZ(0)}to{transform:rotateX(335deg) rotateY(360deg) rotateZ(360deg)}}
@media(prefers-reduced-motion:reduce){.ccod-tab-loader,.ccod-tab-loader__cube{animation:none!important;transition:none!important}}
</style>

<template id="ccod-tab-loader-template">
    <div class="ccod-tab-loader" aria-hidden="true">
        <div class="ccod-tab-loader__content" role="status" aria-live="polite">
            <div class="ccod-tab-loader__cloud" aria-hidden="true">
                <svg viewBox="0 0 96 72"><path d="M25 55h46a15 15 0 0 0 1-30 24 24 0 0 0-46-3A17 17 0 0 0 25 55Z"/></svg>
                <div class="ccod-tab-loader__cube"><span></span><span></span><span></span><span></span><span></span><span></span></div>
            </div>
            <span class="ccod-tab-loader__caption">Loading tab data…</span>
        </div>
    </div>
</template>

<script>
(() => {
    const initTabLoader = () => {
        if (!window.Livewire || window.__ccodTabLoaderInitialized) return;
        window.__ccodTabLoaderInitialized = true;

        let activeRequests = 0;
        let showTimer = null;
        let loader = null;
        let host = null;

        const getActivePanel = () => {
            const selectedTab = document.querySelector('[role="tab"][aria-selected="true"]');
            const panelId = selectedTab?.getAttribute('aria-controls');
            if (panelId) {
                const controlledPanel = document.getElementById(panelId);
                if (controlledPanel) return controlledPanel;
            }

            return document.querySelector('[role="tabpanel"]:not([hidden])');
        };

        const show = () => {
            const panel = getActivePanel();
            if (!panel) return;

            if (host !== panel) {
                loader?.remove();
                host = panel;
                host.classList.add('ccod-tab-loader-host');
                const template = document.getElementById('ccod-tab-loader-template');
                loader = template?.content.firstElementChild.cloneNode(true) ?? null;
                if (loader) host.appendChild(loader);
            }

            if (!loader) return;
            activeRequests++;

            if (activeRequests === 1) {
                showTimer = window.setTimeout(() => {
                    loader?.classList.add('is-visible');
                    loader?.setAttribute('aria-hidden', 'false');
                }, 120);
            } else if (host === panel) {
                loader.classList.add('is-visible');
                loader.setAttribute('aria-hidden', 'false');
            }
        };

        const hide = () => {
            activeRequests = Math.max(0, activeRequests - 1);
            if (activeRequests === 0) {
                window.clearTimeout(showTimer);
                loader?.classList.remove('is-visible');
                loader?.setAttribute('aria-hidden', 'true');
            }
        };

        window.Livewire.hook('request', ({ succeed, fail }) => {
            show();
            succeed(() => hide());
            fail(() => hide());
        });

        document.addEventListener('livewire:navigated', () => {
            activeRequests = 0;
            window.clearTimeout(showTimer);
            loader?.remove();
            loader = null;
            host = null;
        });
    };

    document.addEventListener('livewire:init', initTabLoader);
    document.addEventListener('DOMContentLoaded', initTabLoader);
    initTabLoader();
})();
</script>
