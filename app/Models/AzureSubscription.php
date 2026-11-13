<?php

namespace App\\Models;

use App\\Enums\\NotificationEvent;
use App\\Services\\NotificationRuleService;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;
use Illuminate\\Database\\Eloquent\\Relations\\HasOne;
use Illuminate\\Support\\Facades\\DB;
use Throwable;

class AzureSubscription extends Model
{
    protected $table = 'azure_subscriptions';
    protected $primaryKey = 'subscription_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['subscription_id','display_name','state','tenant_id','authorization_source','application_id','key_vault_reference','health_status','security_score','mtd_spend_eur','last_synced'];
    protected $casts = ['security_score'=>'decimal:2','mtd_spend_eur'=>'decimal:2','last_synced'=>'datetime'];

    protected static function booted(): void
    {
        static::created(function (self $subscription): void {
            $subscription->dispatchLifecycleNotification(NotificationEvent::AzureSubscriptionConnected);
        });

        static::deleted(function (self $subscription): void {
            $subscription->dispatchLifecycleNotification(NotificationEvent::AzureSubscriptionDisconnected);
        });
    }

    private function dispatchLifecycleNotification(NotificationEvent $event): void
    {
        $context = [
            'subscription_id' => $this->subscription_id,
            'subscription_name' => $this->display_name,
            'subscription_state' => $this->state,
            'tenant_id' => $this->tenant_id,
            'authorization_source' => $this->authorization_source,
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

    /** @deprecated Legacy metadata retained for migration compatibility. */
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function projects(): BelongsToMany { return $this->belongsToMany(Project::class,'project_subscriptions','subscription_id','project_id','subscription_id','id'); }
    public function businessUnits(): BelongsToMany { return $this->belongsToMany(BusinessUnit::class,'team_subscription','subscription_id','business_unit_id','subscription_id','id')->withTimestamps(); }
    /** @deprecated Use businessUnits(). */
    public function teams(): BelongsToMany { return $this->businessUnits(); }
    public function documentation(): HasOne { return $this->hasOne(SubscriptionDocumentation::class,'subscription_id','subscription_id'); }
    public function budgets(): HasMany { return $this->hasMany(AzureBudget::class,'subscription_id','subscription_id'); }
    public function costForecast(): HasOne { return $this->hasOne(AzureCostForecast::class,'subscription_id','subscription_id'); }
    public function billingResources(): HasMany { return $this->hasMany(BillingResource::class,'subscription_id','subscription_id'); }
    public function webUrls(): HasMany { return $this->hasMany(SubscriptionWebUrl::class,'subscription_id','subscription_id'); }
    public function alerts(): HasMany { return $this->hasMany(AzureAlert::class,'subscription_id','subscription_id'); }
    public function costs(): HasMany { return $this->hasMany(AzureSubscriptionCost::class,'subscription_id','subscription_id'); }
    public function securityAssessments(): HasMany { return $this->hasMany(AzureSecurityAssessment::class,'subscription_id','subscription_id'); }
    public function securityScores(): HasMany { return $this->hasMany(AzureSecurityScore::class,'subscription_id','subscription_id'); }
}
