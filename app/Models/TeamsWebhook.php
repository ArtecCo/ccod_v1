<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamsWebhook extends Model
{
    protected $fillable = [
        'name', 'category', 'template_id', 'event_keys', 'conditions', 'condition_mode',
        'destination_type', 'message_format', 'webhook_url', 'title_template',
        'message_template', 'enabled', 'last_delivery_status', 'last_sent_at', 'last_error',
    ];

    public function template()
    {
        return $this->belongsTo(TeamsWebhookTemplate::class, 'template_id');
    }

    protected function casts(): array
    {
        return [
            'webhook_url' => 'encrypted',
            'event_keys' => 'array',
            'conditions' => 'array',
            'enabled' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }
}
