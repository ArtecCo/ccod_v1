<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionSecurityScoreHistoryTable extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Security Score History Data';

    protected function getTableQuery(): Builder
    {
        $latestInitiative = AzureSecurityScore::query()
            ->select('initiative_name')
            ->where('subscription_id', $this->record->subscription_id)
            ->orderByDesc('captured_at')
            ->limit(1);

        $latestInitiativeSql = $latestInitiative->toSql();
        $latestInitiativeBindings = $latestInitiative->getBindings();

        return AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('captured_at', '>=', now()->subDays(6)->startOfDay())
            // Preserve the previous behavior when the latest snapshot has a
            // null initiative (or no snapshot exists): show all recent rows.
            ->where(function (Builder $query) use ($latestInitiativeSql, $latestInitiativeBindings): void {
                $query->whereRaw(
                    "initiative_name = ({$latestInitiativeSql})",
                    $latestInitiativeBindings,
                )->orWhereRaw(
                    "({$latestInitiativeSql}) IS NULL",
                    $latestInitiativeBindings,
                );
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('captured_at')
                    ->label('Date')
                    ->dateTime('d M Y'),
                TextColumn::make('percentage_score')
                    ->label('Security Score')
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state * 100, 2) . '%')
                    ->sortable(),
            ])
            ->defaultSort('captured_at', 'desc')
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->striped(false)
            ->recordActions([]);
    }
}
