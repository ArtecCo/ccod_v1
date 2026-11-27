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

    public array $selectedResourceIds = [];

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

        if ($this->selectedResourceIds !== []) {
            $query->whereIn('resource_id', $this->selectedResourceIds);
        }

        if ($this->selectedSubscriptionId !== null) {
            $query->where('subscription_id', $this->selectedSubscriptionId);
        }

        return $query;
    }

    #[On('consolidated-cost-resource-filter')]
    public function applyResourceFilter(array $resourceIds): void
    {
        $resourceIds = collect($resourceIds)
            ->filter(fn ($id): bool => is_string($id) && $id !== '' && strlen($id) <= 512)
            ->unique()
            ->values()
            ->all();

        $this->selectedResourceIds = $this->selectedResourceIds === $resourceIds ? [] : $resourceIds;
        $this->resetTable();
    }

    #[On('consolidated-cost-subscription-filter')]
    public function applySubscriptionFilter(string $subscriptionId): void
    {
        $this->selectedSubscriptionId = $this->selectedSubscriptionId === $subscriptionId ? null : $subscriptionId;
        $this->resetTable();
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
            ->headerActions([
                Action::make('clearChartFilters')
                    ->label('Clear chart filters')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->visible(fn (): bool => $this->selectedResourceIds !== [] || $this->selectedSubscriptionId !== null)
                    ->action(function (): void {
                        $this->selectedResourceIds = [];
                        $this->selectedSubscriptionId = null;
                        $this->resetTable();
                    }),
            ])
            ->striped(false)
            ->recordActions([]);
    }
}
