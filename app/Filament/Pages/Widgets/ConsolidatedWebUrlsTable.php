<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Livewire\Attributes\On;

class ConsolidatedWebUrlsTable extends TableWidget
{
    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    public string $urlFilter = 'all';

    #[On('filter-web-urls')]
    public function filterWebUrls(?string $filter = 'all'): void
    {
        $filter = in_array($filter, ['all', 'live', 'down', 'ssl_expiring'], true) ? $filter : 'all';
        $this->urlFilter = $this->urlFilter === $filter && $filter !== 'all' ? 'all' : $filter;
        $this->resetTable();
    }

    protected function getTableHeading(): ?string
    {
        return match ($this->urlFilter) {
            'live' => 'Live Web URLs',
            'down' => 'Down Web URLs',
            'ssl_expiring' => 'Web URLs with SSL Expiring in 15 Days',
            default => 'All Accessible Web URLs',
        };
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

        $query = SubscriptionWebUrl::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds);

        $filter = $this->urlFilter;

        if ($filter === 'live') {
            $query->where('is_active', true)->where('is_live', true);
        } elseif ($filter === 'down') {
            $query->where('is_active', true)->where('is_live', false);
        } elseif ($filter === 'ssl_expiring') {
            $query->where('is_active', true)
                ->whereNotNull('ssl_expires_at')
                ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)]);
        } elseif ($filter !== 'all') {
            abort(400, 'Invalid web URL filter.');
        }

        return $query;
    }

    private function exportableSubscriptionIds(): array
    {
        $user = auth()->guard('web')->user();

        if (! $user instanceof User) {
            return [];
        }

        $authorization = app(AccessAuthorizationService::class);

        return $authorization->accessibleSubscriptionQuery($user)
            ->get()
            ->filter(fn (AzureSubscription $subscription): bool =>
                $authorization->hasPermission($user, 'urls.export', $subscription)
            )
            ->pluck('subscription_id')
            ->all();
    }

    private function canExport(): bool
    {
        return $this->exportableSubscriptionIds() !== [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
                TernaryFilter::make('is_live')
                    ->label('Live Status')
                    ->trueLabel('Live')
                    ->falseLabel('Down'),
                TernaryFilter::make('use_proxy')
                    ->label('Proxy')
                    ->trueLabel('Proxy Enabled')
                    ->falseLabel('Direct Connection'),
                TernaryFilter::make('ssl_status')
                    ->label('SSL Valid')
                    ->trueLabel('Valid')
                    ->falseLabel('Invalid'),
                Filter::make('ssl_expiring')
                    ->label('SSL Expiring in 15 Days')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                        ->whereNotNull('ssl_expires_at')
                        ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)])),
            ])
            ->columns([
                TextColumn::make('subscription.display_name')
                    ->label('Subscription')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    
                    ->copyable()
                    ->copyMessage('URL copied')
                    ->wrap(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                IconColumn::make('use_proxy')->label('Proxy')->boolean()->tooltip('Uses the configured corporate HTTP proxy'),
                IconColumn::make('is_live')->label('Live')->boolean()->placeholder('Not checked'),
                TextColumn::make('http_status')->label('HTTP')->placeholder('—'),
                TextColumn::make('response_time_ms')
                    ->label('Response')
                    ->formatStateUsing(fn ($state): string => $state === null ? '—' : $state . ' ms'),
                IconColumn::make('ssl_status')->label('SSL')->boolean()->placeholder('N/A'),
                TextColumn::make('ssl_provider')->label('SSL Provider')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_subject')->label('SSL Subject')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_issuer')->label('SSL Issuer')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_valid_from')->label('SSL Valid From')->dateTime()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_expires_at')->label('SSL Expires')->dateTime()->placeholder('—'),
                TextColumn::make('ssl_days_remaining')->label('SSL Days')->placeholder('—'),
                TextColumn::make('last_checked_at')->label('Last Checked')->dateTime()->placeholder('Never'),
            ])
            ->groups([
                Group::make('subscription.display_name')
                    ->label('Subscription')
                    ->collapsible(),
            ])
            ->defaultGroup('subscription.display_name')
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExport())
                    ->queryUsing(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder =>
                        $query->whereIn('subscription_id', $this->exportableSubscriptionIds())
                    ),
            ]);
    }
}
