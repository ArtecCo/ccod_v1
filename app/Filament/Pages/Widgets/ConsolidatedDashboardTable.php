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
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
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

    public function table(Table $table): Table
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

    private function alertDetailAction(string $name, string $label): ViewAction
    {
        return ViewAction::make($name)
            ->label($label)
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn (AzureAlert $record): string => $record->name ?: 'Alert details')
            ->modalWidth('7xl')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->schema([
                Section::make('Alert evaluation')
                    ->icon('heroicon-o-magnifying-glass')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')->label('Alert title')->icon('heroicon-o-bell')->columnSpanFull(),
                        TextEntry::make('severity')->badge()->color(fn (?string $state): string => match ($state) { 'Sev0', 'Sev1' => 'danger', 'Sev2', 'Sev3' => 'warning', default => 'gray' }),
                        TextEntry::make('azure_alert_id')->label('Alert ID')->icon('heroicon-o-identification')->copyable(),
                        TextEntry::make('alert_condition')->label('Alert condition')->badge(),
                        TextEntry::make('user_response')->label('User response')->placeholder('—'),
                        TextEntry::make('fire_time')->label('Fired time')->dateTime(),
                        TextEntry::make('evaluation_start')->label('Evaluation start')->state(fn (AzureAlert $record) => $this->alertEvaluationStart($record))->dateTime()->placeholder('—'),
                        TextEntry::make('evaluation_end')->label('Evaluation end')->state(fn (AzureAlert $record) => $this->alertEvaluationEnd($record))->dateTime()->placeholder('—'),
                        TextEntry::make('aggregation')->label('Time aggregation')->state(fn (AzureAlert $record) => $this->alertAggregation($record))->placeholder('—'),
                        TextEntry::make('rule')->label('Alert rule')->state(fn (AzureAlert $record) => $this->alertRule($record))->placeholder('—'),
                        TextEntry::make('value')->label('Value')->state(fn (AzureAlert $record) => $this->alertValue($record))->placeholder('—'),
                        TextEntry::make('threshold')->label('Threshold')->state(fn (AzureAlert $record) => $this->alertThreshold($record))->placeholder('—'),
                        TextEntry::make('deviation')->label('Deviation')->state(fn (AzureAlert $record) => $this->alertDeviation($record))->placeholder('—'),
                    ]),
                Section::make('Basic alert details')
                    ->icon('heroicon-o-information-circle')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('monitor_service')->label('Monitor service')->icon('heroicon-o-cog-6-tooth'),
                        TextEntry::make('signal_type')->label('Signal type')->icon('heroicon-o-signal'),
                        TextEntry::make('subscription.display_name')->label('Subscription')->icon('heroicon-o-cloud'),
                        TextEntry::make('affected_resource')->label('Affected resource')->icon('heroicon-o-computer-desktop')->copyable(),
                        TextEntry::make('target_resource_group')->label('Resource group')->icon('heroicon-o-folder'),
                        TextEntry::make('target_resource_type')->label('Target resource type')->icon('heroicon-o-cube'),
                        TextEntry::make('suppression_status')->label('Suppression status')->icon('heroicon-o-bell-slash'),
                        TextEntry::make('last_modified_time')->label('Last modified')->dateTime()->icon('heroicon-o-clock'),
                    ]),
            ])
            ->visible(fn (AzureAlert $record): bool => $this->canViewAlert($record));
    }

    private function alertConditionContainerData(AzureAlert $record): array
    {
        $raw = $record->details?->raw_properties ?? [];
        foreach ([
            'context.context.condition',
            'context.condition',
            'alertContext.context.condition',
            'alertContext.condition',
        ] as $path) {
            $condition = data_get($raw, $path);
            if (is_array($condition) && $condition !== []) {
                return $condition;
            }
        }

        return [];
    }

    private function alertConditionData(AzureAlert $record): array
    {
        $condition = $this->alertConditionContainerData($record);
        $allOf = data_get($condition, 'allOf.0');
        if (is_array($allOf) && $allOf !== []) {
            return $allOf;
        }

        return $condition;
    }

    private function alertContextData(AzureAlert $record): array
    {
        $raw = $record->details?->raw_properties ?? [];
        return data_get($raw, 'context.context')
            ?? data_get($raw, 'context')
            ?? data_get($raw, 'alertContext.context')
            ?? data_get($raw, 'alertContext')
            ?? [];
    }

    private function alertEvaluationStart(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionContainerData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'windowStartTime')
            ?? data_get($condition, 'evaluationStartTime')
            ?? data_get($condition, 'window.startTime')
            ?? data_get($context, 'windowStartTime')
            ?? data_get($context, 'evaluationStartTime')
            ?? data_get($context, 'condition.windowStartTime');
    }

    private function alertEvaluationEnd(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionContainerData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'windowEndTime')
            ?? data_get($condition, 'evaluationEndTime')
            ?? data_get($condition, 'window.endTime')
            ?? data_get($context, 'windowEndTime')
            ?? data_get($context, 'evaluationEndTime')
            ?? data_get($context, 'condition.windowEndTime');
    }

    private function alertAggregation(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        return data_get($condition, 'timeAggregation') ?? data_get($condition, 'aggregation');
    }

    private function alertRule(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'metricName') ?? data_get($condition, 'alertRule') ?? data_get($context, 'alertRule');
    }

    private function alertValue(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        return data_get($condition, 'metricValue') ?? data_get($condition, 'value') ?? data_get($condition, 'searchResult');
    }

    private function alertThreshold(AzureAlert $record): mixed
    {
        return data_get($this->alertConditionData($record), 'threshold');
    }

    private function alertDeviation(AzureAlert $record): mixed
    {
        $value = $this->alertValue($record);
        $threshold = $this->alertThreshold($record);
        return is_numeric($value) && is_numeric($threshold) ? number_format((float) $value - (float) $threshold, 2) : null;
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
