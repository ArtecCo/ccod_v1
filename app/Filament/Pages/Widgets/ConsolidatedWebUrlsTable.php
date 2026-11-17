<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ConsolidatedWebUrlsTable extends TableWidget
{
    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'All Accessible Web URLs';
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptionIds = $authorization->accessibleSubscriptionQuery($user)
            ->get()
            ->filter(fn (AzureSubscription $subscription): bool =>
                $authorization->hasPermission($user, 'urls.view', $subscription)
            )
            ->pluck('subscription_id')
            ->all();

        return SubscriptionWebUrl::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscription.display_name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('URL copied')
                    ->wrap(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                IconColumn::make('is_live')->label('Live')->boolean()->placeholder('Not checked'),
                TextColumn::make('http_status')->label('HTTP')->placeholder('—')->sortable(),
                TextColumn::make('response_time_ms')
                    ->label('Response')
                    ->formatStateUsing(fn ($state): string => $state === null ? '—' : $state . ' ms')
                    ->sortable(),
                IconColumn::make('ssl_status')->label('SSL')->boolean()->placeholder('N/A'),
                TextColumn::make('ssl_expires_at')->label('SSL Expires')->dateTime()->placeholder('—')->sortable(),
                TextColumn::make('ssl_days_remaining')->label('SSL Days')->placeholder('—')->sortable(),
                TextColumn::make('last_checked_at')->label('Last Checked')->dateTime()->placeholder('Never')->sortable(),
            ])
            ->defaultSort('url')
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([]);
    }
}
