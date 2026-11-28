<?php

namespace App\Filament\Pages;

use App\Jobs\GenerateReportExport;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ReportExport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Reports extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title = 'Reports';
    protected static ?string $slug = 'reports';
    protected static string|\\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string|\\UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 40;
    protected string $view = 'filament.pages.reports';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'developer'
            && auth()->guard('developers')->user()?->is_active === true;
    }

    public function mount(): void
    {
        $this->form->fill([
            'scope_type' => 'subscription',
            'timeline' => 'mtd',
            'month' => now()->startOfMonth()->toDateString(),
            'report_types' => [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report configuration')
                    ->description('Generate individual PDFs or one consolidated PDF for a selected scope and reporting period.')
                    ->schema([
                        Select::make('scope_type')
                            ->label('Scope')
                            ->options([
                                'subscription' => 'Subscription',
                                'project' => 'Project',
                                'business_unit' => 'Business unit',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('scope_id', null))
                            ->required(),
                        Select::make('scope_id')
                            ->label('Select scope')
                            ->options(fn (Get $get): array => $this->scopeOptions((string) $get('scope_type')))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('timeline')
                            ->label('Timeline')
                            ->options([
                                'mtd' => 'Month to date',
                                'month' => 'Selected month',
                            ])
                            ->live()
                            ->required(),
                        DatePicker::make('month')
                            ->label('Reporting month')
                            ->displayFormat('F Y')
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('timeline') === 'month')
                            ->required(fn (Get $get): bool => $get('timeline') === 'month'),
                        Select::make('report_types')
                            ->label('Report types')
                            ->multiple()
                            ->searchable()
                            ->options([
                                'overview' => 'Scope overview',
                                'costs' => 'Costs, budgets and forecasts',
                                'security' => 'Security posture and recommendations',
                                'alerts' => 'Azure alerts',
                                'web_urls' => 'Web URLs and SSL',
                                'resources' => 'Azure resources',
                                'consolidated' => 'Consolidated PDF (includes selected individual reports)',
                            ])
                            ->required()
                            ->minItems(1)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ReportExport::query()
                    ->where('developer_id', auth()->guard('developers')->id())
            )
            ->columns([
                TextColumn::make('scope_name')
                    ->label('Scope')
                    ->description(fn (ReportExport $record): string => Str::headline($record->scope_type)),
                TextColumn::make('period_start')
                    ->label('Reporting period')
                    ->formatStateUsing(fn ($state, ReportExport $record): string => $record->period_start->format('d M Y').' – '.$record->period_end->format('d M Y'))
                    ->sortable(false),
                TextColumn::make('report_types')
                    ->label('Report type(s)')
                    ->formatStateUsing(fn ($state): string => collect($state ?? [])->map(fn ($type): string => match ($type) {
                        'overview' => 'Scope overview',
                        'costs' => 'Costs, budgets and forecasts',
                        'security' => 'Security posture',
                        'alerts' => 'Azure alerts',
                        'web_urls' => 'Web URLs and SSL',
                        'resources' => 'Azure resources',
                        'consolidated' => 'Consolidated PDF',
                        default => Str::headline((string) $type),
                    })->implode(', '))
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'processing' => 'info',
                        'queued' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Str::headline($state)),
                TextColumn::make('failure_reason')
                    ->label('Error')
                    ->limit(70)
                    ->tooltip(fn (ReportExport $record): ?string => $record->failure_reason)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make(fn (ReportExport $record): array => collect($record->files ?? [])
                    ->values()
                    ->map(fn (array $file, int $index): Action => Action::make('download_'.$index)
                        ->label('Download '.$file['label'])
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(route('developer.reports.download', ['report' => $record, 'file' => $file['key']]))
                        ->openUrlInNewTab(false))
                    ->all())
                    ->label('Downloads')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->button()
                    ->visible(fn (ReportExport $record): bool => ! empty($record->files)),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    private function scopeOptions(string $type): array
    {
        return match ($type) {
            'subscription' => AzureSubscription::query()->orderBy('display_name')->pluck('display_name', 'subscription_id')->all(),
            'project' => Project::query()->orderBy('name')->pluck('name', 'id')->all(),
            'business_unit' => BusinessUnit::query()->orderBy('name')->pluck('name', 'id')->all(),
            default => [],
        };
    }

    public function generate(): void
    {
        $state = $this->form->getState();
        $types = array_values(array_unique($state['report_types'] ?? []));
        if ($types === []) {
            Notification::make()->title('Choose at least one report type')->body('Select one or more individual reports, or choose Consolidated PDF to include all report sections.')->warning()->send();
            return;
        }

        $scopeType = $state['scope_type'];
        $scopeId = (string) $state['scope_id'];
        $scopeName = (string) ($this->scopeOptions($scopeType)[$scopeId] ?? '');

        abort_if($scopeName === '', 422, 'The selected scope is no longer available.');

        $periodStart = $state['timeline'] === 'mtd'
            ? now()->startOfMonth()->toDateString()
            : now()->parse($state['month'])->startOfMonth()->toDateString();
        $periodEnd = $state['timeline'] === 'mtd'
            ? now()->toDateString()
            : now()->parse($state['month'])->endOfMonth()->toDateString();

        /** @var Developer $developer */
        $developer = auth()->guard('developers')->user();

        $report = ReportExport::query()->create([
            'developer_id' => $developer->getKey(),
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'scope_name' => $scopeName,
            'timeline' => $state['timeline'],
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'report_types' => $types,
            'status' => 'queued',
            'expires_at' => now()->addDays(7),
        ]);

        GenerateReportExport::dispatch($report->getKey());

        Notification::make()
            ->title('Report generation queued')
            ->body('You can leave this page. Your PDFs will be available here when processing is complete.')
            ->success()
            ->send();
    }
}
