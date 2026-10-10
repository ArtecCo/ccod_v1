<?php

namespace App\Models;

use App\Enums\NotificationEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRule extends Model
{
    protected $fillable = [
        'event_key',
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

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'recipient_ids' => 'array',
            'event_key' => NotificationEvent::class,
        ];
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
