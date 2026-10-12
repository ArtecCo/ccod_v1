<script>
(() => {
    const panelKey = document.documentElement.dataset.filamentPanel
        ?? (window.location.pathname.startsWith('/developer') ? 'developer' : 'admin');
    const storageKey = `ccod-sidebar-groups-${panelKey}`;
    const buttonId = `ccod-sidebar-collapse-all-${panelKey}`;

    const getGroupButtons = () => Array.from(
        document.querySelectorAll('.fi-sidebar-group-btn[aria-expanded]')
    );

    const updateButton = (button) => {
        const groups = getGroupButtons();

        if (!button || !groups.length) {
            return;
        }

        const allCollapsed = groups.every(
            (group) => group.getAttribute('aria-expanded') === 'false'
        );

        button.textContent = allCollapsed ? 'Expand all' : 'Collapse all';
        button.setAttribute(
            'aria-label',
            allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups'
        );
        button.setAttribute(
            'title',
            allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups'
        );
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

        let button = document.getElementById(buttonId);

        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.id = buttonId;
            button.className = 'ccod-sidebar-collapse-all';
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
                updateButton(button);
            });

            nav.prepend(button);
        }

        updateButton(button);
        applySavedState();
        updateButton(button);

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
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
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
    .ccod-sidebar-collapse-all {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        box-sizing: border-box !important;
        position: relative !important;
        z-index: 9999 !important;
        width: 100% !important;
        min-width: 100% !important;
        min-height: 2rem !important;
        flex: 0 0 auto !important;
        padding: 0.35rem 0.65rem !important;
        margin: 0 0 0.35rem !important;
        border: 0 !important;
        border-radius: 0.375rem !important;
        background: transparent !important;
        color: rgb(107 114 128) !important;
        font-family: inherit !important;
        font-size: 0.72rem !important;
        font-weight: 600 !important;
        line-height: 1.25rem !important;
        text-align: right !important;
        white-space: nowrap !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        cursor: pointer !important;
        transition: color 150ms ease, background-color 150ms ease !important;
    }

    .ccod-sidebar-collapse-all:hover {
        color: rgb(17 24 39) !important;
        background: rgb(243 244 246) !important;
    }

    .dark .ccod-sidebar-collapse-all {
        color: rgb(156 163 175) !important;
    }

    .dark .ccod-sidebar-collapse-all:hover {
        color: rgb(243 244 246) !important;
        background: rgb(31 41 55) !important;
    }
</style>
