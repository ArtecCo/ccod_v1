<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\AzureAlertsRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\AzureSecurityAssessmentsRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\BillingResourcesRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\SubscriptionWebUrlsRelationManager;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionAlertSeverityOverview;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostHistoryChart;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostHistoryTable;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionModuleSyncStat;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionSecurityOverview;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionSecurityScoreHistoryChart;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionSecurityScoreHistoryTable;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use App\Livewire\Filament\Subscriptions\SubscriptionMetricsDashboard;
use App\Models\AzureSubscription;
use App\Models\Developer;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Actions\EditAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    protected ?AzureSubscription $record = null;

    public function mount(): void
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }

        // Temporary single-subscription mode: use the first accessible subscription.
        $this->record = app(AccessAuthorizationService::class)
            ->accessibleSubscriptionQuery($user)
            ->first();

        abort_unless($this->record !== null, 404);

        $requestedTab = request()->query('tab');
        $tabPermissions = [
            'Alerts' => 'alerts.view',
            'Security' => 'security.view',
            'Tickets' => 'tickets.view',
            'Web URLs' => 'urls.view',
            'Resources' => 'resources.view',
            'Overview' => null,
            'Cost' => 'cost.view',
            'Metrics' => 'resources.metrics.view',
            'metrics' => 'resources.metrics.view',
        ];
        $permission = $requestedTab !== null ? ($tabPermissions[$requestedTab] ?? null) : null;

        if ($permission !== null && ! $this->canViewTab($permission)) {
            abort(403);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Subscription Navigation')->tabs([
                $this->makeTab('Overview', null)->schema(fn (): array => [
                    Section::make('Subscription Details')
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('display_name')->label('Subscription Name')->weight('bold'),
                                TextEntry::make('subscription_id')->label('Subscription ID')->copyable()->copyMessage('Subscription ID copied'),
                                TextEntry::make('overview_business_units')
                                    ->label('Business Unit')
                                    ->state(function () {
                                        $record = $this->record;
                                        $direct = $record->businessUnits()->pluck('business_units.name');
                                        $projectUnits = $record->projects()->with('businessUnit:id,name')->get()->pluck('businessUnit.name');
                                        return $direct->merge($projectUnits)->filter()->unique()->sort()->implode(', ') ?: '—';
                                    }),
                                TextEntry::make('overview_tags')
                                    ->label('Tags')->badge()
                                    ->state(function () {
                                        $record = $this->record;
                                        return $record->projects()->with('subscriptionAssignments')->get()
                                            ->flatMap(fn ($project) => $project->subscriptionAssignments
                                                ->where('subscription_id', $record->subscription_id)
                                                ->flatMap(fn ($assignment) => (array) $assignment->tags))
                                            ->filter()->map(fn ($tag): string => strtoupper((string) $tag))
                                            ->unique()->sort()->values()->all();
                                    })->placeholder('No tags assigned'),
                                TextEntry::make('overview_user_groups')
                                    ->label('Assigned User Groups')->badge()->color('info')
                                    ->state(fn () => $this->record->projects()->with('userGroups:id,name')->get()
                                        ->flatMap(fn ($project) => $project->userGroups->pluck('name'))
                                        ->filter()->unique()->sort()->values()->all())
                                    ->placeholder('No user groups assigned')->columnSpanFull(),
                            ])->columnSpanFull(),
                        ])->columnSpanFull(),
                    Livewire::make(SubscriptionStatsOverview::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->makeTab('Cost', 'cost.view')->schema(fn (): array => array_values(array_filter([
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'cost'])->lazy()->columnSpanFull(),
                    Livewire::make(SubscriptionCostHistoryChart::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(SubscriptionCostHistoryTable::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(SubscriptionBudgetDetails::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    $this->canViewTab('cost.breakdown.view')
                        ? Livewire::make(SubscriptionResourceCostBreakdown::class, ['record' => $this->record])->lazy()->columnSpanFull()
                        : null,
                ])))->columns(2),
                $this->makeTab('Alerts', 'alerts.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionAlertSeverityOverview::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    Livewire::make(AzureAlertsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->makeTab('Security', 'security.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionSecurityOverview::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    Livewire::make(SubscriptionSecurityScoreHistoryChart::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(SubscriptionSecurityScoreHistoryTable::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(AzureSecurityAssessmentsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull(),
                ])->columns(2),
                $this->makeTab('Tickets', 'tickets.view')->schema(fn (): array => [])->columns(1),
                $this->makeTab('Web URLs', 'urls.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionModuleSyncStat::class, ['record' => $this->record, 'module' => 'web_urls', 'label' => 'Last Sync'])->lazy()->columnSpanFull(),
                    Livewire::make(SubscriptionWebUrlsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->makeTab('Resources', 'resources.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionModuleSyncStat::class, ['record' => $this->record, 'module' => 'resources', 'label' => 'Last Sync'])->lazy()->columnSpanFull(),
                    Livewire::make(BillingResourcesRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->makeTab('Metrics', 'resources.metrics.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionMetricsDashboard::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                ])->columns(1),
            ])->persistTabInQueryString()->contained(false)->extraAttributes(['class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none'])->columnSpanFull(),
        ]);
    }

    protected function makeTab(string $label, ?string $permission): Tab
    {
        $tab = Tab::make($label);
        if ($permission !== null) {
            $tab->visible(fn (): bool => $this->canViewTab($permission));
        }
        return $tab;
    }

    protected function canViewTab(string $permission): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer instanceof Developer && $developer->is_active) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, $permission, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(fn (): bool => AzureSubscriptionResource::canEdit($this->record)),
        ];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
