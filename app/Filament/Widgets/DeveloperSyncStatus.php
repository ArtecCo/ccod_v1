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

    protected static ?string $pollingInterval = '10s';

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
                        'security' => 'Security Recommendations',\n                        'security_scores' => 'Security Scores',
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
                        'failed' => 'danger',\n                        'partial' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'success' => 'Successful',
                        'running' => 'Running',
                        'failed' => 'Failed',\n                        'partial' => 'Partially successful',
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
