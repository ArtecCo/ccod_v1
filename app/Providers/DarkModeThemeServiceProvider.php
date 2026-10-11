<?php

namespace App\Providers;

use App\Support\FilamentStyling;
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
                $theme = FilamentStyling::settings()?->dark_mode_theme ?? 'black';

                if ($theme === 'blue') {
                    return new HtmlString('<style>
                        html.dark .fi-sidebar,
                        html.dark .fi-main-ctn,
                        html.dark .fi-main,
                        html.dark .fi-page {
                            background-color: #030712 !important;
                        }

                        html.dark .fi-section,
                        html.dark .fi-ta-ctn,
                        html.dark .fi-wi-widget,
                        html.dark .fi-card,
                        html.dark .fi-modal-window {
                            background-color: #101827 !important;
                        }

                        html.dark .ccod-notifications__panel,
                        html.dark .ccod-notifications__row {
                            background-color: #101827 !important;
                        }
                    </style>');
                }

                return new HtmlString('<style>
                    html.dark .fi-sidebar,
                    html.dark .fi-main-ctn,
                    html.dark .fi-main,
                    html.dark .fi-page {
                        background-color: #09090b !important;
                    }

                    html.dark .fi-section,
                    html.dark .fi-ta-ctn,
                    html.dark .fi-wi-widget,
                    html.dark .fi-card,
                    html.dark .fi-modal-window {
                        background-color: #09090b !important;
                    }

                    html.dark .ccod-notifications__panel,
                    html.dark .ccod-notifications__row {
                        background-color: #09090b !important;
                    }
                </style>');
            },
        );
    }
}
