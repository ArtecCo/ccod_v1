<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                                Section::make('Cost Summary')
                                    ->schema([
                                        Livewire::make(CostSummary::class, [
                                            'record' => $this->record,
                                        ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Budget Details')
                                    ->schema([
                                        Table::make('Budget Details')
                                            ->query(fn (): Builder => $this->record->budgets())
                                            ->columns([
                                                TextColumn::make('budget_name')
                                                    ->label('Budget')
                                                    ->searchable()
                                                    ->sortable(),
                                                TextColumn::make('amount')
                                                    ->label('Budget Amount')
                                                    ->money(fn ($record): string => $record->currency ?? 'EUR')
                                                    ->sortable(),
                                                TextColumn::make('current_spend')
                                                    ->label('Actual Spend')
                                                    ->money(fn ($record): string => $record->currency ?? 'EUR')
                                                    ->sortable(),
                                                TextColumn::make('forecast_spend')
                                                    ->label('Forecast')
                                                    ->money(fn ($record): string => $record->currency ?? 'EUR')
                                                    ->sortable(),
                                                TextColumn::make('time_grain')
                                                    ->label('Period')
                                                    ->sortable(),
                                                TextColumn::make('start_date')
                                                    ->date()
                                                    ->sortable(),
                                                TextColumn::make('end_date')
                                                    ->date()
                                                    ->sortable(),
                                            ])
                                            ->paginated([10, 25, 50])
                                            ->defaultSort('budget_name')
                                            ->striped(false)
                                            ->recordActions([])
                                            ->toolbarActions([]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Resource Cost Breakdown')
                                    ->schema([
                                        Livewire::make(CostResourceBreakdown::class, [
                                            'record' => $this->record,
                                        ])
                                            ->columnSpanFull(),
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
                        'class' => '!bg-transparent !border-0 !shadow-none !ring-0 !shadow-none !rounded-none',
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
