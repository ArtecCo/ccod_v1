<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MaintenanceSettings extends Settings
{
    public bool $enabled = false;

    public static function group(): string
    {
        return 'maintenance';
    }
}
