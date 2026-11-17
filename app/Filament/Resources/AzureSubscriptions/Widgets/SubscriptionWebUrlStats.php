<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionWebUrlStats extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $urls = $this->record->webUrls();
        $active = (clone $urls)->where('is_active', true);
        $total = (clone $urls)->count();
        $live = (clone $active)->where('is_live', true)->count();
        $down = (clone $active)->where('is_live', false)->count();
        $sslExpiring = (clone $active)->whereNotNull('ssl_expires_at')
            ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)])->count();

        $card = fn (string $filter, string $background, string $border): array => [
            'role' => 'button',
            'tabindex' => '0',
            'class' => 'cursor-pointer',
            'style' => "background-color: {$background} !important; border: 1px solid {$border} !important;",
            'x-on:click' => "\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
            'x-on:keydown.enter' => "\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
        ];

        return [
            Stat::make('Total URLs', number_format($total))
                ->icon('heroicon-o-globe-alt')
                ->color('primary')
                ->extraAttributes($card('all', 'rgba(59, 130, 246, 0.10)', 'rgba(59, 130, 246, 0.35)')),
            Stat::make('Total Live', number_format($live))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->extraAttributes($card('live', 'rgba(34, 197, 94, 0.10)', 'rgba(34, 197, 94, 0.35)')),
            Stat::make('Total Down', number_format($down))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->extraAttributes($card('down', 'rgba(239, 68, 68, 0.10)', 'rgba(239, 68, 68, 0.35)')),
            Stat::make('SSL Expiring in 15 Days', number_format($sslExpiring))
                ->icon('heroicon-o-shield-exclamation')
                ->color('warning')
                ->extraAttributes($card('ssl_expiring', 'rgba(245, 158, 11, 0.12)', 'rgba(245, 158, 11, 0.40)')),
        ];
    }
}
