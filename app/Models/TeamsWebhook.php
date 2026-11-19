<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamsWebhook extends Model
{
    protected $fillable = [
        'name', 'description', 'destination_type', 'message_format', 'webhook_url',
        'enabled', 'last_delivery_status', 'last_sent_at', 'last_error',
    ];

    protected function casts(): array
    {
        return [
            'webhook_url' => 'encrypted',
            'enabled' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }
}
