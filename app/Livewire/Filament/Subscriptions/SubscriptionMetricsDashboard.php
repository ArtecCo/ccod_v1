<?php

declare(strict_types=1);

namespace App\Livewire\Filament\Subscriptions;


use App\Models\AzureSubscription;
use App\Models\BillingResource;
use App\Models\Developer;
use App\Models\SubscriptionMetricGraph;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\AzureMetricsService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class SubscriptionMetricsDashboard extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents;

    public AzureSubscription $record;

    public array $filters = [];

    public int $refreshVersion = 0;

    public function mount(AzureSubscription $record): void
    {
        $this->record = $record;
        abort_unless($this->canViewMetrics(), 403);

        $now = now('Asia/Kolkata');

        $this->filtersForm->fill([
            'preset' => '15m',
            'custom_start' => $now->copy()->subMinutes(15)->format('Y-m-d H:i:s'),
            'custom_end' => $now->format('Y-m-d H:i:s'),
        ]);
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->components([
                Section::make('Time range')
                    ->headerActions([
                        Action::make('refreshCharts')
                            ->label('Refresh')
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->action(fn () => $this->refreshCharts()),
                    ])
                    ->schema([
                        Select::make('preset')
                            ->label('Period')
                            ->options([
                                '15m' => 'Last 15 minutes',
                                '30m' => 'Last 30 minutes',
                                '1h' => 'Last 1 hour',
                                '6h' => 'Last 6 hours',
                                '12h' => 'Last 12 hours',
                                '24h' => 'Last 24 hours',
                                '7d' => 'Last 7 days',
                                '30d' => 'Last 30 days',
                                'custom' => 'Custom range',
                            ])
                            ->default('15m')
                            ->live()
                            ->required(),
                        DateTimePicker::make('custom_start')
                            ->label('Start (IST)')
                            ->timezone('Asia/Kolkata')
                            ->seconds(false)
                            ->native(true)
                            ->visible(fn ($get): bool => $get('preset') === 'custom'),
                        DateTimePicker::make('custom_end')
                            ->label('End (IST)')
                            ->timezone('Asia/Kolkata')
                            ->seconds(false)
                            ->native(true)
                            ->visible(fn ($get): bool => $get('preset') === 'custom'),
                    ])
                    ->columns(3),
            ]);
    }

    public function addGraphAction(): Action
    {
        return Action::make('addGraph')
            ->label('Add graph')
            ->icon('heroicon-o-plus')
            ->visible(fn (): bool => $this->canManageMetrics())
            ->modalHeading('Add resource metric graph')
            ->modalSubmitActionLabel('Create graph')
            ->schema([
                Select::make('resource_id')
                    ->label('Azure resource')
                    ->options(fn (): array => $this->resourceOptions())
                    ->searchable()
                    ->live()
                    ->required(),
                TextInput::make('title')
                    ->label('Graph title')
                    ->maxLength(160)
                    ->required(),
                Repeater::make('series')
                    ->label('Metric series')
                    ->schema([
                        Select::make('metric')
                            ->label('Metric')
                            ->options(fn ($get): array => $this->metricOptions($get('../../resource_id')))
                            ->searchable()
                            ->live()
                            ->required(),
                        Select::make('aggregation')
                            ->label('Aggregation')
                            ->options(fn ($get): array => $this->aggregationOptions($get('../../resource_id'), $get('metric')))
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->maxItems(20)
                    ->required(),
            ])
            ->action(function (array $data): void {
                abort_unless($this->canManageMetrics(), 403);

                $resource = BillingResource::query()
                    ->where('subscription_id', $this->record->subscription_id)
                    ->where('resource_id', $data['resource_id'])
                    ->firstOrFail();

                $definitions = collect(app(AzureMetricsService::class)->definitions($resource))->keyBy('name');
                $series = collect($data['series'])->map(function (array $item) use ($definitions): array {
                    $definition = $definitions->get($item['metric'] ?? '');

                    abort_unless(
                        $definition && in_array($item['aggregation'] ?? null, $definition['aggregations'], true),
                        422,
                        'A selected metric or aggregation is not supported by this resource.'
                    );

                    return [
                        'metric' => $item['metric'],
                        'aggregation' => $item['aggregation'],
                    ];
                })->values()->all();

                SubscriptionMetricGraph::query()->create([
                    'subscription_id' => $this->record->subscription_id,
                    'resource_id' => $resource->resource_id,
                    'title' => $data['title'],
                    'series' => $series,
                    'position' => (int) SubscriptionMetricGraph::query()
                        ->where('subscription_id', $this->record->subscription_id)
                        ->max('position') + 1,
                ]);

                $this->refreshVersion++;
            })
            ->successNotificationTitle('Metric graph created');
    }

    public function deleteGraph(int $graphId): void
    {
        abort_unless($this->canManageMetrics(), 403);

        SubscriptionMetricGraph::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->whereKey($graphId)
            ->firstOrFail()
            ->delete();
    }

    public function refreshCharts(): void
    {
        abort_unless($this->canViewMetrics(), 403);
        $this->refreshVersion++;
    }

    public function graphsSchema(Schema $schema): Schema
    {
        $sections = [];

        foreach ($this->getGraphs() as $graph) {
            $headerActions = [];

            if ($this->canManageMetrics()) {
                $headerActions[] = Action::make('editMetricGraph'.$graph->id)
                    ->label('Edit graph')
                    ->tooltip('Edit graph')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->color('gray')
                    ->modalHeading('Edit resource metric graph')
                    ->modalSubmitActionLabel('Save changes')
                    ->fillForm(fn (): array => [
                        'resource_id' => $graph->resource_id,
                        'title' => $graph->title,
                        'series' => $graph->series ?? [],
                    ])
                    ->schema([
                        Select::make('resource_id')
                            ->label('Azure resource')
                            ->options(fn (): array => $this->resourceOptions())
                            ->searchable()
                            ->live()
                            ->required(),
                        TextInput::make('title')
                            ->label('Graph title')
                            ->maxLength(160)
                            ->required(),
                        Repeater::make('series')
                            ->label('Metric series')
                            ->schema([
                                Select::make('metric')
                                    ->label('Metric')
                                    ->options(fn ($get): array => $this->metricOptions($get('../../resource_id')))
                                    ->searchable()
                                    ->live()
                                    ->required(),
                                Select::make('aggregation')
                                    ->label('Aggregation')
                                    ->options(fn ($get): array => $this->aggregationOptions($get('../../resource_id'), $get('metric')))
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->maxItems(20)
                            ->required(),
                    ])
                    ->action(function (array $data) use ($graph): void {
                        abort_unless($this->canManageMetrics(), 403);

                        $resource = BillingResource::query()
                            ->where('subscription_id', $this->record->subscription_id)
                            ->where('resource_id', $data['resource_id'])
                            ->firstOrFail();

                        $definitions = collect(app(AzureMetricsService::class)->definitions($resource))->keyBy('name');
                        $series = collect($data['series'])->map(function (array $item) use ($definitions): array {
                            $definition = $definitions->get($item['metric'] ?? '');

                            abort_unless(
                                $definition && in_array($item['aggregation'] ?? null, $definition['aggregations'], true),
                                422,
                                'A selected metric or aggregation is not supported by this resource.'
                            );

                            return [
                                'metric' => $item['metric'],
                                'aggregation' => $item['aggregation'],
                            ];
                        })->values()->all();

                        SubscriptionMetricGraph::query()
                            ->where('subscription_id', $this->record->subscription_id)
                            ->whereKey($graph->id)
                            ->firstOrFail()
                            ->update([
                                'resource_id' => $resource->resource_id,
                                'title' => $data['title'],
                                'series' => $series,
                            ]);

                        $this->refreshVersion++;
                    })
                    ->successNotificationTitle('Metric graph updated');

                $headerActions[] = Action::make('removeMetricGraph'.$graph->id)
                    ->label('Remove graph')
                    ->tooltip('Remove graph')
                    ->icon('heroicon-o-trash')
                    ->iconButton()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove metric graph')
                    ->modalDescription('This removes the graph for everyone who can view this subscription.')
                    ->action(function () use ($graph): void {
                        $this->deleteGraph($graph->id);
                        $this->refreshVersion++;
                    });
            }

            $range = $this->getRange();

            $sections[] = Section::make($graph->title)
                ->description(($graph->resource?->name ?? 'Resource').' · '.($graph->resource?->resource_type ?? ''))
                ->headerActions($headerActions)
                ->schema([
                    Livewire::make(\App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionMetricGraphChart::class, [
                        'record' => $this->record,
                        'graphId' => $graph->id,
                        'start' => $range['start'],
                        'end' => $range['end'],
                        'interval' => $range['interval'],
                    ])
                        ->key('metric-chart-'.$graph->id.'-'.sha1(json_encode($range)).'-'.$this->refreshVersion)
                        ->columnSpanFull(),
                ])
                ->columnSpan(1);
        }

        if ($sections === []) {
            return $schema->components([
                Section::make('No metric graphs configured')
                    ->description($this->canManageMetrics()
                        ? 'Add a graph to monitor one or more metrics from an Azure resource.'
                        : 'No metric graphs have been configured for this subscription.'),
            ]);
        }

        return $schema->components([
            Grid::make(2)->schema($sections),
        ]);
    }

    public function getGraphs()
    {
        abort_unless($this->canViewMetrics(), 403);

        return SubscriptionMetricGraph::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->with('resource:resource_id,name,resource_type')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    public function getRange(): array
    {
        $preset = $this->filters['preset'] ?? '15m';
        $durations = [
            '15m' => 15,
            '30m' => 30,
            '1h' => 60,
            '6h' => 360,
            '12h' => 720,
            '24h' => 1440,
            '7d' => 10080,
            '30d' => 43200,
        ];

        if ($preset === 'custom') {
            abort_unless(filled($this->filters['custom_start'] ?? null) && filled($this->filters['custom_end'] ?? null), 422, 'Choose both a start and end time.');
            $start = Carbon::parse($this->filters['custom_start'] ?? null, 'Asia/Kolkata');
            $end = Carbon::parse($this->filters['custom_end'] ?? null, 'Asia/Kolkata');
            abort_unless($end->greaterThan($start), 422, 'The end of the range must be after the start.');
            abort_if($start->diffInDays($end) > 93, 422, 'Metric time range cannot exceed 93 days.');
        } else {
            $minutes = $durations[$preset] ?? 15;
            $end = now();

            if ($preset === '30d') {
                // Use exactly 30 calendar-day buckets (including today),
                // rather than touching 31 dates with a rolling 30x24h range.
                $start = $end->copy()->startOfDay()->subDays(29);
            } else {
                $start = $end->copy()->subMinutes($minutes);
            }
        }

        $minutes = max(1, $start->diffInMinutes($end));

        // Long-range charts use Azure's daily aggregation to keep browser
        // rendering lightweight. Shorter ranges retain one-minute sampling.
        // AzureMetricsService validates that each metric supports the grain.
        $interval = $preset === '30d' ? 'P1D' : 'PT1M';

        return [
            // Azure Monitor's timespan parser treats '+' in an offset as a
            // space in some request paths. Send explicit UTC Zulu timestamps.
            'start' => $start->utc()->format('Y-m-d\\TH:i:s\\Z'),
            'end' => $end->utc()->format('Y-m-d\\TH:i:s\\Z'),
            'interval' => $interval,
        ];
    }

    private function resourceOptions(): array
    {
        $options = [];

        foreach (app(AzureMetricsService::class)->eligibleResources($this->record) as $resource) {
            $options[$resource->resource_id] = $resource->name.' ('.$resource->resource_type.')';
        }

        return $options;
    }

    private function metricOptions(?string $resourceId): array
    {
        $resource = $this->findEligibleResource($resourceId);
        if (! $resource) {
            return [];
        }

        try {
            return collect(app(AzureMetricsService::class)->definitions($resource))
                ->mapWithKeys(fn (array $metric): array => [
                    $metric['name'] => $metric['display_name'].(filled($metric['unit']) ? ' ('.$metric['unit'].')' : ''),
                ])->all();
        } catch (Throwable $exception) {
            report($exception);
            return [];
        }
    }

    private function aggregationOptions(?string $resourceId, ?string $metricName): array
    {
        $resource = $this->findEligibleResource($resourceId);
        if (! $resource || blank($metricName)) {
            return [];
        }

        try {
            $definition = collect(app(AzureMetricsService::class)->definitions($resource))
                ->firstWhere('name', $metricName);

            return collect($definition['aggregations'] ?? [])
                ->mapWithKeys(fn (string $aggregation): array => [$aggregation => $aggregation])
                ->all();
        } catch (Throwable $exception) {
            report($exception);
            return [];
        }
    }

    private function findEligibleResource(?string $resourceId): ?BillingResource
    {
        if (blank($resourceId)) {
            return null;
        }

        return BillingResource::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('resource_id', $resourceId)
            ->first();
    }

    private function canViewMetrics(): bool
    {
        if (auth()->guard('developers')->user() instanceof Developer
            && auth()->guard('developers')->user()->is_active) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User
            && $user->is_active
            && app(AccessAuthorizationService::class)->hasPermission($user, 'resources.metrics.view', $this->record);
    }

    private function canManageMetrics(): bool
    {
        if (auth()->guard('developers')->user() instanceof Developer
            && auth()->guard('developers')->user()->is_active) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User
            && $user->is_active
            && app(AccessAuthorizationService::class)->hasPermission($user, 'resources.metrics.manage', $this->record);
    }

    public function render()
    {
        abort_unless($this->canViewMetrics(), 403);

        return view('livewire.filament.subscriptions.metrics-dashboard', [
            'graphs' => $this->getGraphs(),
            'range' => $this->getRange(),
        ]);
    }
}