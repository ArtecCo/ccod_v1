<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionWebUrl extends Model
{
    protected $table = 'subscription_web_urls';

    protected $fillable = [
        'subscription_id',
        'url',
        'url_hash',
        'is_active',
        'is_live',
        'http_status',
        'response_time_ms',
        'ssl_status',
        'ssl_provider',
        'ssl_subject',
        'ssl_issuer',
        'ssl_valid_from',
        'ssl_expires_at',
        'ssl_days_remaining',
        'last_checked_at',
        'last_check_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_live' => 'boolean',
        'ssl_status' => 'boolean',
        'ssl_valid_from' => 'datetime',
        'ssl_expires_at' => 'datetime',
        'last_checked_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            AzureSubscription::class,
            'subscription_id',
            'subscription_id',
        );
    }
}
