<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\AzureSecurityScore;
use App\Models\BillingResource;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Url;

class ConsolidatedDashboardTable extends BaseWidget
{
    #[Url(as: 'scope', history: true)]
    public string $filter = 'all';

    #[Reactive]
    public string $activeTab = 'overview';

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return match ($this->activeTab) {
            'overview' => 'Subscriptions',
            'cost' => 'Monthly Cost History',
            'alerts' => 'Alerts',
            'security', 'security_high' => 'High Severity Recommendations',
            'security_scores' => 'Subscriptions with Security Score Below 80%',
            'resources' => 'Resources',
            default => 'Dashboard Data',
        };
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn (Builder $query): Builder => $query->whereKey((int) $matches[1]));
        } elseif (preg_match('/^business_unit:(\\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn (Builder $query): Builder => $query->where('business_unit_id', (int) $matches[1]));
        } elseif ($this->filter !== 'all') {
            abort(403);
        }

        $models = $subscriptions->get();
        $permission = match ($this->activeTab) {
            'cost' => 'cost.view',
            'alerts' => 'alerts.view',
            'security', 'security_scores', 'security_high' => 'security.view',
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
                ->whereIn('severity', ['Sev0', 'Sev1'])
                ->with(['subscription', 'details'])->orderByDesc('fire_time'),
            'security', 'security_high' => AzureSecurityAssessment::query()->whereIn('subscription_id', $ids)
                ->where('state', 'Unhealthy')->where('severity', 'High')
                ->with('subscription')->orderByDesc('synced_at'),
            'security_scores' => AzureSecurityScore::query()->whereIn('subscription_id', $ids)
                ->whereIn('id', function ($query) use ($ids): void {
                    $query->selectRaw('MAX(id)')->from('azure_security_scores')
                        ->whereIn('subscription_id', $ids)->groupBy('subscription_id');
                })->whereRaw('(percentage_score * 100) < 80')->with('subscription')
                ->orderBy('percentage_score'),
            'resources' => BillingResource::query()->whereIn('subscription_id', $ids)
                ->with('subscription')->orderBy('name'),
            default => AzureSubscription::query()->whereIn('subscription_id', $models->pluck('subscription_id')),
        };
    }

    protected function table(Table $table): Table
    {
        $table = parent::table($table);

        if ($this->activeTab !== 'alerts') {
            return $table;
        }

        return $table
            ->recordAction('viewAlert')
            ->recordActions([
                $this->alertDetailAction('viewAlert', 'View'),
            ]);
    }

    private function alertDetailAction(string $name, string $label): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn (AzureAlert $record): string => $record->name ?: 'Alert details')
            ->modalWidth('7xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(fn (AzureAlert $record) => view('filament.pages.widgets.alert-details-modal', [
                'record' => $record,
                'detail' => $record->details,
            ]))
            ->visible(fn (AzureAlert $record): bool => $this->canViewAlert($record));
    }

    private function canViewAlert(AzureAlert $record): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User
            && $record->subscription !== null
            && app(AccessAuthorizationService::class)->hasPermission($user, 'alerts.view', $record->subscription);
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
                TextColumn::make('name')->label('Alert')->searchable()->wrap()
                    ->action(fn (AzureAlert $record): Action => $this->alertDetailAction('openAlertFromName', 'View alert')),
                TextColumn::make('severity')->badge()->color(fn (?string $state): string => match ($state) {
                    'Sev0', 'Sev1' => 'danger',
                    'Sev2', 'Sev3' => 'warning',
                    default => 'gray',
                }),
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('affected_resource')->label('Affected resource')->searchable()->wrap(),
                TextColumn::make('fire_time')->label('Fired')->dateTime()->sortable(),
            ],
            'security', 'security_high' => [
                TextColumn::make('recommendation_name')->label('Recommendation')->searchable()->wrap(),
                TextColumn::make('state')->badge()->color(fn (?string $state): string => $state === 'Unhealthy' ? 'danger' : 'success'),
                TextColumn::make('severity')->badge(),
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('resource_id')->label('Resource')->searchable()->toggleable(),
                TextColumn::make('synced_at')->label('Last synced')->dateTime()->sortable(),
            ],
            'security_scores' => [
                TextColumn::make('subscription.display_name')->label('Subscription')->searchable(),
                TextColumn::make('initiative_name')->label('Initiative')->searchable()->placeholder('Overall score'),
                TextColumn::make('percentage_score')->label('Security Score')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state * 100, 2) . '%')
                    ->badge()->color('danger')->sortable(),
                TextColumn::make('captured_at')->label('Captured')->dateTime()->sortable(),
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
