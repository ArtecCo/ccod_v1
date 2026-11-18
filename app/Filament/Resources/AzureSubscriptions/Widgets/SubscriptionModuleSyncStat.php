<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Services\SubscriptionModuleSyncTracker;
use App\Support\DataAge;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionModuleSyncStat extends BaseWidget
{
    public AzureSubscription $record;

    public string $module;

    public string $label = 'Last Sync';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $sync = app(SubscriptionModuleSyncTracker::class)->latest($this->record->subscription_id, $this->module);
        $lastSynced = $sync?->last_successful_at !== null ? Carbon::parse($sync->last_successful_at) : null;
        $label = $lastSynced?->copy()->setTimezone(config('app.timezone'))->format('d M Y, H:i') ?? '—';
        [$description, $color] = DataAge::for($lastSynced);
        if (($sync?->status ?? null) === 'failed' || $lastSynced === null) {
            [$description, $color] = DataAge::for(null);
        }

        return [
            Stat::make($this->label, $label)
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->description($description)
                ->descriptionColor($color),
        ];
    }
}
