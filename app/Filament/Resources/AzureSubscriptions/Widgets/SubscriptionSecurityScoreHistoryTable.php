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
        $latest = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->orderByDesc('captured_at')
            ->first();

        $query = AzureSecurityScore::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('captured_at', '>=', now()->subDays(6)->startOfDay());

        if ($latest?->initiative_name !== null) {
            $query->where('initiative_name', $latest->initiative_name);
        }

        return $query;
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
