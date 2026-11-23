<?php

namespace App\Models;

use App\Services\NotificationRuleService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionWebUrl extends Model
{
    protected static function booted(): void
    {
        static::created(function (self $webUrl): void {
            if ($webUrl->is_active) {
                $webUrl->dispatchMonitoringLifecycleEvent('web_url.activated');
            }
        });

        static::updated(function (self $webUrl): void {
            if ($webUrl->wasChanged('is_active')) {
                $webUrl->dispatchMonitoringLifecycleEvent(
                    $webUrl->is_active ? 'web_url.activated' : 'web_url.deactivated',
                );
            }
        });
    }

    private function dispatchMonitoringLifecycleEvent(string $event): void
    {
        $context = [
            'web_url_id' => (int) $this->getKey(),
            'url' => (string) $this->url,
            'domain' => (string) (parse_url($this->url, PHP_URL_HOST) ?: $this->url),
            'subscription_id' => (string) $this->subscription_id,
            'subscription_name' => $this->subscription?->display_name,
            'occurred_at' => now()->toIso8601String(),
        ];

        DB::afterCommit(static function () use ($event, $context): void {
            try {
                app(NotificationRuleService::class)->trigger($event, $context);
            } catch (Throwable $exception) {
                report($exception);
            }
        });
    }

    protected $table = 'subscription_web_urls';

    protected $fillable = [
        'subscription_id',
        'url',
        'url_hash',
        'is_active',
        'use_proxy',
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
        'use_proxy' => 'boolean',
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
