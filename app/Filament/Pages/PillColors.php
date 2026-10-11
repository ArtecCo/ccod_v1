<?php

namespace App\Filament\Pages;

use App\Models\PillColor;
use App\Support\PillColorRegistry;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PillColors extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Colors';
    protected static ?string $title = 'Pill Colors';
    protected static ?string $slug = 'colors';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
    protected static string|\UnitEnum|null $navigationGroup = 'Developers';
    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.pill-colors';

    public ?array $data = [];

    public function mount(): void
    {
        foreach (PillColorRegistry::all() as $key => $definition) {
            PillColor::query()->firstOrCreate(
                ['key' => $key],
                ['color' => $definition['default']],
            );
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pill color')
                    ->description('Choose the Filament color used for this pill throughout the application.')
                    ->schema([
                        Select::make('color')
                            ->label('Color')
                            ->options([
                                'gray' => 'Gray',
                                'danger' => 'Red',
                                'warning' => 'Amber',
                                'success' => 'Green',
                                'info' => 'Blue',
                            ])
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function saveColor(string $key): void
    {
        $data = $this->form->getState();

        PillColor::query()->updateOrCreate(
            ['key' => $key],
            ['color' => $data['color']],
        );

        Notification::make()
            ->title('Pill color updated')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        $registry = PillColorRegistry::all();

        return $table
            ->query(PillColor::query()->orderBy('key'))
            ->columns([
                TextColumn::make('key')
                    ->label('Pill')
                    ->formatStateUsing(fn (string $state): string => $registry[$state]['label'] ?? $state)
                    ->searchable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (string $state): string => $state)
                    ->sortable(),
            ]);
    }
}
