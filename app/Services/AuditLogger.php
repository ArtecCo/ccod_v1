<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

            $causer = $this->causer();

            // Write directly to Spatie Activitylog instead of going through the
            // Filament Logger event layer. This keeps audit persistence independent
            // from package listeners and guarantees that no old/new model values are
            // written by this application-level audit logger.
            Activity::query()->create([
                'log_name' => $logName,
                'event' => $event,
                'description' => $description,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'causer_type' => $causer?->getMorphClass(),
                'causer_id' => $causer?->getKey(),
                'properties' => $this->removeNulls([
                    ...$properties,
                    'tags' => $tags,
                    'risk' => $success ? null : 'high',
                ]),
            ]);
        } catch (Throwable $exception) {
            // Auditing must never turn a successful application operation into a 500.
            report($exception);
        }
    }

    public function request(
        Request $request,
        ?Response $response,
        int $durationMs,
        ?Throwable $exception = null,
    ): void {
        if ($this->shouldSkipRequest($request)) {
            return;
        }

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
            failureReason: $this->sanitizeFailureReason($reason),
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

    private function shouldSkipRequest(Request $request): bool
    {
        if ($request->is('up') || $request->is('favicon.ico')) {
            return true;
        }

        return preg_match('/\.(?:css|js|map|png|jpe?g|gif|svg|ico|woff2?|ttf|webp)$/i', $request->path()) === 1;
    }

    private function sanitizeFailureReason(?string $reason): ?string
    {
        if ($reason === null) {
            return null;
        }

        $reason = preg_replace('/(password|token|secret|authorization|api[_-]?key)\s*[=:]\s*[^\s,;]+/i', '$1=[REDACTED]', $reason) ?? $reason;

        return mb_substr($reason, 0, 1000);
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
