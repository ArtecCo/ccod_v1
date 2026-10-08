<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AzureSubscription extends Model
{
    protected $table = 'azure_subscriptions';

    protected $primaryKey = 'subscription_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'display_name',
        'health_status',
        'security_score',
        'mtd_spend_eur',
        'last_synced',
    ];

    protected $casts = [
        'security_score' => 'decimal:2',
        'mtd_spend_eur' => 'decimal:2',
        'last_synced' => 'datetime',
    ];

    public function budgets(): HasMany
    {
        return $this->hasMany(
            AzureBudget::class,
            'subscription_id',
            'subscription_id'
        );
    }

    public function costForecast(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            AzureCostForecast::class,
            'subscription_id',
            'subscription_id'
        );
    }

    public function billingResources(): HasMany
    {
        return $this->hasMany(
            BillingResource::class,
            'subscription_id',
            'subscription_id'
        );
    }
}