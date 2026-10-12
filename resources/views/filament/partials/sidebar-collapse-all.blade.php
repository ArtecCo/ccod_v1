<script>
(() => {
    const panelKey = document.documentElement.dataset.filamentPanel
        ?? (window.location.pathname.startsWith('/developer') ? 'developer' : 'admin');
    const storageKey = `ccod-sidebar-groups-${panelKey}`;
    const buttonId = `ccod-sidebar-collapse-all-${panelKey}`;

    const getGroupButtons = () => Array.from(
        document.querySelectorAll('.fi-sidebar-group-btn[aria-expanded]')
    );

    const getButton = () => document.getElementById(buttonId);

    const updateButton = () => {
        const button = getButton();
        const groups = getGroupButtons();

        if (!button || !groups.length) {
            return;
        }

        const allCollapsed = groups.every(
            (group) => group.getAttribute('aria-expanded') === 'false'
        );

        button.dataset.state = allCollapsed ? 'collapsed' : 'expanded';
        button.setAttribute(
            'aria-label',
            allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups'
        );
        button.setAttribute(
            'title',
            allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups'
        );

        button.innerHTML = allCollapsed
            ? '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5 7.5 10 12.5 15 7.5"/></svg>'
            : '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 12.5 5-5 5 5"/></svg>';
    };

    const applySavedState = () => {
        const saved = localStorage.getItem(storageKey);

        if (saved !== 'collapsed' && saved !== 'expanded') {
            return;
        }

        const shouldExpand = saved === 'expanded';

        getGroupButtons().forEach((group) => {
            const expanded = group.getAttribute('aria-expanded') === 'true';

            if (expanded !== shouldExpand) {
                group.click();
            }
        });
    };

    const install = () => {
        const nav = document.querySelector('.fi-sidebar-nav');

        if (!nav) {
            return false;
        }

        let button = getButton();

        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.id = buttonId;
            button.className = 'ccod-sidebar-collapse-all';
            button.setAttribute('aria-label', 'Collapse all navigation groups');
            button.setAttribute('title', 'Collapse all navigation groups');

            button.addEventListener('click', () => {
                const groups = getGroupButtons();

                if (!groups.length) {
                    return;
                }

                const allCollapsed = groups.every(
                    (group) => group.getAttribute('aria-expanded') === 'false'
                );
                const shouldExpand = allCollapsed;

                groups.forEach((group) => {
                    const expanded = group.getAttribute('aria-expanded') === 'true';

                    if (expanded !== shouldExpand) {
                        group.click();
                    }
                });

                localStorage.setItem(storageKey, shouldExpand ? 'expanded' : 'collapsed');
                updateButton();
            });

            nav.prepend(button);
        }

        updateButton();
        applySavedState();
        updateButton();

        return true;
    };

    let observer = null;
    let installTimer = null;

    const scheduleInstall = () => {
        if (installTimer !== null) {
            return;
        }

        installTimer = window.setTimeout(() => {
            installTimer = null;
            install();
        }, 50);
    };

    const start = () => {
        install();

        if (!observer) {
            observer = new MutationObserver(() => {
                scheduleInstall();
                updateButton();
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['aria-expanded'],
            });
        }

        let attempts = 0;
        const retry = () => {
            if (install() || attempts >= 20) {
                return;
            }

            attempts += 1;
            window.setTimeout(retry, 100);
        };

        retry();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
})();
</script>

<style>
    /* Keep the control as a compact overlay at the top-right of the sidebar navigation. */
    .fi-sidebar-nav {
        position: relative !important;
    }

    .ccod-sidebar-collapse-all {
        position: absolute !important;
        top: 0.35rem !important;
        right: 0.65rem !important;
        z-index: 999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 1.75rem !important;
        height: 1.75rem !important;
        min-width: 1.75rem !important;
        min-height: 1.75rem !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0.375rem !important;
        background: transparent !important;
        color: rgb(107 114 128) !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        opacity: 1 !important;
        visibility: visible !important;
        line-height: 1 !important;
    }

    .ccod-sidebar-collapse-all svg {
        width: 0.9rem !important;
        height: 0.9rem !important;
        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 1.8 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
    }

    .ccod-sidebar-collapse-all:hover {
        background: rgb(243 244 246) !important;
        color: rgb(17 24 39) !important;
    }

    .dark .ccod-sidebar-collapse-all {
        color: rgb(156 163 175) !important;
    }

    .dark .ccod-sidebar-collapse-all:hover {
        background: rgb(31 41 55) !important;
        color: rgb(243 244 246) !important;
    }
</style>
