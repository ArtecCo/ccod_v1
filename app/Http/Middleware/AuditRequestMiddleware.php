<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $started = hrtime(true);
        $exception = null;

        try {
            $response = $next($request);
        } catch (Throwable $throwable) {
            $exception = $throwable;
            throw $throwable;
        } finally {
            $duration = (int) round((hrtime(true) - $started) / 1_000_000);

            app(AuditLogger::class)->request(
                request: $request,
                response: $exception === null ? ($response ?? null) : null,
                durationMs: $duration,
                exception: $exception,
            );
        }

        return $response;
    }
}
