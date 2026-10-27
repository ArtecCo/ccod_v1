<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\AzureAlertsRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\SubscriptionWebUrlsRelationManager;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionAlertSeverityOverview;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use App\Models\AzureSubscription;
use Filament\Infolists\Components\Section;
use Filament\Pages\Concerns\InteractsWithRecord;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ViewAzureSubscription extends Page
{
    use InteractsWithRecord;

    protected static string $resource = AzureSubscriptionResource::class;

    protected string $view = 'filament.resources.azure-subscriptions.pages.view-azure-subscription';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->authorizeRecordAccess();
    }

    protected function resolveRecord(int|string $record): AzureSubscription
    {
        return AzureSubscriptionResource::getEloquentQuery()->findOrFail($record);
    }

    protected function authorizeRecordAccess(): void
    {
        abort_unless(AzureSubscriptionResource::canView($this->record), 403);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Subscription Navigation')->tabs([
                $this->makeTab('Overview', null)->schema(fn (): array => [Section::make('Subscription Details')->schema([$this->getInfolistContentComponent()->columnSpanFull()])->columnSpanFull(), Livewire::make(SubscriptionStatsOverview::class, ['record' => $this->record])->columnSpanFull()])->columns(1),
                $this->makeTab('Cost', 'cost.view')->schema(fn (): array => array_values(array_filter([
                    Livewire::make(SubscriptionCostSummary::class, ['record' => $this->record])->columnSpanFull(),
                    Livewire::make(SubscriptionBudgetDetails::class, ['record' => $this->record])->columnSpanFull(),
                    $this->canViewTab('cost.breakdown.view')
                        ? Livewire::make(SubscriptionResourceCostBreakdown::class, ['record' => $this->record])->columnSpanFull()
                        : null,
                ])))->columns(1),
                $this->makeTab('Alerts', 'alerts.view')->schema(fn (): array => [Livewire::make(SubscriptionAlertSeverityOverview::class, ['record' => $this->record])->columnSpanFull(), Livewire::make(AzureAlertsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->columnSpanFull()])->columns(1),
                $this->makeTab('Security', 'security.view')->schema(fn (): array => [])->columns(1),
                $this->makeTab('Tickets', 'tickets.view')->schema(fn (): array => [])->columns(1),
                $this->makeTab('Web URLs', 'urls.view')->schema(fn (): array => [Livewire::make(SubscriptionWebUrlsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->columnSpanFull()])->columns(1),
                $this->makeTab('Resources', 'resources.view')->schema(fn (): array => [$this->getRelationManagersContentComponent()->columnSpanFull()])->columns(1),
            ])->persistTabInQueryString()->contained(false)->extraAttributes(['class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none'])->columnSpanFull(),
        ]);
    }

    protected function makeTab(string $label, ?string $permission): Tab
    {
        $tab = Tab::make($label);
        if ($permission !== null) $tab->visible(fn (): bool => $this->canViewTab($permission));
        return $tab;
    }

    protected function canViewTab(string $permission): bool
    {
        return AzureSubscriptionResource::canViewTab($permission, $this->record);
    }
}
