<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StylingSettings extends Settings
{
    public string $brand_name = 'CCOD';
    public ?string $logo_light = null;
    public ?string $logo_dark = null;
    public ?string $favicon = null;
    public string $font_family = 'Inter';
    public string $primary_color = 'amber';
    public string $success_color = 'green';
    public string $warning_color = 'amber';
    public string $danger_color = 'red';
    public string $info_color = 'blue';
    public string $gray_color = 'gray';
    public string $corner_radius = '0.375rem';

    public static function group(): string
    {
        return 'styling';
    }
}
