<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\NotificationRule;
use App\Models\User;
use App\Models\UserGroup;
use App\Support\NotificationEventCatalog;
use Illuminate\Support\Collection;
use Throwable;

class NotificationRuleService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly TeamsWebhookNotifier $teamsWebhooks,
    ) {}

    public function trigger(NotificationEvent|string $event, array $context = []): int
    {
        $eventKey = $event instanceof NotificationEvent
            ? $event->value
            : NotificationEventCatalog::normalizeKey($event);
        $eventLabel = NotificationEventCatalog::event($eventKey)['label'] ?? $eventKey;

        $context = array_merge($context, [
            'event_key' => $eventKey,
            'event_label' => $eventLabel,
            'occurred_at' => $context['occurred_at'] ?? now()->toIso8601String(),
        ]);

        $sent = 0;
        $rules = NotificationRule::query()
            ->where('event_key', $eventKey)
            ->where('enabled', true)
            ->with(['webhook', 'template'])
            ->orderBy('id')
            ->get();

        foreach ($rules as $rule) {
            if (! $this->matchesConditions($rule->conditions ?? [], $rule->condition_mode ?? 'all', $context)) {
                continue;
            }

            if (($rule->channel ?? 'in_app') === 'webhook') {
                if ($rule->webhook) {
                    try {
                        $sent += (int) $this->teamsWebhooks->deliverRule(
                            $rule->webhook,
                            $eventKey,
                            $context,
                            $rule->template,
                        );
                    } catch (Throwable $exception) {
                        report($exception);
                    }
                }

                continue;
            }

            // Hardcoded mandatory in-app defaults are independent and cannot be
            // replaced or duplicated by configurable in-app rules.
            if (($this->eventIsMandatory($eventKey))) {
                continue;
            }

            $title = $this->renderTemplate($rule->title ?: $eventLabel, $context);
            $message = $this->renderTemplate($rule->message ?: $event->label(), $context);
            $actionUrl = $rule->action_url ? $this->renderTemplate($rule->action_url, $context) : null;
            $actionLabel = $rule->action_label ? $this->renderTemplate($rule->action_label, $context) : null;

            $recipientIds = match ($rule->recipient_type) {
                'all' => [],
                'users' => $this->activeUserIds($rule->recipient_ids ?? []),
                'user_groups' => $this->activeUserIdsForGroups($rule->recipient_ids ?? []),
                // Preserve legacy team-based rules and recipient selections.
                                default => [],
            };

            $allUsers = $rule->recipient_type === 'all';
            if (! $allUsers && $recipientIds === []) {
                continue;
            }

            $sent += $this->notifications->send(
                title: $title,
                message: $message,
                type: $rule->type,
                severity: $rule->severity,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                allUsers: $allUsers,
                userIds: $recipientIds,
            );
        }

        return $sent;
    }

    private function eventIsMandatory(string $eventKey): bool
    {
        return (bool) (\App\Support\NotificationEventCatalog::event($eventKey)['mandatory_in_app'] ?? false);
    }

    private function activeUserIds(array $ids): array
    {
        return User::query()->where('is_active', true)->whereKey($ids)->pluck('id')->map(fn ($id): int => (int) $id)->all();
    }

    private function activeUserIdsForGroups(array $ids): array
    {
        $users = collect();

        UserGroup::query()->whereKey($ids)->with(['users' => function ($query): void {
            $query->where('users.is_active', true)
                ->where(function ($membership): void {
                    $membership->whereNull('user_group_user.starts_at')
                        ->orWhere('user_group_user.starts_at', '<=', now());
                })
                ->where(function ($membership): void {
                    $membership->whereNull('user_group_user.expires_at')
                        ->orWhere('user_group_user.expires_at', '>', now());
                });
        }])->get()->each(function (UserGroup $group) use ($users): void {
            $group->users->each(fn (User $user) => $users->push((int) $user->getKey()));
        });

        return $users->unique()->values()->all();
    }

    private function matchesConditions(array $conditions, string $mode, array $context): bool
    {
        if ($conditions === []) {
            return true;
        }

        $results = [];
        foreach ($conditions as $condition) {
            $field = trim((string) ($condition['field'] ?? ''));
            $operator = (string) ($condition['operator'] ?? 'equals');
            $actual = $field !== '' ? data_get($context, $field) : null;
            $expected = $condition['value'] ?? $condition['value_text'] ?? null;
            $left = is_scalar($actual) ? mb_strtolower((string) $actual) : null;
            $right = is_scalar($expected) ? mb_strtolower((string) $expected) : null;

            $results[] = match ($operator) {
                'exists' => $actual !== null,
                'not_exists' => $actual === null,
                'equals' => $actual !== null && $left === $right,
                'not_equals' => $actual !== null && $left !== $right,
                'contains' => is_array($actual)
                    ? in_array($expected, $actual, true)
                    : ($left !== null && $right !== null && str_contains($left, $right)),
                'not_contains' => is_array($actual)
                    ? ! in_array($expected, $actual, true)
                    : ($left !== null && $right !== null && ! str_contains($left, $right)),
                'in' => $actual !== null && (function () use ($actual, $left, $expected): bool {
                    $expectedValues = is_array($expected) ? $expected : explode(',', (string) $expected);
                    $expectedValues = array_map(
                        static fn ($value): string => mb_strtolower(trim((string) $value)),
                        $expectedValues,
                    );

                    if (is_array($actual)) {
                        $actualValues = array_map(
                            static fn ($value): string => mb_strtolower(trim((string) $value)),
                            $actual,
                        );

                        return count(array_intersect($actualValues, $expectedValues)) > 0;
                    }

                    return in_array($left, $expectedValues, true);
                })(),
                'greater_than', 'greater_or_equal', 'less_than', 'less_or_equal' => $this->compare($actual, $expected, $operator),
                default => false,
            };
        }

        return $mode === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    private function compare(mixed $actual, mixed $expected, string $operator): bool
    {
        if ($actual === null || $expected === null) {
            return false;
        }

        if (is_numeric($actual) && is_numeric($expected)) {
            $left = (float) $actual;
            $right = (float) $expected;
        } else {
            $left = strtotime((string) $actual);
            $right = strtotime((string) $expected);
            if ($left === false || $right === false) {
                return false;
            }
        }

        return match ($operator) {
            'greater_than' => $left > $right,
            'greater_or_equal' => $left >= $right,
            'less_than' => $left < $right,
            'less_or_equal' => $left <= $right,
            default => false,
        };
    }

    private function renderTemplate(string $value, array $context): string
    {
        return preg_replace_callback('/\{([a-zA-Z0-9_.-]+)\}/', function (array $matches) use ($context): string {
            $resolved = data_get($context, $matches[1]);
            if (is_array($resolved) || is_object($resolved)) {
                $resolved = json_encode($resolved, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            return $resolved === null ? $matches[0] : (string) $resolved;
        }, $value) ?? $value;
    }
}
