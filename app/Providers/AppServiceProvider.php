<?php

namespace App\Providers;

use App\Models\Documentation;
use App\Policies\ActivityPolicy;
use App\Policies\DocumentationPolicy;
use App\Services\AuditLogger;
use Filament\Forms\Components\Field;
use Filament\Tables\Table;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditLogger::class);
    }

    public function boot(): void
    {
        Gate::policy(Documentation::class, DocumentationPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->extraAttributes([
                    'class' => 'compact-table [&_td]:py-1 [&_th]:py-1',
                ]);
        });

        Field::configureUsing(function (Field $field): void {
            $field->inlineLabel();
        });

        Event::listen(Login::class, function (Login $event): void {
            app(AuditLogger::class)->log(
                event: 'Login',
                description: 'Authentication succeeded.',
                success: true,
                properties: [
                    'guard' => $event->guard,
                    'remember' => $event->remember,
                ],
                logName: 'Access',
                tags: ['authentication', 'login'],
            );
        });

        Event::listen(Logout::class, function (Logout $event): void {
            app(AuditLogger::class)->log(
                event: 'Logout',
                description: 'Authentication session ended.',
                success: true,
                properties: [
                    'guard' => $event->guard,
                ],
                logName: 'Access',
                tags: ['authentication', 'logout'],
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
                properties: [
                    'guard' => $event->guard,
                    'identifier_type' => $identifier,
                ],
                logName: 'Access',
                tags: ['authentication', 'failure'],
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

            $label = method_exists($model, 'getAuditLabel')
                ? $model->getAuditLabel()
                : class_basename($model).' #'.$model->getKey();

            app(AuditLogger::class)->log(
                event: $event,
                description: sprintf('%s %s', $label, strtolower($event)),
                subject: $model,
                logName: 'Models',
                tags: ['model', strtolower(str_replace(' ', '_', $event))],
            );
        });
    }
}
