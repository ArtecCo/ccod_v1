<?php

namespace App\Filament\Pages;

use App\Services\AuditLogger;
use App\Services\LogSettings;
use App\Settings\MaintenanceSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MaintenanceMode extends Page
{
    protected static ?string $navigationLabel = 'Maintenance Mode';
    protected static ?string $title = 'Maintenance Mode';
    protected static ?string $slug = 'maintenance-mode';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';
    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.maintenance-mode';

    public bool $enabled = false;

    public function mount(MaintenanceSettings $settings): void
    {
        $this->enabled = $settings->enabled;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleMaintenance')
                ->label(fn (): string => $this->enabled ? 'Disable maintenance mode' : 'Enable maintenance mode')
                ->icon(fn (): string => $this->enabled ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                ->color(fn (): string => $this->enabled ? 'success' : 'warning')
                ->requiresConfirmation()
                ->modalHeading(fn (): string => $this->enabled ? 'Disable maintenance mode?' : 'Enable maintenance mode?')
                ->modalDescription(fn (): string => $this->enabled
                    ? 'The public application will become accessible again.'
                    : 'The public application will become inaccessible. Localhost and the Developer panel will remain available for testing and administration.'
                )
                ->modalSubmitActionLabel(fn (): string => $this->enabled ? 'Disable maintenance mode' : 'Enable maintenance mode')
                ->action(fn (MaintenanceSettings $settings, AuditLogger $auditLogger): void => $this->toggle($settings, $auditLogger)),
        ];
    }

    public function toggle(MaintenanceSettings $settings, AuditLogger $auditLogger): void
    {
        $settings->enabled = ! $settings->enabled;
        $settings->save();
        $this->enabled = $settings->enabled;

        $event = $this->enabled ? 'Maintenance Mode Enabled' : 'Maintenance Mode Disabled';
        $description = $this->enabled
            ? 'Maintenance mode was enabled. Public application access is now blocked.'
            : 'Maintenance mode was disabled. Public application access is now available.';

        $auditLogger->log(
            event: $event,
            description: $description,
            success: true,
            properties: [
                'maintenance_mode' => $this->enabled,
                'scope' => 'public_application',
            ],
            logName: 'Maintenance',
            tags: ['maintenance', $this->enabled ? 'enabled' : 'disabled'],
            category: LogSettings::MAINTENANCE_MODE,
        );

        Notification::make()
            ->title($this->enabled ? 'Maintenance mode enabled' : 'Maintenance mode disabled')
            ->body($this->enabled
                ? 'Public access is blocked. Localhost and the Developer panel remain available.'
                : 'Public access has been restored.'
            )
            ->success()
            ->send();
    }
}
