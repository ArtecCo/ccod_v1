<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\ConsolidatedDashboardStats;
use App\Filament\Pages\Widgets\ConsolidatedDashboardTable;
use App\Filament\Pages\Widgets\ConsolidatedCostHistoryChart;
use App\Filament\Pages\Widgets\ConsolidatedCostHistoryTable;
use App\Filament\Pages\Widgets\ConsolidatedWebUrlStats;
use App\Filament\Pages\Widgets\ConsolidatedWebUrlsTable;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Models\AzureSubscription;
use App\Models\Developer;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    protected ?AzureSubscription $record = null;

    protected bool $hasProjectAccess = false;

    public function mount(): void
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }

        $authorization = app(AccessAuthorizationService::class);
        $this->hasProjectAccess = $authorization->hasAnyResourceAccess($user);

        // Users without an accessible subscription still reach the dashboard.
        $this->record = $authorization->accessibleSubscriptionQuery($user)->first();

        if ($this->record === null) {
            return;
        }

        $requestedTab = request()->query('tab');
        $tabPermissions = [
            'Alerts' => 'alerts.view',
            'Security' => 'security.view',
            'Web URLs' => 'urls.view',
            'Overview' => null,
            'Cost' => 'cost.view',
        ];
        $permission = $requestedTab !== null ? ($tabPermissions[$requestedTab] ?? null) : null;

        if ($permission !== null && ! $this->canViewTab($permission)) {
            abort(403);
        }
    }

    public function content(Schema $schema): Schema
    {
        if ($this->record === null) {
            $hasProjects = $this->hasProjectAccess;

            return $schema->components([
                Section::make($hasProjects ? 'No subscriptions assigned yet' : 'No projects assigned yet')
                    ->description(
                        $hasProjects
                            ? 'Your projects do not currently have any Azure subscriptions assigned. Please contact your administrator for access.'
                            : 'Your user group is not currently assigned to any projects. Please contact your administrator to get access.'
                    )
                    ->icon('heroicon-o-folder-open')
                    ->iconColor('gray')
                    ->extraAttributes([
                        'class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none !px-0 !py-12 text-center [&_.fi-section-header]:justify-center [&_.fi-section-header]:text-center [&_.fi-section-content]:text-center [&_.fi-section-header-heading]:text-xl [&_.fi-section-header-description]:max-w-2xl [&_.fi-section-header-description]:mx-auto',
                    ])
                    ->columnSpanFull(),
            ]);
        }

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
                    Livewire::make(ConsolidatedCostHistoryChart::class)->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedCostHistoryTable::class)->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedResourceCostPieChart::class)->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedSubscriptionCostBarChart::class)->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedResourceCostBreakdown::class)->lazy()->columnSpanFull(),
                ])))->columns(2),
                $this->makeTab('Alerts', 'alerts.view')->schema(fn (): array => [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'alerts'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'alerts'])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->makeTab('Security', 'security.view')->schema(fn (): array => [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'security'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'security'])->lazy()->columnSpanFull(),
                ])->columns(1),
                // TEMPORARILY HIDDEN: Tickets tab. Restore by uncommenting this line.
                // $this->makeTab('Tickets', 'tickets.view')->schema(fn (): array => [])->columns(1),
                $this->makeTab('Web URLs', 'urls.view')->schema(fn (): array => [
                    Livewire::make(ConsolidatedWebUrlStats::class)->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedWebUrlsTable::class)->lazy()->columnSpanFull(),
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
        if (! $user instanceof User) {
            return false;
        }

        $authorization = app(AccessAuthorizationService::class);

        // Consolidated cost access is granted when the user can view costs
        // for at least one subscription in their authorized subscription scope.
        if (in_array($permission, ['cost.view', 'cost.breakdown.view', 'alerts.view', 'security.view', 'urls.view'], true)) {
            return $authorization->accessibleSubscriptionQuery($user)->get()
                ->contains(fn (AzureSubscription $subscription): bool =>
                    $authorization->hasPermission($user, $permission, $subscription)
                );
        }

        return $authorization->hasPermission($user, $permission, $this->record);
    }

    protected function getHeaderActions(): array
    {
        if ($this->record === null) {
            return [];
        }

        return [
            EditAction::make()->visible(fn (): bool => AzureSubscriptionResource::canEdit($this->record)),
        ];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
