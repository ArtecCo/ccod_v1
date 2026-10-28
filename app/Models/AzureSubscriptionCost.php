<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureSubscriptionCost extends Model
{
    protected $table = 'azure_subscription_costs';

    protected $fillable = [
        'subscription_id',
        'period_start',
        'period_end',
        'amount',
        'currency',
        'granularity',
        'source',
        'synced_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:6',
        'synced_at' => 'datetime',
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
