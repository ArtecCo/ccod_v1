<?php

namespace App\Filament\Pages;

class Changelog extends \Ysfkaya\ShipLog\Filament\Pages\Changelog
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
