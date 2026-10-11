<?php

namespace App\Filament\Pages;

use App\Settings\StylingSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
    protected static string|\UnitEnum|null $navigationGroup = 'Styling';
    protected static ?int $navigationSort = 10;

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
                        FileUpload::make('logo_light')
                            ->label('Light logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->nullable(),
                        FileUpload::make('logo_dark')
                            ->label('Dark logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->nullable(),
                        FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Saved Colors')
                    ->description('Create custom colors with Filament’s color picker. Saved colors become available in the global palette selectors below.')
                    ->schema([
                        Repeater::make('custom_colors')
                            ->label('Custom colors')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(100),
                                ColorPicker::make('value')
                                    ->label('Color')
                                    ->hex()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add color')
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),

                Section::make('Colors')
                    ->description('Configure the global Filament color palette.')
                    ->schema([
                        self::colorSelect('primary_color', 'Primary'),
                        self::colorSelect('success_color', 'Success'),
                        self::colorSelect('warning_color', 'Warning'),
                        self::colorSelect('danger_color', 'Danger'),
                        self::colorSelect('info_color', 'Info'),
                        self::colorSelect('gray_color', 'Gray'),
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

    protected static function colorSelect(string $name, string $label): Select
    {
        return Select::make($name)
            ->label($label)
            ->options(fn (StylingSettings $settings): array => self::colorOptions($settings))
            ->required();
    }

    protected static function colorOptions(?StylingSettings $settings = null): array
    {
        $options = [
            'black' => 'Black',
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

        foreach ($settings?->custom_colors ?? [] as $customColor) {
            if (! empty($customColor['name']) && ! empty($customColor['value'])) {
                $options['custom:' . $customColor['value']] = $customColor['name'];
            }
        }

        return $options;
    }
}
