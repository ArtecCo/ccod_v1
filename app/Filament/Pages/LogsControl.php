<?php

namespace App\Filament\Pages;

use App\Services\LogSettings;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogsControl extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Logs Control';
    protected static ?string $title = 'Logs Control';
    protected static ?string $slug = 'logs-control';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'filament.pages.logs-control';

    public ?array $data = [];

    public function mount(LogSettings $settings): void
    {
        $this->form->fill([
            'categories' => array_keys(array_filter($settings->all())),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audit logging controls')
                    ->description('Choose which categories of activity CCOD should record. Changes apply immediately to new activity; existing audit records are not deleted.')
                    ->schema([
                        CheckboxList::make('categories')
                            ->label('Log these categories')
                            ->options([
                                LogSettings::AUTHENTICATION => 'Authentication — login, logout and failed authentication',
                                LogSettings::REQUESTS => 'Requests — page and application requests',
                                LogSettings::MODEL_ACTIONS => 'Model actions — create, update, delete, restore and force-delete',
                            ])
                            ->columns(1)
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(LogSettings $settings): void
    {
        $selected = $this->data['categories'] ?? [];

        $settings->save([
            LogSettings::AUTHENTICATION => in_array(LogSettings::AUTHENTICATION, $selected, true),
            LogSettings::REQUESTS => in_array(LogSettings::REQUESTS, $selected, true),
            LogSettings::MODEL_ACTIONS => in_array(LogSettings::MODEL_ACTIONS, $selected, true),
        ]);

        Notification::make()
            ->title('Logging settings saved')
            ->success()
            ->send();
    }
}
