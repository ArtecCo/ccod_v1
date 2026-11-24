<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SslReminderRule extends Model
{
    protected $fillable = [
        'threshold_days',
        'severity',
        'webhook_ids',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'threshold_days' => 'integer',
            'webhook_ids' => 'array',
            'enabled' => 'boolean',
        ];
    }
}
