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

        $card = fn (string $filter): array => [
            'role' => 'button',
            'tabindex' => '0',
            'class' => 'cursor-pointer',
            'x-on:click' => "\\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
            'x-on:keydown.enter' => "\\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
        ];

        return [
            Stat::make('Total URLs', number_format($total))->icon('heroicon-o-globe-alt')->color('primary')
                ->extraAttributes(['class' => 'ring-1 ring-primary-200/60 dark:ring-primary-800/30'])
                ->extraAttributes($card('all')),
            Stat::make('Total Live', number_format($live))->icon('heroicon-o-check-circle')->color('success')
                ->extraAttributes(['class' => 'bg-success-50/60 dark:bg-success-950/20 ring-1 ring-success-200/60 dark:ring-success-800/30'])
                ->extraAttributes($card('live')),
            Stat::make('Total Down', number_format($down))->icon('heroicon-o-x-circle')->color('danger')
                ->extraAttributes(['class' => 'bg-danger-50/60 dark:bg-danger-950/20 ring-1 ring-danger-200/60 dark:ring-danger-800/30'])
                ->extraAttributes($card('down')),
            Stat::make('SSL Expiring in 15 Days', number_format($sslExpiring))->icon('heroicon-o-shield-exclamation')->color('warning')
                ->extraAttributes(['class' => 'bg-warning-50/60 dark:bg-warning-950/20 ring-1 ring-warning-200/60 dark:ring-warning-800/30'])
                ->extraAttributes($card('ssl_expiring')),
        ];
    }
}
