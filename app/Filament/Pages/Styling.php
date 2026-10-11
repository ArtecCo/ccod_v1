<?php

namespace App\Filament\Pages;

use App\Settings\StylingSettings;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Styling extends SettingsPage
{
    protected static string $settings = StylingSettings::class;

    protected static ?string $navigationLabel = 'Styling';
    protected static ?string $title = 'Styling';
    protected static ?string $slug = 'styling';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static string|\UnitEnum|null $navigationGroup = 'Developers';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'developer';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Branding')
                    ->description('Configure the application identity used throughout the panels.')
                    ->schema([
                        TextInput::make('brand_name')
                            ->label('Brand name')
                            ->required(),
                        TextInput::make('logo_light')
                            ->label('Light logo')
                            ->nullable(),
                        TextInput::make('logo_dark')
                            ->label('Dark logo')
                            ->nullable(),
                        TextInput::make('favicon')
                            ->label('Favicon')
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Fonts')
                    ->description('Choose the Google Font used by Filament.')
                    ->schema([
                        Select::make('font_family')
                            ->label('Font family')
                            ->options([
                                'Inter' => 'Inter',
                                'Roboto' => 'Roboto',
                                'Open Sans' => 'Open Sans',
                                'Lato' => 'Lato',
                                'Poppins' => 'Poppins',
                                'Nunito Sans' => 'Nunito Sans',
                                'Source Sans 3' => 'Source Sans 3',
                                'Manrope' => 'Manrope',
                            ])
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Colors')
                    ->description('Configure the global Filament color palette.')
                    ->schema([
                        Select::make('primary_color')
                            ->label('Primary')
                            ->options(self::colorOptions())
                            ->required(),
                        Select::make('success_color')
                            ->label('Success')
                            ->options(self::colorOptions())
                            ->required(),
                        Select::make('warning_color')
                            ->label('Warning')
                            ->options(self::colorOptions())
                            ->required(),
                        Select::make('danger_color')
                            ->label('Danger')
                            ->options(self::colorOptions())
                            ->required(),
                        Select::make('info_color')
                            ->label('Info')
                            ->options(self::colorOptions())
                            ->required(),
                        Select::make('gray_color')
                            ->label('Gray')
                            ->options(self::colorOptions())
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Corner radius')
                    ->description('Control the global corner radius used by the application components.')
                    ->schema([
                        Select::make('corner_radius')
                            ->label('Radius')
                            ->options([
                                '0' => 'None',
                                '0.25rem' => 'Small',
                                '0.375rem' => 'Default',
                                '0.5rem' => 'Medium',
                                '0.75rem' => 'Large',
                                '1rem' => 'Extra large',
                            ])
                            ->required(),
                    ]),
            ]);
    }

    protected static function colorOptions(): array
    {
        return [
            'gray' => 'Gray',
            'red' => 'Red',
            'orange' => 'Orange',
            'amber' => 'Amber',
            'yellow' => 'Yellow',
            'lime' => 'Lime',
            'green' => 'Green',
            'emerald' => 'Emerald',
            'teal' => 'Teal',
            'cyan' => 'Cyan',
            'sky' => 'Sky',
            'blue' => 'Blue',
            'indigo' => 'Indigo',
            'violet' => 'Violet',
            'purple' => 'Purple',
            'fuchsia' => 'Fuchsia',
            'pink' => 'Pink',
            'rose' => 'Rose',
        ];
    }
}
