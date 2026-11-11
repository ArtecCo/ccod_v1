<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;


use App\Models\AzureSubscription;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResourceCostBreakdown extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Resource Cost Breakdown';

    protected function getTableQuery(): Builder
    {
        return $this->record
            ->billingResources()
            ->getQuery()
            ->select([
                'resource_id',
                'subscription_id',
                'name',
                'resource_type',
                'region',
                'cost_eur',
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Resource Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('resource_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('region')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cost_eur')
                    ->label('Cost')
                    ->money('EUR')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('resource_type')
                    ->label('Resource Type')
                    ->options(fn (): array => $this->record->billingResources()
                        ->whereNotNull('resource_type')
                        ->distinct()
                        ->orderBy('resource_type')
                        ->pluck('resource_type', 'resource_type')
                        ->all()),
                SelectFilter::make('region')
                    ->label('Location')
                    ->options(fn (): array => $this->record->billingResources()
                        ->whereNotNull('region')
                        ->distinct()
                        ->orderBy('region')
                        ->pluck('region', 'region')
                        ->all()),
            ])
            ->groups([
                Group::make('resource_type')->label('Resource Type'),
                Group::make('region')->label('Location'),
            ])
            ->defaultSort('name')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExportCost()),
            ]);
    }

    protected function canExportCost(): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'cost.export', $this->record);
    }
}
