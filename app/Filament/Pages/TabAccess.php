<?php

namespace App\Filament\Pages;

use App\Settings\TabAccessSettings;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TabAccess extends SettingsPage
{
    protected static string $settings = TabAccessSettings::class;

    protected static ?string $navigationLabel = 'Tab Access';
    protected static ?string $title = 'Tab Access';
    protected static ?string $slug = 'tab-access';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 30;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Restricted tab behavior')
                    ->description('Choose how tabs are presented to users who do not have the permission required to view them.')
                    ->schema([
                        Select::make('restricted_tab_behavior')
                            ->label('Behavior')
                            ->options([
                                'disabled' => 'Disable restricted tabs',
                                'hidden' => 'Hide restricted tabs',
                            ])
                            ->helperText('Disabled tabs remain visible but cannot be opened. Hidden tabs are removed from the tab navigation.')
                            ->required(),
                    ]),
            ]);
    }
}
