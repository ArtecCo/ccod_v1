<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Support\DataAge;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SubscriptionsPageSyncStat extends BaseWidget
{
    protected static bool $isLazy = true;
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 1;

    protected function getStats(): array
    {
        $subscriptionIds = AzureSubscriptionResource::getEloquentQuery()->pluck('subscription_id')->all();

        $syncs = DB::table('subscription_module_syncs')
            ->where('module', 'subscriptions')
            ->whereIn('subscription_id', $subscriptionIds)
            ->get();

        $lastSuccessfulAt = $syncs->pluck('last_successful_at')->filter()
            ->map(fn ($timestamp) => Carbon::parse($timestamp))
            ->sortByDesc(fn (Carbon $timestamp) => $timestamp->timestamp)->first();

        $lastAttemptAt = $syncs->pluck('last_started_at')->filter()
            ->map(fn ($timestamp) => Carbon::parse($timestamp))
            ->sortByDesc(fn (Carbon $timestamp) => $timestamp->timestamp)->first();

        $syncStatus = $syncs->contains(fn ($sync): bool => $sync->status === 'partial')
            ? 'partial'
            : ($syncs->contains(fn ($sync): bool => $sync->status === 'failed') ? 'failed' : null);

        [$dataAge, $ageColor] = DataAge::for($lastSuccessfulAt, $syncStatus);

        return [
            Stat::make('Last Subscription Sync', $lastAttemptAt?->copy()->setTimezone(config('app.timezone'))->format('d M Y, H:i') ?? 'Never')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->description($dataAge)
                ->descriptionColor($ageColor),
        ];
    }
}
