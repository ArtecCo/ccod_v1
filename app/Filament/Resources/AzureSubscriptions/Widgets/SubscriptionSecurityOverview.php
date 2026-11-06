<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSecurityScore;
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
        $assessmentStats = $this->record->securityAssessments()
            ->where('state', 'Unhealthy')
            ->selectRaw(
                'COUNT(*) AS unhealthy_count,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS high_count,
                 MAX(synced_at) AS last_synced',
                ['High'],
            )
            ->first();

        $unhealthy = (int) ($assessmentStats?->unhealthy_count ?? 0);
        $high = (int) ($assessmentStats?->high_count ?? 0);
        $lastSynced = $assessmentStats?->last_synced;

        // Fetch the latest initiative's score history in one round trip.
        // MySQL's null-safe equality preserves the case where the latest
        // snapshot has a null initiative name.
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
        $scorePercentage = $latestScore !== null
            ? round(((float) $latestScore->percentage_score) * 100, 2)
            : null;

        $scoreHistory = $latestScore !== null && $latestScore->initiative_name !== null
            ? $scoreSnapshots->sortBy('captured_at')->values()
            : collect();

        $previousScore = $scoreHistory->count() > 1
            ? $scoreHistory->get($scoreHistory->count() - 2)
            : null;

        $scoreDifference = $latestScore !== null && $previousScore !== null && (float) $previousScore->percentage_score !== 0.0
            ? ((((float) $latestScore->percentage_score - (float) $previousScore->percentage_score) / abs((float) $previousScore->percentage_score)) * 100)
            : null;

        $scoreChart = $scoreHistory
            ->map(fn (AzureSecurityScore $score): float => round(((float) $score->percentage_score) * 100, 2))
            ->all();

        $trendColor = $scoreDifference === null ? 'gray' : ($scoreDifference >= 0 ? 'success' : 'danger');

        $lastSyncedLabel = $lastSynced !== null
            ? Carbon::parse($lastSynced)->setTimezone(config('app.timezone'))->format('d M Y, H:i')
            : '—';

        $securityScore = Stat::make('Security Score', $scorePercentage !== null ? $scorePercentage . '%' : '—')
            ->icon('heroicon-o-shield-check')
            ->color('gray');

        if (count($scoreChart) >= 2) {
            $securityScore = $securityScore->chart($scoreChart)->chartColor($trendColor);
        }

        if ($scoreDifference !== null) {
            $securityScore = $securityScore
                ->description(($scoreDifference >= 0 ? '+' : '') . number_format($scoreDifference, 1) . '% vs previous score')
                ->descriptionIcon($scoreDifference >= 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($trendColor);
        }

        return [
            $securityScore,
            Stat::make('Unhealthy', (string) $unhealthy)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($unhealthy > 0 ? 'danger' : 'success'),
            Stat::make('High Recommendations', (string) $high)
                ->icon('heroicon-o-shield-exclamation')
                ->color($high > 0 ? 'danger' : 'success'),
            Stat::make('Last Sync', $lastSyncedLabel)
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
        ];
    }
}
