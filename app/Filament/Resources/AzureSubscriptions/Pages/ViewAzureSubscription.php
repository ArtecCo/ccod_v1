<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ViewAzureSubscription extends ViewRecord
{
    protected static string $resource = AzureSubscriptionResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Subscription Navigation')
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                Section::make('Subscription Details')
                                    ->schema([
                                        $this->getInfolistContentComponent()
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionStatsOverview::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Security')
                            ->schema([]),

                        Tab::make('Cost')
                            ->schema([
                                Livewire::make(SubscriptionCostSummary::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionBudgetDetails::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionResourceCostBreakdown::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Resources')
                            ->schema([
                                $this->getRelationManagersContentComponent()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Tickets')
                            ->schema([]),

                        Tab::make('Alerts')
                            ->schema([]),
                    ])
                    ->persistTabInQueryString()
                    ->contained(false)
                    ->extraAttributes([
                        'class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
