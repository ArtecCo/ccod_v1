<?php

use App\Http\Middleware\AuditRequestMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('azure:sync-security')->everyEightHours();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(MaintenanceModeMiddleware::class);
        $middleware->append(AuditRequestMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! $exception instanceof HttpExceptionInterface) {
                return null;
            }

            $status = $exception->getStatusCode();

            if (! in_array($status, [403, 404], true)) {
                return null;
            }

            return response()->view(
                "errors.{$status}",
                ['exception' => $exception],
                $status,
            );
        });
    })->create();
