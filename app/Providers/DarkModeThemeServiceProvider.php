<?php

namespace App\Providers;

use App\Support\FilamentStyling;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class DarkModeThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::styles.after',
            function (): HtmlString {
                $settings = FilamentStyling::settings();
                $lightTheme = $settings?->light_mode_theme ?? 'gray';
                $darkTheme = $settings?->dark_mode_theme ?? 'black';

                $lightPalette = self::themePalette($lightTheme);
                $darkPalette = self::themePalette($darkTheme);

                $css = '<style>';
                $css .= self::paletteCss('html:not(.dark)', $lightPalette);

                if ($darkTheme !== 'black') {
                    $css .= self::paletteCss('html.dark', $darkPalette);
                }

                $css .= '\n                    html .ccod-notifications__row:has(.ccod-notifications__severity--info) {
                        border-left: 4px solid var(--info-500, #3b82f6) !important;
                    }

                    html .ccod-notifications__row:has(.ccod-notifications__severity--success) {
                        border-left: 4px solid var(--success-500, #22c55e) !important;
                    }

                    html .ccod-notifications__row:has(.ccod-notifications__severity--warning) {
                        border-left: 4px solid var(--warning-500, #f59e0b) !important;
                    }

                    html .ccod-notifications__row:has(.ccod-notifications__severity--critical),
                    html .ccod-notifications__row:has(.ccod-notifications__severity--danger) {
                        border-left: 4px solid var(--danger-500, #ef4444) !important;
                    }
                </style>';

                return new HtmlString($css);
            },
        );
    }

    private static function themePalette(string $theme): array
    {
        return match ($theme) {
            'slate' => Color::Slate,
            'gray', 'blue' => Color::Gray,
            'zinc', 'black' => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone' => Color::Stone,
            'mauve' => Color::Mauve,
            'olive' => Color::Olive,
            'mist' => Color::Mist,
            'taupe' => Color::Taupe,
            default => Color::Gray,
        };
    }

    private static function paletteCss(string $selector, array $palette): string
    {
        $variables = '';

        foreach ($palette as $shade => $value) {
            $variables .= "--gray-{$shade}: {$value};";
        }

        return "{$selector}{{$variables}}";
    }
}
