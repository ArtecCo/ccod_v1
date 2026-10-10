@php
    $panel = \Filament\Facades\Filament::getCurrentPanel();
    $user = auth()->user();
@endphp

@if ($panel && $user)
    @php
        $changelogUrl = \Ysfkaya\ShipLog\ShipLogPlugin::get()->getPage()::getUrl();
    @endphp

    <div class="px-2 pb-2">
        <a
            href="{{ $changelogUrl }}"
            wire:navigate
            class="fi-sidebar-item-button group flex w-full items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 hover:text-gray-950 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-white"
        >
            <x-heroicon-o-megaphone class="fi-sidebar-item-icon h-5 w-5 shrink-0 text-gray-500 transition group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200" />

            <span class="fi-sidebar-item-label truncate">
                Changelog
            </span>
        </a>
    </div>
@endif
