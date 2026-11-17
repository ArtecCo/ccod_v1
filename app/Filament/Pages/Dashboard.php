<?php

namespace App\\Filament\\Pages;

use App\\Livewire\\Dashboard\\ConsolidatedDashboard;
use App\\Filament\\Pages\\Widgets\\ConsolidatedDashboardStats;
use App\\Filament\\Pages\\Widgets\\ConsolidatedSeverityChart;
use App\\Filament\\Pages\\Widgets\\ConsolidatedDashboardTable;
use App\\Filament\\Pages\\Widgets\\ConsolidatedCostHistoryChart;
use App\\Filament\\Pages\\Widgets\\ConsolidatedCostHistoryTable;
use App\\Models\\AzureSubscription;
use App\\Models\\User;
use App\\Services\\AccessAuthorizationService;
use Filament\\Actions\\Action;
use Filament\\Pages\\Dashboard as BaseDashboard;
use Filament\\Schemas\\Components\\Livewire;
use Filament\\Schemas\\Components\\Tabs;
use Filament\\Schemas\\Components\\Tabs\\Tab;
use Filament\\Schemas\\Schema;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        abort_unless(auth()->guard('web')->user() instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Dashboard Navigation')->tabs([
                Tab::make('Overview')->schema([
                    Livewire::make(ConsolidatedDashboard::class)->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'overview'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'overview'])->lazy()->columnSpanFull(),
                ])->columns(1),
                $this->dashboardTab('Cost', 'cost.view', 'cost', [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'cost'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedCostHistoryChart::class)->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedCostHistoryTable::class)->lazy()->columnSpan(1),
                ], 2),
                $this->dashboardTab('Alerts', 'alerts.view', 'alerts', [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'alerts'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedSeverityChart::class, ['activeTab' => 'alerts'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'alerts'])->lazy()->columnSpanFull(),
                ], 1),
                $this->dashboardTab('Security', 'security.view', 'security', [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'security'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedSeverityChart::class, ['activeTab' => 'security'])->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'security_scores'])->lazy()->columnSpan(1),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'security_high'])->lazy()->columnSpanFull(),
                ], 2),
                $this->dashboardTab('Resources', 'resources.view', 'resources', [
                    Livewire::make(ConsolidatedDashboardStats::class, ['activeTab' => 'resources'])->lazy()->columnSpanFull(),
                    Livewire::make(ConsolidatedDashboardTable::class, ['activeTab' => 'resources'])->lazy()->columnSpanFull(),
                ], 1),
            ])->persistTabInQueryString()->contained(false)
                ->extraAttributes(['class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none'])
                ->columnSpanFull(),
        ]);
    }

    protected function dashboardTab(string $label, string $permission, string $key, array $components, int $columns): Tab
    {
        return Tab::make($label)
            ->visible(function () use ($permission): bool {
                $user = auth()->guard('web')->user();
                if (! $user instanceof User) return false;

                $authorization = app(AccessAuthorizationService::class);
                return $authorization->accessibleSubscriptionQuery($user)->get()
                    ->contains(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription));
            })
            ->schema(array_merge([
                Livewire::make(ConsolidatedDashboard::class)->lazy()->columnSpanFull(),
            ], $components))
            ->columns($columns);
    }

    public function getWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('myProfile')->label('My Profile')->icon('heroicon-o-user-circle')->url(fn (): string => Profile::getUrl())->button(),
            Action::make('notifications')->label('Notifications')->icon('heroicon-o-bell')->url(fn (): string => ClientNotifications::getUrl())->button(),
        ];
    }
}
