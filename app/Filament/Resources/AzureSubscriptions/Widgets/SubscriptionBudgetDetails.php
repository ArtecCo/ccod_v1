<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;


use App\Livewire\Concerns\HasLoadingPlaceholder;
use App\Models\AzureBudget;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionBudgetDetails extends BaseWidget
{
    use HasLoadingPlaceholder;

    protected static string $loadingPlaceholderVariant = 'table';
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Budget Details';

    private bool $subscriptionForecastResolved = false;

    private ?float $cachedSubscriptionForecastTotal = null;

    protected function getTableQuery(): Builder
    {
        return $this->record->budgets()->getQuery();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget_name')
                    ->label('Budget')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Budget Amount')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('current_spend')
                    ->label('Actual Spend')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('forecast_spend')
                    ->label('Forecast')
                    ->state(fn (AzureBudget $record): ?float => $record->forecast_spend !== null
                        ? (float) $record->forecast_spend
                        : $this->subscriptionForecastTotal())
                    ->money('EUR')
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
            ->defaultSort('budget_name')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExportCost()),
            ]);
    }

    private function subscriptionForecastTotal(): ?float
    {
        if ($this->subscriptionForecastResolved) {
            return $this->cachedSubscriptionForecastTotal;
        }

        $this->subscriptionForecastResolved = true;

        $forecast = $this->record->costForecast;

        if ($forecast?->forecast_amount === null) {
            return null;
        }

        $latestAmount = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderByDesc('period_start')
            ->value('amount');

        $this->cachedSubscriptionForecastTotal = ($latestAmount !== null ? (float) $latestAmount : 0.0)
            + (float) $forecast->forecast_amount;

        return $this->cachedSubscriptionForecastTotal;
    }

    protected function canExportCost(): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'cost.export', $this->record);
    }
}
