<?php

namespace App\Providers;

use App\Filament\Plugins\ShipLogPlugin;
use App\Models\Developer;
use App\Models\Documentation;
use App\Models\SubscriptionWebUrl;
use App\Policies\ActivityPolicy;
use App\Policies\DocumentationPolicy;
use App\Policies\SubscriptionWebUrlPolicy;
use App\Services\AuditLogger;
use App\Services\LogSettings;
use App\Support\FilamentStyling;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use TomaszBoloz\LaravelUpdater\Events\PackagesUpdateFailed;
use TomaszBoloz\LaravelUpdater\Events\PackagesUpdated;
use TomaszBoloz\LaravelUpdater\Events\UpdateFailed;
use TomaszBoloz\LaravelUpdater\Events\UpdatesAvailable;
use TomaszBoloz\LaravelUpdater\Events\UpdateSucceeded;

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
                        ->fab(enabled: false)
                        ->navigationLabel('Changelog')
                        ->pageTitle('Changelog')
                        ->resource(false),
                ),
                'developer' => $panel->plugin(
                    ShipLogPlugin::make()
                        ->usingDatabase()
                        ->fab(enabled: false)
                        ->navigationLabel('Changelog')
                        ->pageTitle('Changelog')
                        ->resource()
                        ->navigationGroup('Maintenance')
                        ->navigationSort(20),
                ),
                default => null,
            };

            $panel->userMenuItems([
                Action::make('account')
                    ->label('Account')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->url(fn (): string => \App\Filament\Pages\Profile::getUrl())
                    ->visible(fn (): bool => $panel->getId() === 'admin' && auth()->guard('web')->check())
                    ->sort(90),
                'logout' => fn (Action $action): Action => $action->sort(100),
                Action::make('changelog')
                    ->label('Changelog')
                    ->icon(Heroicon::OutlinedMegaphone)
                    ->url(fn (): string => \Ysfkaya\ShipLog\Filament\Pages\Changelog::getUrl())
                    ->visible(fn (): bool => auth()->check())
                    ->sort(110),
            ]);

            $darkModeCss = FilamentStyling::darkModeTheme() === 'blue'
                ? '\n                        html.dark body,\n                        html.dark .fi-body,\n                        html.dark .fi-main,\n                        html.dark .fi-main-ctn {\n                            background-color: #030712 !important;\n                        }\n\n                        html.dark .fi-sidebar,\n                        html.dark .fi-topbar {\n                            background-color: #030712 !important;\n                        }\n\n                        html.dark .fi-section,\n                        html.dark .fi-ta-ctn,\n                        html.dark .fi-wi-widget,\n                        html.dark .fi-card,\n                        html.dark .fi-modal-window {\n                            background-color: #101828 !important;\n                        }\n\n                        html.dark .fi-sidebar-search input,\n                        html.dark .fi-input-wrp {\n                            background-color: #101828 !important;\n                        }\n\n                        html.dark .fi-sidebar-item-button:not([data-active]),\n                        html.dark .fi-sidebar-group-btn {\n                            background-color: transparent !important;\n                        }\n\n                        html.dark .fi-sidebar-item-button[data-active] {\n                            background-color: #101828 !important;\n                        }\n                    '
                : '';

            FilamentView::registerRenderHook(
                'panels::styles.after',
                fn (): HtmlString => new HtmlString(
                    '<style>:root { --ccod-corner-radius: '.e(FilamentStyling::cornerRadius()).'; }'.$darkModeCss.'</style>'
                ),
            );
        });
    }

    public function boot(): void
    {
        Gate::policy(Documentation::class, DocumentationPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(SubscriptionWebUrl::class, SubscriptionWebUrlPolicy::class);

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
                properties: ['package_count' => count($event->packages ?? [])],
                logName: 'Maintenance',
                tags: ['updater', 'updates_available'],
                category: LogSettings::UPDATES_AVAILABLE,
            );
        });

        Event::listen(UpdateSucceeded::class, function (UpdateSucceeded $event): void {
            app(AuditLogger::class)->log(
                event: 'Update Succeeded',
                description: 'The updater completed an application or dependency update.',
                properties: ['package_count' => count($event->packages ?? [])],
                logName: 'Maintenance',
                tags: ['updater', 'update_succeeded'],
                category: LogSettings::UPDATES_SUCCEEDED,
            );
        });

        Event::listen(UpdateFailed::class, function (UpdateFailed $event): void {
            app(AuditLogger::class)->log(
                event: 'Update Failed',
                description: 'The updater failed to complete an application or dependency update.',
                success: false,
                failureReason: $event->exception?->getMessage(),
                logName: 'Maintenance',
                tags: ['updater', 'update_failed'],
                category: LogSettings::UPDATES_FAILED,
            );
        });

        Event::listen(PackagesUpdated::class, function (PackagesUpdated $event): void {
            app(AuditLogger::class)->log(
                event: 'Packages Updated',
                description: 'The updater completed a package update operation.',
                properties: ['package_count' => count($event->packages ?? [])],
                logName: 'Maintenance',
                tags: ['updater', 'packages_updated'],
                category: LogSettings::PACKAGES_UPDATED,
            );
        });

        Event::listen(PackagesUpdateFailed::class, function (PackagesUpdateFailed $event): void {
            app(AuditLogger::class)->log(
                event: 'Package Update Failed',
                description: 'The updater failed to complete a package update operation.',
                success: false,
                failureReason: $event->exception?->getMessage(),
                logName: 'Maintenance',
                tags: ['updater', 'packages_update_failed'],
                category: LogSettings::PACKAGES_UPDATE_FAILED,
            );
        });
    }
}
