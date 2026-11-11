<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionModuleSync;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class DeveloperSyncStatus extends TableWidget
{
    protected static ?string $heading = 'Azure data sync';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        $latestIds = SubscriptionModuleSync::query()
            ->selectRaw('MAX(id)')
            ->groupBy('module');

        return $table
            ->query(SubscriptionModuleSync::query()->whereIn('id', $latestIds))
            ->columns([
                TextColumn::make('module')
                    ->label('Module')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'subscriptions' => 'Subscriptions',
                        'resources' => 'Resources',
                        'costs' => 'Costs',
                        'security' => 'Security',
                        'alerts' => 'Alerts',
                        'web_urls' => 'Web URLs',
                        default => str($state)->replace('_', ' ')->title()->toString(),
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'success' => 'success',
                        'running' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'success' => 'Successful',
                        'running' => 'Running',
                        'failed' => 'Failed',
                        default => 'Not recorded',
                    }),
                TextColumn::make('last_successful_at')
                    ->label('Last successful')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('last_started_at')
                    ->label('Last attempt')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('last_started_at', 'desc')
            ->paginated(false)
            ->striped();
    }
}
