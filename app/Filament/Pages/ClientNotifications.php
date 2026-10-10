<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ClientNotifications extends Page
{
    protected static ?string $navigationLabel = 'Notifications';
    protected static ?string $title = 'Notifications';
    protected static ?string $slug = 'notifications';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';
    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.client-notifications';
}
