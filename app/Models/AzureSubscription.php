<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'key_vault_reference',
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

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(
            Team::class,
            'team_subscription',
            'subscription_id',
            'team_id',
            'subscription_id',
            'id',
        )->withTimestamps();
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(AzureBudget::class, 'subscription_id', 'subscription_id');
    }

    public function costForecast(): HasOne
    {
        return $this->hasOne(AzureCostForecast::class, 'subscription_id', 'subscription_id');
    }

    public function billingResources(): HasMany
    {
        return $this->hasMany(BillingResource::class, 'subscription_id', 'subscription_id');
    }
}
