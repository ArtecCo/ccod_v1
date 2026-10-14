<script>
(() => {
    const panelKey = document.documentElement.dataset.filamentPanel
        ?? (window.location.pathname.startsWith('/developer') ? 'developer' : 'admin');
    const storageKey = `ccod-sidebar-groups-${panelKey}`;
    const scrollStorageKey = `ccod-sidebar-scroll-${panelKey}`;
    const buttonId = `ccod-sidebar-collapse-all-${panelKey}`;

    const getGroupButtons = () => Array.from(
        document.querySelectorAll('.fi-sidebar-group-button[aria-expanded]')
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

    const restoreSidebarScroll = () => {
        const nav = document.querySelector('.fi-sidebar-nav');

        if (!nav) {
            return;
        }

        const savedScroll = Number.parseInt(localStorage.getItem(scrollStorageKey) ?? '0', 10);

        if (Number.isNaN(savedScroll)) {
            return;
        }

        requestAnimationFrame(() => {
            nav.scrollTop = savedScroll;
        });

        window.setTimeout(() => {
            nav.scrollTop = savedScroll;
        }, 100);
    };

    const install = () => {
        const nav = document.querySelector('.fi-sidebar-nav');

        if (!nav) {
            return false;
        }

        if (!nav.dataset.ccodScrollPersistence) {
            nav.dataset.ccodScrollPersistence = 'true';
            nav.addEventListener('scroll', () => {
                localStorage.setItem(scrollStorageKey, String(nav.scrollTop));
            }, { passive: true });
        }

        let button = getButton();

        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.id = buttonId;
            button.className = 'ccod-sidebar-collapse-all';
            button.setAttribute('aria-label', 'Collapse all navigation groups');
            button.setAttribute('title', 'Collapse all navigation groups');

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

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
        restoreSidebarScroll();

        return true;
    };

    const start = () => {
        install();

        // Re-run after Filament navigates between pages and rebuilds the sidebar.
        document.addEventListener('livewire:navigated', install);

        // Do not observe aria-expanded changes. Filament uses the same
        // attribute for the native collapsed-sidebar subscription flyout.
        const observer = new MutationObserver(() => {
            updateButton();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });

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
        display: block !important;
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
