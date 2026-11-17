<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\BillingResource;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Reactive;

class ConsolidatedDashboardTable extends BaseWidget
{
    #[Reactive]
    public string $filter = 'all';

    #[Reactive]
    public string $activeTab = 'overview';

    protected static bool $isLazy = true;

    protected int | string | array | null $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn (Builder $query): Builder => $query->whereKey((int) $matches[1]));
        } elseif (preg_match('/^business_unit:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn (Builder $query): Builder => $query->where('business_unit_id', (int) $matches[1]));
        } elseif ($this->filter !== 'all') {
            abort(403);
        }

        $models = $subscriptions->get();
        $permission = match ($this->activeTab) {
            'cost' => 'cost.view',
            'alerts' => 'alerts.view',
            'security' => 'security.view',
            'resources' => 'resources.view',
            default => null,
        };

        if ($permission === null) {
            return AzureSubscription::query()
                ->whereIn('subscription_id', $models->pluck('subscription_id'))
                ->with('projects.businessUnit')
                ->orderBy('display_name');
        }

        $ids = $models
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription))
            ->pluck('subscription_id')->values()->all();

        return match ($this->activeTab) {
            'cost' => AzureSubscriptionCost::query()->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')->with('subscription')->orderByDesc('period_start'),
            'alerts' => AzureAlert::query()->whereIn('subscription_id', $ids)
                ->with('subscription')->orderByDesc('fire_time'),
            'security' => AzureSecurityAssessment::query()->whereIn('subscription_id', $ids)
                ->with('subscription')->orderByDesc('synced_at'),
            'resources' => BillingResource::query()->whereIn('subscription_id', $ids)
                ->with('subscription')->orderBy('name'),
            default => AzureSubscription::query()->whereIn('subscription_id', $models->pluck('subscription_id')),
        };
    }

    protected function getTableColumns(): array
    {
        return match ($this->activeTab) {
            'cost' => [
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('period_start')->label('Period')->date()->sortable(),
                TextColumn::make('amount')->numeric(decimalPlaces: 2)->sortable(),
                TextColumn::make('currency')->badge(),
                TextColumn::make('granularity')->badge(),
                TextColumn::make('synced_at')->label('Last synced')->dateTime()->sortable(),
            ],
            'alerts' => [
                TextColumn::make('name')->label('Alert')->searchable()->wrap(),
                TextColumn::make('severity')->badge()->color(fn (?string $state): string => match ($state) {
                    'Sev0', 'Sev1' => 'danger',
                    'Sev2', 'Sev3' => 'warning',
                    default => 'gray',
                }),
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('affected_resource')->label('Affected resource')->searchable()->wrap(),
                TextColumn::make('fire_time')->label('Fired')->dateTime()->sortable(),
            ],
            'security' => [
                TextColumn::make('recommendation_name')->label('Recommendation')->searchable()->wrap(),
                TextColumn::make('state')->badge()->color(fn (?string $state): string => $state === 'Unhealthy' ? 'danger' : 'success'),
                TextColumn::make('severity')->badge(),
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('resource_id')->label('Resource')->searchable()->toggleable(),
                TextColumn::make('synced_at')->label('Last synced')->dateTime()->sortable(),
            ],
            'resources' => [
                TextColumn::make('name')->label('Resource')->searchable()->wrap(),
                TextColumn::make('resource_type')->label('Type')->badge()->searchable(),
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('region')->searchable(),
                TextColumn::make('sku')->toggleable(),
                TextColumn::make('cost_eur')->label('Cost (EUR)')->numeric(decimalPlaces: 2)->sortable(),
            ],
            default => [
                TextColumn::make('display_name')->label('Subscription')->searchable()->sortable(),
                TextColumn::make('projects.name')->label('Projects')->listWithLineBreaks(),
                TextColumn::make('health_status')->label('Health')->badge(),
                TextColumn::make('state')->label('State')->badge(),
                TextColumn::make('mtd_spend_eur')->label('MTD Spend (EUR)')->numeric(decimalPlaces: 2),
            ],
        };
    }

}
