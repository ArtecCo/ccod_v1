<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\ConsolidatedCostHistory;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\ConsolidatedCostHistoryService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Livewire\Attributes\Url;

class ConsolidatedCostHistoryTable extends TableWidget
{
    #[Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    protected static bool $isLazy = true;

    protected static ?string $pollingInterval = '10s';

    protected int | string | array $columnSpan = 1;

    protected function getTableHeading(): ?string
    {
        return 'Consolidated Cost History';
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user)->get()
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, 'cost.view', $subscription));

        if (preg_match('/^project:(\d+)$/', $this->scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(fn (AzureSubscription $subscription): bool => $subscription->projects()->whereKey((int) $matches[1])->exists());
        } elseif (preg_match('/^business_unit:(\d+)$/', $this->scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(fn (AzureSubscription $subscription): bool => $subscription->projects()->where('business_unit_id', (int) $matches[1])->exists());
        } elseif ($this->scopeFilter !== 'all') {
            abort(403);
        }

        $hash = app(ConsolidatedCostHistoryService::class)->capture($subscriptions, $this->scopeFilter);

        return ConsolidatedCostHistory::query()->where('scope_hash', $hash);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')->label('Month')->date('M Y')->sortable(),
                TextColumn::make('actual_cost')->label('Actual Cost')->formatStateUsing(fn ($state, ConsolidatedCostHistory $record): string => $state === null ? '—' : (($record->currency ?? '') . ' ' . number_format((float) $state, 2)))->sortable(),
                TextColumn::make('budget_total')->label('Budget')->formatStateUsing(fn ($state, ConsolidatedCostHistory $record): string => $state === null ? '—' : (($record->currency ?? '') . ' ' . number_format((float) $state, 2))),
                TextColumn::make('forecast_total')->label('Forecast')->formatStateUsing(fn ($state, ConsolidatedCostHistory $record): string => $state === null ? '—' : (($record->currency ?? '') . ' ' . number_format((float) $state, 2))),
                TextColumn::make('mom_difference')->label('MoM Difference')->formatStateUsing(fn ($state, ConsolidatedCostHistory $record): string => $state === null ? '—' : (($state > 0 ? '+' : '') . ($record->currency ?? '') . ' ' . number_format((float) $state, 2)))->color(fn ($state): string => $state > 0 ? 'danger' : ($state < 0 ? 'success' : 'gray')),
                TextColumn::make('mom_percentage')->label('MoM Change')->formatStateUsing(fn ($state): string => $state === null ? '—' : (($state > 0 ? '+' : '') . number_format((float) $state, 2) . '%'))->sortable(),
            ])
            ->defaultSort('period_start', 'desc')
            ->paginated([12, 24, 48])
            ->defaultPaginationPageOption(12)
            ->striped(false)
            ->recordActions([]);
    }
}
