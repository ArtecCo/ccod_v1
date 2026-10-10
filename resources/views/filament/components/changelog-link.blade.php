@php
    $changelogUrl = \App\Filament\Pages\Changelog::getUrl();
@endphp

<div class="px-2 pb-2">
    <a
        href="{{ $changelogUrl }}"
        wire:navigate
        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-950 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white"
    >
        <x-filament::icon icon="heroicon-o-megaphone" class="h-5 w-5" />
        <span>Changelog</span>
    </a>
</div>
