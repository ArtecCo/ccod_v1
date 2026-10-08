<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureCostForecast extends Model
{
    protected $table = 'azure_cost_forecasts';

    protected $primaryKey = 'subscription_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'forecast_amount',
        'currency',
        'fetched_at',
    ];

    protected $casts = [
        'forecast_amount' => 'decimal:4',
        'fetched_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            AzureSubscription::class,
            'subscription_id',
            'subscription_id'
        );
    }
}