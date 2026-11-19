<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class NotificationRule extends Model
{
    protected $fillable = [
        'event_key',
        'channel',
        'conditions',
        'condition_mode',
        'webhook_id',
        'template_id',
        'enabled',
        'recipient_type',
        'recipient_ids',
        'title',
        'message',
        'type',
        'severity',
        'action_label',
        'action_url',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $rule): void {
            if (($rule->channel ?? 'in_app') === 'webhook') {
                $rule->recipient_type ??= 'all';
            }

            if (($rule->channel ?? 'in_app') === 'webhook') {
                if (! $rule->webhook_id) {
                    throw ValidationException::withMessages([
                        'webhook_id' => 'Select a registered webhook destination.',
                    ]);
                }

                if (! $rule->template_id) {
                    throw ValidationException::withMessages([
                        'template_id' => 'Select a message template for this webhook rule.',
                    ]);
                }

                $template = TeamsWebhookTemplate::query()->find($rule->template_id);
                $eventKey = $rule->getAttributes()['event_key'] ?? null;

                if ($template && $eventKey && $template->event_key !== $eventKey) {
                    throw ValidationException::withMessages([
                        'template_id' => 'The selected template is configured for a different event. Choose a template for this event.',
                    ]);
                }
            }

            if (
                ($rule->channel ?? 'in_app') === 'in_app'
                && $rule->enabled
                && (\App\Support\NotificationEventCatalog::event((string) ($rule->getAttributes()['event_key'] ?? ''))['mandatory_in_app'] ?? false)
            ) {
                throw ValidationException::withMessages([
                    'event_key' => 'This event already has a mandatory in-app notification. Configure a webhook rule instead.',
                ]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'recipient_ids' => 'array',
            'conditions' => 'array',
            'event_key' => 'string',
        ];
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(TeamsWebhook::class, 'webhook_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TeamsWebhookTemplate::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Developer::class, 'updated_by');
    }
}
