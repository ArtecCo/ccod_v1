<?php

return [
    Barryvdh\DomPDF\ServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\ProfileMenuServiceProvider::class,
    App\Providers\DarkModeThemeServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\DeveloperPanelProvider::class,
];
