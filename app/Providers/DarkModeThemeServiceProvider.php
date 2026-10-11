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

                        html .ccod-notifications__row:has(.ccod-notifications__severity--info) {
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

                    html.dark .ccod-notifications__row--unread {
                        background-color: #1a1a1d !important;
                    }

                    html .ccod-notifications__row:has(.ccod-notifications__severity--info) {
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
                </style>');
            },
        );
    }
}
