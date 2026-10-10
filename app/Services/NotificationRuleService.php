<?php

namespace App\Services;

use App\Enums\NotificationEvent;
use App\Models\NotificationRule;
use App\Models\Team;
use App\Models\User;
use Throwable;

class NotificationRuleService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    public function trigger(
        NotificationEvent $event,
        array $context = [],
    ): int {
        $rule = NotificationRule::query()
            ->where('event_key', $event->value)
            ->where('enabled', true)
            ->first();

        if (! $rule) {
            return 0;
        }

        $title = $this->resolveTemplate($rule->title ?: $event->label(), $context);
        $message = $this->resolveTemplate($rule->message ?: $event->label(), $context);
        $actionUrl = $rule->action_url ? $this->resolveTemplate($rule->action_url, $context) : null;
        $actionLabel = $rule->action_label ? $this->resolveTemplate($rule->action_label, $context) : null;

        $recipientIds = $rule->recipient_ids ?? [];

        return match ($rule->recipient_type) {
            'all' => $this->notifications->send(
                title: $title,
                message: $message,
                type: $rule->type,
                severity: $rule->severity,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                allUsers: true,
            ),
            'teams' => $this->notifications->send(
                title: $title,
                message: $message,
                type: $rule->type,
                severity: $rule->severity,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                teamIds: array_map('intval', $recipientIds),
            ),
            'users' => $this->notifications->send(
                title: $title,
                message: $message,
                type: $rule->type,
                severity: $rule->severity,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                userIds: array_map('intval', $recipientIds),
            ),
            default => 0,
        };
    }

    private function resolveTemplate(string $value, array $context): string
    {
        return preg_replace_callback('/\{([a-zA-Z0-9_.-]+)\}/', function (array $matches) use ($context): string {
            $resolved = data_get($context, $matches[1]);

            return $resolved === null ? $matches[0] : (string) $resolved;
        }, $value) ?? $value;
    }
}
