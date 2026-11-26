<script data-navigate-once>
    (() => {
        if (window.__ccodNavigationSearch) {
            return;
        }

        const inputSelector = '.fi-global-search input[type="search"]';
        const navSelector = '.fi-sidebar-nav';

        const filterNavigation = (query) => {
            const nav = document.querySelector(navSelector);

            if (! nav) {
                return;
            }

            const term = query.trim().toLocaleLowerCase();
            const groups = nav.querySelectorAll(':scope > .fi-sidebar-nav-groups > .fi-sidebar-group');

            groups.forEach((group) => {
                const groupLabel = group.querySelector(':scope > .fi-sidebar-group-btn .fi-sidebar-group-label')
                    ?.textContent?.trim().toLocaleLowerCase() ?? '';
                const groupMatches = term !== '' && groupLabel.includes(term);
                const list = group.querySelector(':scope > .fi-sidebar-group-items');

                if (! list) {
                    group.style.display = '';
                    return;
                }

                const filterItem = (item) => {
                    const label = item.querySelector(':scope > a .fi-sidebar-item-label')
                        ?.textContent?.trim().toLocaleLowerCase() ?? '';
                    const ownMatch = term === '' || groupMatches || label.includes(term);
                    const childList = item.querySelector(':scope > .fi-sidebar-sub-group-items');
                    let childMatch = false;

                    if (childList) {
                        childList.querySelectorAll(':scope > .fi-sidebar-item').forEach((child) => {
                            if (filterItem(child)) {
                                childMatch = true;
                            }
                        });
                    }

                    const visible = ownMatch || childMatch;
                    item.style.display = visible ? '' : 'none';

                    if (ownMatch && groupMatches && childList) {
                        childList.querySelectorAll('.fi-sidebar-item').forEach((child) => {
                            child.style.display = '';
                        });
                    }

                    return visible;
                };

                let hasVisibleItem = false;

                list.querySelectorAll(':scope > .fi-sidebar-item').forEach((item) => {
                    if (filterItem(item)) {
                        hasVisibleItem = true;
                    }
                });

                group.style.display = term === '' || groupMatches || hasVisibleItem ? '' : 'none';
            });
        };

        const onInput = (event) => {
            if (! event.target.matches(inputSelector)) {
                return;
            }

            filterNavigation(event.target.value);
        };

        document.addEventListener('input', onInput);

        document.addEventListener('keydown', (event) => {
            if (event.target.matches(inputSelector) && event.key === 'Escape') {
                event.target.value = '';
                filterNavigation('');
            }
        });

        document.addEventListener('livewire:navigated', () => {
            const input = document.querySelector(inputSelector);
            filterNavigation(input?.value ?? '');
        });

        window.__ccodNavigationSearch = { filterNavigation };
    })();
</script>
