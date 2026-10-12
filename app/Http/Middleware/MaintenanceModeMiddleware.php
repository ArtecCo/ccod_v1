<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isLocalRequest($request) || $this->isDeveloperPanel($request)) {
            return $next($request);
        }

        if (! is_file(storage_path('framework/maintenance.flag'))) {
            return $next($request);
        }

        return response()->view('errors.503', [], 503);
    }

    private function isLocalRequest(Request $request): bool
    {
        return in_array($request->ip(), ['127.0.0.1', '::1'], true);
    }

    private function isDeveloperPanel(Request $request): bool
    {
        return $request->is('developer') || $request->is('developer/*');
    }
}
