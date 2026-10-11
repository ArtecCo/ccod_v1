<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StylingSettings extends Settings
{
    public string $brand_name;
    public ?string $logo_light;
    public ?string $logo_dark;
    public ?string $favicon;
    public string $font_family;
    public string $primary_color;
    public string $success_color;
    public string $warning_color;
    public string $danger_color;
    public string $info_color;
    public string $gray_color;
    public string $corner_radius;

    public static function group(): string
    {
        return 'styling';
    }
}
