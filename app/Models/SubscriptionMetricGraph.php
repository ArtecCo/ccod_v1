<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionMetricGraph extends Model
{
    protected $fillable = ['subscription_id', 'resource_id', 'title', 'series', 'position'];

    protected $casts = ['series' => 'array', 'position' => 'integer'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(BillingResource::class, 'resource_id', 'resource_id');
    }
}
