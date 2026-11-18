<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Support\DataAge;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $scoreSnapshots = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->whereRaw(
                'initiative_name <=> (
                    SELECT latest_score.initiative_name
                    FROM azure_security_scores AS latest_score
                    WHERE latest_score.subscription_id = ?
                    ORDER BY latest_score.captured_at DESC
                    LIMIT 1
                )',
                [$this->record->subscription_id],
            )
            ->orderByDesc('captured_at')
            ->limit(30)
            ->get(['initiative_name', 'percentage_score', 'captured_at']);

        $latestScore = $scoreSnapshots->first();
        $score = $latestScore !== null
            ? round((float) $latestScore->percentage_score * 100, 2)
            : null;
        $scoreHistory = $latestScore !== null && $latestScore->initiative_name !== null
            ? $scoreSnapshots->sortBy('captured_at')->values()
            : collect();
        $previousScore = $scoreHistory->count() > 1
            ? $scoreHistory->get($scoreHistory->count() - 2)
            : null;
        $scoreDifference = $previousScore !== null && (float) $previousScore->percentage_score !== 0.0
            ? (((float) $latestScore->percentage_score - (float) $previousScore->percentage_score) / abs((float) $previousScore->percentage_score)) * 100
            : null;
        $scoreChart = $scoreHistory
            ->map(fn (AzureSecurityScore $snapshot): float => round((float) $snapshot->percentage_score * 100, 2))
            ->all();

        $scoreTrendColor = $scoreDifference === null
            ? 'gray'
            : ($scoreDifference >= 0 ? 'success' : 'danger');

        $securityStat = Stat::make('Security Score', $score !== null ? number_format($score, 2) . '%' : '—')
            ->icon('heroicon-o-shield-check')
            ->color('gray');

        if (count($scoreChart) >= 2) {
            $securityStat = $securityStat->chart($scoreChart)->chartColor($scoreTrendColor);
        }

        if ($scoreDifference !== null) {
            $securityStat = $securityStat
                ->description(($scoreDifference >= 0 ? '+' : '') . number_format($scoreDifference, 1) . '% vs previous score')
                ->descriptionIcon($scoreDifference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($scoreTrendColor);
        }

        $isHealthy = $score !== null && $score >= 80;

        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start')
            ->get(['amount', 'currency']);

        $currentCost = $costs->last();
        $previousCost = $costs->count() > 1 ? $costs->get($costs->count() - 2) : null;
        $costValue = $currentCost !== null
            ? strtoupper($currentCost->currency ?: 'EUR') . ' ' . number_format((float) $currentCost->amount, 2)
            : '—';
        $costChart = $costs->map(fn (AzureSubscriptionCost $cost): float => (float) $cost->amount)->values()->all();
        $costDifference = $currentCost !== null && $previousCost !== null && (float) $previousCost->amount != 0.0
            ? (((float) $currentCost->amount - (float) $previousCost->amount) / abs((float) $previousCost->amount)) * 100
            : null;
        $costTrendColor = $costDifference === null
            ? 'primary'
            : ($costDifference > 0 ? 'danger' : ($costDifference < 0 ? 'success' : 'primary'));

        $costStat = Stat::make('MTD Cost', $costValue)
            ->icon('heroicon-o-banknotes')
            ->color($costTrendColor);

        if (count($costChart) >= 2) {
            $costStat = $costStat->chart($costChart)->chartColor($costTrendColor);
        }

        if ($costDifference !== null) {
            $costStat = $costStat
                ->description(($costDifference >= 0 ? '+' : '') . number_format($costDifference, 1) . '% vs previous month')
                ->descriptionIcon($costDifference > 0 ? 'heroicon-m-arrow-trending-up' : ($costDifference < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->descriptionColor($costTrendColor);
        }

        $moduleSync = app(\App\Services\SubscriptionModuleSyncTracker::class)->latest($this->record->subscription_id, 'subscriptions');
        $lastSynced = $moduleSync?->last_successful_at !== null
            ? Carbon::parse($moduleSync->last_successful_at)
            : ($this->record->last_synced !== null ? Carbon::parse($this->record->last_synced) : null);
        $lastSyncedLabel = $lastSynced !== null
            ? $lastSynced->copy()->setTimezone(config('app.timezone'))->format('d M Y, H:i')
            : '—';

        [$syncAgeDescription, $syncAgeColor] = DataAge::for($lastSynced);

        return [
            Stat::make('Last Sync', $lastSyncedLabel)
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->description($syncAgeDescription)
                ->descriptionColor($syncAgeColor),
            $securityStat,
            Stat::make('Score Health', $isHealthy ? 'Healthy' : 'Unhealthy')
                ->icon($isHealthy ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                ->color($isHealthy ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => $isHealthy
                        ? 'ccod-score-health ccod-score-health--healthy border-l-4 !border-l-success-500 !bg-success-50 dark:!bg-success-950/30'
                        : 'ccod-score-health ccod-score-health--unhealthy border-l-4 !border-l-danger-500 !bg-danger-50 dark:!bg-danger-950/30',
                    'data-score-health' => $isHealthy ? 'healthy' : 'unhealthy',
                ])
                ->description($score === null ? 'Security score unavailable' : 'Based on the 80% security score threshold'),
            $costStat,
            Stat::make('Total Alerts', number_format($this->record->alerts()->count()))
                ->icon('heroicon-o-bell-alert')
                ->color('primary'),
            Stat::make('Total URLs', number_format($this->record->webUrls()->count()))
                ->icon('heroicon-o-globe-alt')
                ->color('gray'),
        ];
    }
}
