<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use MrAdder\FilamentLogger\Facades\FilamentLogger;

class AuditLogger
{
    public function log(
        string $event,
        string $description,
        ?Model $subject = null,
        bool $success = true,
        ?string $failureReason = null,
        array $properties = [],
        string $logName = 'CCOD',
        array $tags = [],
    ): void {
        try {
            $properties = array_merge([
                'success' => $success,
                'failure_reason' => $failureReason,
                'request_id' => request()?->header('X-Request-ID') ?: request()?->attributes->get('request_id'),
                'url' => request()?->fullUrl(),
                'route' => request()?->route()?->getName(),
                'method' => request()?->method(),
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ], $properties);

            $properties = $this->removeNulls($properties);

            FilamentLogger::log(
                event: $event,
                description: $description,
                options: [
                    'logName' => $logName,
                    'causer' => $this->causer(),
                    'subject' => $subject,
                    'properties' => $properties,
                    'tags' => $tags,
                    'risk' => $success ? null : 'high',
                ],
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function request(
        Request $request,
        ?Response $response,
        int $durationMs,
        ?Throwable $exception = null,
    ): void {
        $status = $exception !== null ? 500 : $response?->getStatusCode();
        $success = $status !== null && $status < 400;

        $reason = $exception?->getMessage();
        if ($reason === null && $status !== null && $status >= 400) {
            $reason = Response::$statusTexts[$status] ?? 'Request failed';
        }

        $this->log(
            event: $request->isMethod('GET') ? 'Page Request' : 'Request',
            description: sprintf('%s %s (%s)', $request->method(), $request->path(), $status ?? 'unknown'),
            success: $success,
            failureReason: $reason,
            properties: [
                'status_code' => $status,
                'response_time_ms' => $durationMs,
                'is_refresh_or_navigation' => $request->isMethod('GET'),
                'is_filament_request' => str_starts_with($request->path(), 'admin')
                    || str_starts_with($request->path(), 'developer'),
            ],
            logName: 'Requests',
            tags: $success ? ['request'] : ['request', 'failure'],
        );
    }

    private function causer(): ?Model
    {
        return auth()->guard('developers')->user()
            ?? auth()->guard('web')->user();
    }

    private function removeNulls(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->removeNulls($value);
            }

            if ($values[$key] === null) {
                unset($values[$key]);
            }
        }

        return $values;
    }
}
