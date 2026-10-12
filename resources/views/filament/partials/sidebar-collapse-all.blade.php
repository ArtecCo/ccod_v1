<script>
(() => {
    const panelKey = document.documentElement.dataset.filamentPanel ?? window.location.pathname.split('/')[1] ?? 'default';
    const storageKey = `ccod-sidebar-groups-${panelKey}`;
    const buttonId = 'ccod-sidebar-collapse-all';

    const getGroupButtons = () => Array.from(
        document.querySelectorAll('.fi-sidebar-group-btn[aria-expanded]')
    );

    const updateButton = (button) => {
        const groups = getGroupButtons();

        if (!groups.length || !button) {
            return;
        }

        const allCollapsed = groups.every((group) => group.getAttribute('aria-expanded') === 'false');
        button.textContent = allCollapsed ? 'Expand all' : 'Collapse all';
        button.setAttribute('aria-label', allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups');
        button.setAttribute('title', allCollapsed ? 'Expand all navigation groups' : 'Collapse all navigation groups');
        button.dataset.collapsed = allCollapsed ? 'true' : 'false';
    };

    const applySavedState = () => {
        const saved = localStorage.getItem(storageKey);

        if (saved !== 'collapsed' && saved !== 'expanded') {
            return;
        }

        getGroupButtons().forEach((group) => {
            const expanded = group.getAttribute('aria-expanded') === 'true';
            const shouldExpand = saved === 'expanded';

            if (expanded !== shouldExpand) {
                group.click();
            }
        });
    };

    const install = () => {
        const nav = document.querySelector('.fi-sidebar-nav');

        if (!nav) {
            return;
        }

        let button = document.getElementById(buttonId);

        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.id = buttonId;
            button.className = 'ccod-sidebar-collapse-all';
            button.addEventListener('click', () => {
                const groups = getGroupButtons();
                const allCollapsed = groups.length > 0 && groups.every(
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
    };

    const observer = new MutationObserver(() => {
        install();
    });

    const start = () => {
        install();

        const sidebar = document.querySelector('.fi-sidebar');

        if (sidebar) {
            observer.observe(sidebar, { childList: true, subtree: true });
        }
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
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        padding: 0.35rem 0.65rem;
        margin-bottom: 0.35rem;
        border: 0;
        background: transparent;
        color: rgb(107 114 128);
        font-size: 0.72rem;
        font-weight: 600;
        line-height: 1.25rem;
        cursor: pointer;
        transition: color 150ms ease;
    }

    .ccod-sidebar-collapse-all:hover {
        color: rgb(17 24 39);
    }

    .dark .ccod-sidebar-collapse-all {
        color: rgb(156 163 175);
    }

    .dark .ccod-sidebar-collapse-all:hover {
        color: rgb(243 244 246);
    }
</style>
