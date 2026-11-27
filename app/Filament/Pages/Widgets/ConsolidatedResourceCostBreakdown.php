<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Filament\Pages\Widgets\Concerns\HasConsolidatedCostScope;
use App\Models\BillingResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ConsolidatedResourceCostBreakdown extends TableWidget
{
    use HasConsolidatedCostScope;

    #[Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    public array $selectedResourceTypes = [];

    public ?string $selectedSubscriptionId = null;

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return 'Resource Cost Breakdown';
    }

    protected function getTableQuery(): Builder
    {
        $subscriptions = $this->authorizedCostSubscriptions($this->scopeFilter);
        $query = BillingResource::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptions->pluck('subscription_id')->all())
            ->whereNotNull('cost_eur');

        if ($this->selectedResourceTypes !== []) {
            $query->whereIn('resource_type', $this->selectedResourceTypes);
        }

        if ($this->selectedSubscriptionId !== null) {
            $query->where('subscription_id', $this->selectedSubscriptionId);
        }

        return $query;
    }

    #[On('consolidated-cost-resource-type-filter')]
    public function applyResourceTypeFilter(array $resourceTypes): void
    {
        $resourceTypes = collect($resourceTypes)
            ->filter(fn ($type): bool => is_string($type) && $type !== '' && strlen($type) <= 255)
            ->unique()
            ->values()
            ->all();

        $this->selectedResourceTypes = $this->selectedResourceTypes === $resourceTypes ? [] : $resourceTypes;
        $this->resetTable();
    }

    #[On('consolidated-cost-subscription-filter')]
    public function applySubscriptionFilter(string $subscriptionId): void
    {
        $this->selectedSubscriptionId = $this->selectedSubscriptionId === $subscriptionId ? null : $subscriptionId;
        $this->resetTable();
    }

    /**
     * The export contains the current table query, so only expose it when the
     * user has export permission for every subscription represented by scope.
     * This prevents exporting rows that are viewable but not exportable.
     */
    protected function canExportCost(): bool
    {
        $user = auth()->guard('web')->user();

        if (! $user instanceof User) {
            return false;
        }

        $subscriptions = $this->authorizedCostSubscriptions($this->scopeFilter);

        if ($this->selectedSubscriptionId !== null) {
            $subscriptions = $subscriptions->filter(
                fn ($subscription): bool => (string) $subscription->subscription_id === $this->selectedSubscriptionId
            );
        }

        if ($subscriptions->isEmpty()) {
            return false;
        }

        $authorization = app(AccessAuthorizationService::class);

        return $subscriptions->every(
            fn ($subscription): bool => $authorization->hasPermission($user, 'cost.export', $subscription)
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscription.display_name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable(),
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
            ->defaultSort('cost_eur', 'desc')
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExportCost()),
                Action::make('clearChartFilters')
                    ->label('Clear chart filters')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->visible(fn (): bool => $this->selectedResourceTypes !== [] || $this->selectedSubscriptionId !== null)
                    ->action(function (): void {
                        $this->selectedResourceTypes = [];
                        $this->selectedSubscriptionId = null;
                        $this->resetTable();
                    }),
            ])
            ->striped(false)
            ->recordActions([]);
    }
}
