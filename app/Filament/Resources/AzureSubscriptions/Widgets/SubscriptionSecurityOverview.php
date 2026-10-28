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
        $lastEvaluated = (clone $assessmentQuery)->max('first_evaluated_at');
        $lastChanged = (clone $assessmentQuery)->max('status_changed_at');

        $scores = $this->record->securityScores()
            ->orderByDesc('captured_at')
            ->limit(7)
            ->get()
            ->sortBy('captured_at')
            ->values();

        $latestScore = $scores->last();
        $scorePercentage = $latestScore !== null
            ? round(((float) $latestScore->percentage_score) * 100, 2)
            : null;
        $scoreChart = $scores->map(fn ($score): float => round(((float) $score->percentage_score) * 100, 2))->all();
        $scoreDescription = $latestScore !== null
            ? sprintf('%.2f / %.2f points', (float) $latestScore->current_score, (float) $latestScore->max_score)
            : 'No secure score imported yet';

        return [
            Stat::make('Security Score', $scorePercentage !== null ? $scorePercentage . '%' : '—')
                ->description($scoreDescription)
                ->icon('heroicon-o-shield-check')
                ->color($scorePercentage === null ? 'gray' : ($scorePercentage >= 80 ? 'success' : ($scorePercentage >= 60 ? 'warning' : 'danger')))
                ->chart($scoreChart),
            Stat::make('Unhealthy', (string) $unhealthy)
                ->description('Recommendations requiring attention')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($unhealthy > 0 ? 'danger' : 'success'),
            Stat::make('Last Evaluated', $lastEvaluated ? Carbon::parse($lastEvaluated)->format('d M Y, H:i') : '—')
                ->description('Latest Defender assessment evaluation')
                ->icon('heroicon-o-clock')
                ->color('gray'),
            Stat::make('Last Status Change', $lastChanged ? Carbon::parse($lastChanged)->format('d M Y, H:i') : '—')
                ->description('Latest recommendation status change')
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
        ];
    }
}
