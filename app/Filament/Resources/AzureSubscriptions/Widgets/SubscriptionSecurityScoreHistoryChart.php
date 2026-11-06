<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use Filament\Widgets\ChartWidget;

class SubscriptionSecurityScoreHistoryChart extends ChartWidget
{
    public AzureSubscription $record;

    protected ?string $heading = 'Security Score History';

    protected string $color = 'gray';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
        ];
    }

    protected function getData(): array
    {
        $days = 7;

        $from = now()->subDays($days - 1)->startOfDay();

        // Resolve the most recently captured initiative inside the history
        // query, avoiding a separate latest-record round trip.
        $scores = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('initiative_name', function ($query): void {
                $query->select('initiative_name')
                    ->from('azure_security_scores')
                    ->where('subscription_id', $this->record->subscription_id)
                    ->orderByDesc('captured_at')
                    ->limit(1);
            })
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
