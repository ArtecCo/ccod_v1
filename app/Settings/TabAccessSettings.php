<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TabAccessSettings extends Settings
{
    public string $restricted_tab_behavior = 'disabled';

    public static function group(): string
    {
        return 'tab_access';
    }
}
