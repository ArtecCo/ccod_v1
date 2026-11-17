<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

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
            'x-on:click' => "\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
            'x-on:keydown.enter' => "\$dispatch('filter-subscription-web-urls', { filter: '{$filter}' })",
        ];

        return [
            Stat::make('Total URLs', number_format($total))
                ->icon('heroicon-o-globe-alt')
                ->extraAttributes($card('all')),
            Stat::make('Total Live', number_format($live))
                ->icon('heroicon-o-check-circle')
                ->extraAttributes($card('live')),
            Stat::make('Total Down', new HtmlString('<span style="color: #dc2626;">' . e(number_format($down)) . '</span>'))
                ->icon('heroicon-o-x-circle')
                ->extraAttributes($card('down')),
            Stat::make('SSL Expiring in 15 Days', new HtmlString('<span style="color: #d97706;">' . e(number_format($sslExpiring)) . '</span>'))
                ->icon('heroicon-o-shield-exclamation')
                ->extraAttributes($card('ssl_expiring')),
        ];
    }
}
