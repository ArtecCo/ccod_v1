<?php

namespace App\Http\Middleware;

use App\Settings\MaintenanceSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(MaintenanceSettings::class);

        if (! $settings->enabled || $this->isLocalRequest($request) || $this->isDeveloperPanel($request)) {
            return $next($request);
        }

        return response()->view('errors.503', [
            'exception' => null,
        ], 503);
    }

    private function isLocalRequest(Request $request): bool
    {
        return in_array($request->ip(), ['127.0.0.1', '::1'], true)
            || $request->getHost() === 'localhost';
    }

    private function isDeveloperPanel(Request $request): bool
    {
        return $request->is('developer') || $request->is('developer/*');
    }
}
