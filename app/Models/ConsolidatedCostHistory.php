<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsolidatedCostHistory extends Model
{
    protected $table = 'consolidated_cost_histories';

    protected $fillable = [
        'scope_hash',
        'scope_filter',
        'period_start',
        'actual_cost',
        'budget_total',
        'forecast_total',
        'mom_difference',
        'mom_percentage',
        'currency',
        'subscription_count',
        'captured_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'actual_cost' => 'decimal:6',
        'budget_total' => 'decimal:6',
        'forecast_total' => 'decimal:6',
        'mom_difference' => 'decimal:6',
        'mom_percentage' => 'decimal:4',
        'captured_at' => 'datetime',
    ];
}
