<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use Filament\Widgets\ChartWidget;

class SubscriptionSecurityScoreHistoryChart extends ChartWidget
{
    public AzureSubscription $record;

    protected static ?string $heading = 'Security Score History';

    protected static string $color = 'gray';

    protected ?string $pollingInterval = null;

    protected static bool $isLazy = true;

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '15' => 'Last 15 days',
            '30' => 'Last 30 days',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            '7' => 7,
            '15' => 15,
            '30' => 30,
            default => 30,
        };

        $latest = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->orderByDesc('captured_at')
            ->first();

        if ($latest === null || $latest->initiative_name === null) {
            return [
                'datasets' => [
                    [
                        'label' => 'Security Score',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $from = now()->subDays($days - 1)->startOfDay();

        $scores = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('initiative_name', $latest->initiative_name)
            ->where('captured_at', '>=', $from)
            ->orderBy('captured_at')
            ->get(['captured_at', 'percentage_score']);

        return [
            'datasets' => [
                [
                    'label' => 'Security Score',
                    'data' => $scores
                        ->map(fn (AzureSecurityScore $score): float => round((float) $score->percentage_score * 100, 2))
                        ->values()
                        ->all(),
                ],
            ],
            'labels' => $scores
                ->map(fn (AzureSecurityScore $score): string => $score->captured_at->format('d M'))
                ->values()
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
