<?php

namespace App\Filament\Pages;

use App\Services\LogSettings;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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
        $this->form->fill($settings->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Authentication')
                    ->description('Control each authentication event independently.')
                    ->schema([
                        Checkbox::make(LogSettings::LOGIN)->label('Login'),
                        Checkbox::make(LogSettings::LOGOUT)->label('Logout'),
                        Checkbox::make(LogSettings::FAILED_AUTHENTICATION)->label('Failed authentication'),
                    ]),
                Section::make('Requests')
                    ->description('Control request logging independently by request type.')
                    ->schema([
                        Checkbox::make(LogSettings::PAGE_REQUESTS)->label('Page requests (GET)'),
                        Checkbox::make(LogSettings::APPLICATION_REQUESTS)->label('Application requests (non-GET)'),
                    ]),
                Section::make('Model Actions')
                    ->description('Control every model lifecycle action independently.')
                    ->schema([
                        Checkbox::make(LogSettings::CREATED)->label('Create'),
                        Checkbox::make(LogSettings::UPDATED)->label('Update'),
                        Checkbox::make(LogSettings::DELETED)->label('Delete'),
                        Checkbox::make(LogSettings::RESTORED)->label('Restore'),
                        Checkbox::make(LogSettings::FORCE_DELETED)->label('Force delete'),
                    ]),
                Section::make('Maintenance')
                    ->description('Control audit logging for application and dependency update activity.')
                    ->schema([
                        Checkbox::make(LogSettings::UPDATES_AVAILABLE)->label('Updates available'),
                        Checkbox::make(LogSettings::UPDATE_SUCCEEDED)->label('Application update succeeded'),
                        Checkbox::make(LogSettings::UPDATE_FAILED)->label('Application update failed'),
                        Checkbox::make(LogSettings::PACKAGES_UPDATED)->label('Packages updated'),
                        Checkbox::make(LogSettings::PACKAGES_UPDATE_FAILED)->label('Package update failed'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(LogSettings $settings): void
    {
        $settings->save($this->data ?? []);

        Notification::make()
            ->title('Logging settings saved')
            ->success()
            ->send();
    }
}
