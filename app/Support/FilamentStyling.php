<?php

namespace App\Support;

use App\Settings\StylingSettings;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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

    public static function brandName(): string
    {
        return self::settings()?->brand_name ?? 'CCOD';
    }

    public static function logoLight(): ?string
    {
        return self::assetUrl(self::settings()?->logo_light);
    }

    public static function logoDark(): ?string
    {
        return self::assetUrl(self::settings()?->logo_dark);
    }

    public static function favicon(): ?string
    {
        return self::assetUrl(self::settings()?->favicon);
    }

    public static function cornerRadius(): string
    {
        return self::settings()?->corner_radius ?? '0.375rem';
    }

    public static function darkModeTheme(): string
    {
        return self::settings()?->dark_mode_theme ?? 'black';
    }

    private static function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    private static function color(string $color): array
    {
        if (str_starts_with($color, 'custom:')) {
            $hex = substr($color, 7);

            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
                return Color::hex($hex);
            }
        }

        return match ($color) {
            'black' => Color::hex('#000000'),
            'gray' => Color::Zinc,
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
            default => Color::Zinc,
        };
    }
}
