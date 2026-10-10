<?php

namespace App\Providers;

use App\Models\Developer;
use App\Models\Documentation;
use App\Policies\ActivityPolicy;
use App\Policies\DocumentationPolicy;
use App\Services\AuditLogger;
use App\Services\LogSettings;
use Filament\Forms\Components\Field;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use TomaszBoloz\LaravelUpdater\Events\PackagesUpdateFailed;
use TomaszBoloz\LaravelUpdater\Events\PackagesUpdated;
use TomaszBoloz\LaravelUpdater\Events\UpdateFailed;
use TomaszBoloz\LaravelUpdater\Events\UpdatesAvailable;
use TomaszBoloz\LaravelUpdater\Events\UpdateSucceeded;
use Ysfkaya\ShipLog\ShipLogPlugin;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditLogger::class);
        $this->app->singleton(LogSettings::class);

        Panel::configureUsing(function (Panel $panel): void {
            match ($panel->getId()) {
                'admin' => $panel->plugin(
                    ShipLogPlugin::make()
                        ->usingDatabase()
                        ->usingPage(\App\Filament\Pages\Changelog::class)
                        ->fab(enabled: false)
                        ->navigationLabel('Changelog')
                        ->pageTitle('Changelog')
                        ->resource(false),
                ),
                'developer' => $panel->plugin(
                    ShipLogPlugin::make()
                        ->usingDatabase()
                        ->usingPage(\App\Filament\Pages\Changelog::class)
                        ->fab(enabled: false)
                        ->navigationLabel('Changelog')
                        ->pageTitle('Changelog')
                        ->resource()
                        ->navigationGroup('Maintenance')
                        ->navigationSort(20),
                ),
                default => null,
            };
        });
    }

    public function boot(): void
    {
        Gate::policy(Documentation::class, DocumentationPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        Gate::define('updater.manage', function (mixed $user): bool {
            return $user instanceof Developer
                && $user->is_active === true
                && auth()->guard('developers')->id() === $user->getAuthIdentifier();
        });

        Gate::define('shiplog.view', function (mixed $user): bool {
            return $user !== null;
        });

        Gate::define('shiplog.manage', function (mixed $user): bool {
            return $user instanceof Developer
                && $user->is_active === true
                && auth()->guard('developers')->id() === $user->getAuthIdentifier();
        });

        FilamentView::registerRenderHook(
            'panels::sidebar.footer',
            fn (): mixed => view('filament.components.changelog-link'),
        );

        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->extraAttributes(['class' => 'compact-table [&_td]:py-1 [&_th]:py-1']);
        });

        Field::configureUsing(function (Field $field): void {
            $field->inlineLabel();
        });

        Event::listen(Login::class, function (Login $event): void {
            app(AuditLogger::class)->log(
                event: 'Login',
                description: 'Authentication succeeded.',
                success: true,
                properties: ['guard' => $event->guard, 'remember' => $event->remember],
                logName: 'Access',
                tags: ['authentication', 'login'],
                category: LogSettings::LOGIN,
            );
        });

        Event::listen(Logout::class, function (Logout $event): void {
            app(AuditLogger::class)->log(
                event: 'Logout',
                description: 'Authentication session ended.',
                success: true,
                properties: ['guard' => $event->guard],
                logName: 'Access',
                tags: ['authentication', 'logout'],
                category: LogSettings::LOGOUT,
            );
        });

        Event::listen(Failed::class, function (Failed $event): void {
            $identifier = collect(['email', 'username', 'login'])
                ->first(fn (string $key): bool => array_key_exists($key, $event->credentials));

            app(AuditLogger::class)->log(
                event: 'Login Failed',
                description: 'Authentication failed.',
                success: false,
                failureReason: 'Authentication credentials did not authenticate.',
                properties: ['guard' => $event->guard, 'identifier_type' => $identifier],
                logName: 'Access',
                tags: ['authentication', 'failure'],
                category: LogSettings::FAILED_AUTHENTICATION,
            );
        });

        Event::listen([
            'eloquent.created: *',
            'eloquent.updated: *',
            'eloquent.deleted: *',
            'eloquent.restored: *',
            'eloquent.forceDeleted: *',
        ], function (string $eventName, array $data): void {
            $model = $data[0] ?? null;

            if (! $model instanceof Model || $model instanceof Activity) {
                return;
            }

            $event = match (true) {
                str_contains($eventName, '.created:') => 'Created',
                str_contains($eventName, '.updated:') => 'Updated',
                str_contains($eventName, '.deleted:') => 'Deleted',
                str_contains($eventName, '.restored:') => 'Restored',
                str_contains($eventName, '.forceDeleted:') => 'Force Deleted',
                default => 'Model Action',
            };

            $category = match ($event) {
                'Created' => LogSettings::CREATED,
                'Updated' => LogSettings::UPDATED,
                'Deleted' => LogSettings::DELETED,
                'Restored' => LogSettings::RESTORED,
                'Force Deleted' => LogSettings::FORCE_DELETED,
                default => LogSettings::APPLICATION_REQUESTS,
            };

            $label = method_exists($model, 'getAuditLabel')
                ? $model->getAuditLabel()
                : class_basename($model).' #'.$model->getKey();

            app(AuditLogger::class)->log(
                event: $event,
                description: sprintf('%s %s', $label, strtolower($event)),
                subject: $model,
                logName: 'Models',
                tags: ['model', strtolower(str_replace(' ', '_', $event))],
                category: $category,
            );
        });

        Event::listen(UpdatesAvailable::class, function (UpdatesAvailable $event): void {
            app(AuditLogger::class)->log(
                event: 'Updates Available',
                description: 'The updater detected available application or dependency updates.',
                properties: [
                    'package_count' => count($event->packages ?? []),
                ],
                logName: 'Maintenance',
                tags: ['updater', 'updates_available'],
                category: LogSettings::UPDATES_AVAILABLE,
            );
        });

        Event::listen(UpdateSucceeded::class, function (UpdateSucceeded $event): void {
            app(AuditLogger::class)->log(
                event: 'Update Succeeded',
                description: 'The application update completed successfully.',
                logName: 'Maintenance',
                tags: ['updater', 'application_update', 'success'],
                category: LogSettings::UPDATE_SUCCEEDED,
            );
        });

        Event::listen(UpdateFailed::class, function (UpdateFailed $event): void {
            app(AuditLogger::class)->log(
                event: 'Update Failed',
                description: 'The application update failed.',
                success: false,
                failureReason: $event->exception->getMessage(),
                logName: 'Maintenance',
                tags: ['updater', 'application_update', 'failure'],
                category: LogSettings::UPDATE_FAILED,
            );
        });

        Event::listen(PackagesUpdated::class, function (PackagesUpdated $event): void {
            app(AuditLogger::class)->log(
                event: 'Packages Updated',
                description: sprintf('Composer package update completed%s.', $event->package ? ' for '.$event->package : ''),
                properties: ['package' => $event->package],
                logName: 'Maintenance',
                tags: ['updater', 'packages', 'success'],
                category: LogSettings::PACKAGES_UPDATED,
            );
        });

        Event::listen(PackagesUpdateFailed::class, function (PackagesUpdateFailed $event): void {
            app(AuditLogger::class)->log(
                event: 'Package Update Failed',
                description: sprintf('Composer package update failed%s.', $event->package ? ' for '.$event->package : ''),
                success: false,
                failureReason: $event->exception->getMessage(),
                properties: ['package' => $event->package],
                logName: 'Maintenance',
                tags: ['updater', 'packages', 'failure'],
                category: LogSettings::PACKAGES_UPDATE_FAILED,
            );
        });
    }
}
