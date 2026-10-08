<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureBudget extends Model
{
    protected $table = 'azure_budgets';

    protected $fillable = [
        'subscription_id',
        'budget_name',
        'amount',
        'currency',
        'current_spend',
        'forecast_spend',
        'time_grain',
        'start_date',
        'end_date',
        'fetched_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'amount' => 'decimal:4',
        'current_spend' => 'decimal:4',
        'forecast_spend' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
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