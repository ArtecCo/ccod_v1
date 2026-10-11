<?php

namespace App\Support;

use App\Settings\StylingSettings;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Schema;
use Throwable;

class FilamentStyling
{
    public static function settings(): ?StylingSettings
    {
        try {
            if (! Schema::hasTable('settings')) {
                return null;
            }

            return app(StylingSettings::class);
        } catch (Throwable) {
            return null;
        }
    }

    public static function colors(): array
    {
        $settings = self::settings();

        return [
            'primary' => self::color($settings?->primary_color ?? 'amber'),
            'success' => self::color($settings?->success_color ?? 'green'),
            'warning' => self::color($settings?->warning_color ?? 'amber'),
            'danger' => self::color($settings?->danger_color ?? 'red'),
            'info' => self::color($settings?->info_color ?? 'blue'),
            'gray' => self::color($settings?->gray_color ?? 'gray'),
        ];
    }

    public static function fontFamily(): string
    {
        return self::settings()?->font_family ?? 'Inter';
    }

    public static function brandName(): string
    {
        return self::settings()?->brand_name ?? 'CCOD';
    }

    public static function logoLight(): ?string
    {
        return self::settings()?->logo_light ?: null;
    }

    public static function logoDark(): ?string
    {
        return self::settings()?->logo_dark ?: null;
    }

    public static function favicon(): ?string
    {
        return self::settings()?->favicon ?: null;
    }

    public static function cornerRadius(): string
    {
        return self::settings()?->corner_radius ?? '0.375rem';
    }

    private static function color(string $color): array
    {
        return match ($color) {
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
            default => Color::Gray,
        };
    }
}
