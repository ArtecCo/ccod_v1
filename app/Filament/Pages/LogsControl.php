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
            'actions' => array_keys(array_filter($settings->all())),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audit logging controls')
                    ->description('Each action can be enabled or disabled independently. Changes apply to new audit activity; existing audit records are not deleted.')
                    ->schema([
                        CheckboxList::make('actions')
                            ->label('Log these actions')
                            ->options(LogSettings::GROUPS)
                            ->columns(1)
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(LogSettings $settings): void
    {
        $selected = $this->data['actions'] ?? [];

        $settings->save(array_fill_keys(
            LogSettings::KEYS,
            false,
        ) + array_fill_keys($selected, true));

        Notification::make()
            ->title('Logging settings saved')
            ->success()
            ->send();
    }
}
