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
        $query = $this->record->securityAssessments();

        $unhealthy = (clone $query)->where('state', 'Unhealthy')->count();
        $healthy = (clone $query)->where('state', 'Healthy')->count();
        $total = (clone $query)->count();
        $lastEvaluated = (clone $query)->max('first_evaluated_at');
        $lastChanged = (clone $query)->max('status_changed_at');

        $lastEvaluatedLabel = $lastEvaluated
            ? Carbon::parse($lastEvaluated)->format('d M Y, H:i')
            : '—';

        $lastChangedLabel = $lastChanged
            ? 'Last status change: ' . Carbon::parse($lastChanged)->format('d M Y, H:i')
            : 'No evaluation timestamp available';

        return [
            Stat::make('Security Recommendations', (string) $total)
                ->description('All Defender for Cloud assessments')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->extraAttributes(['class' => 'ccod-security-stat']),
            Stat::make('Unhealthy', (string) $unhealthy)
                ->description('Recommendations requiring attention')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($unhealthy > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'ccod-security-stat']),
            Stat::make('Healthy', (string) $healthy)
                ->description('Assessments currently healthy')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->extraAttributes(['class' => 'ccod-security-stat']),
            Stat::make('Last Evaluated', $lastEvaluatedLabel)
                ->description($lastChangedLabel)
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->extraAttributes(['class' => 'ccod-security-stat']),
        ];
    }
}
