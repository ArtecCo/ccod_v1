<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionSecurityOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $assessmentQuery = $this->record->securityAssessments();
        $unhealthy = (clone $assessmentQuery)->where('state', 'Unhealthy')->count();
        $high = (clone $assessmentQuery)
            ->where('state', 'Unhealthy')
            ->where('severity', 'High')
            ->count();
        $lastChanged = (clone $assessmentQuery)->max('status_changed_at');

        $latestScore = $this->record->securityScores()
            ->orderByDesc('captured_at')
            ->first();

        $scorePercentage = $latestScore !== null
            ? round(((float) $latestScore->percentage_score) * 100, 2)
            : null;

        $scoreChart = [];

        if ($latestScore !== null && $latestScore->initiative_name !== null) {
            $scoreChart = $this->record->securityScores()
                ->where('initiative_name', $latestScore->initiative_name)
                ->orderByDesc('captured_at')
                ->limit(7)
                ->get()
                ->sortBy('captured_at')
                ->map(fn ($score): float => round(((float) $score->percentage_score) * 100, 2))
                ->values()
                ->all();
        }

        $lastChangedLabel = $lastChanged !== null
            ? Carbon::parse($lastChanged)->format('d M Y, H:i')
            : '—';

        $securityScore = Stat::make('Security Score', $scorePercentage !== null ? $scorePercentage . '%' : '—')
            ->icon('heroicon-o-shield-check')
            ->color('gray');

        if (count($scoreChart) >= 2) {
            $securityScore = $securityScore->chart($scoreChart);
        }

        return [
            $securityScore,
            Stat::make('Unhealthy', (string) $unhealthy)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($unhealthy > 0 ? 'danger' : 'success'),
            Stat::make('High Recommendations', (string) $high)
                ->icon('heroicon-o-shield-exclamation')
                ->color($high > 0 ? 'danger' : 'success'),
            Stat::make('Last Status Change', $lastChangedLabel)
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
        ];
    }
}
