<?php

declare(strict_types=1);

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class AzureCostForecastSnapshot extends Model
{
    protected $table = 'azure_cost_forecast_snapshots';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'forecast_period',
        'forecast_amount',
        'currency',
        'captured_on',
        'captured_at',
        'synced_at',
    ];

    protected $casts = [
        'forecast_period' => 'date',
        'forecast_amount' => 'decimal:6',
        'captured_on' => 'date',
        'captured_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
