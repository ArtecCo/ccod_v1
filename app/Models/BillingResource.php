<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingResource extends Model
{
    protected $table = 'billing_resources';

    protected $primaryKey = 'resource_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'resource_id',
        'subscription_id',
        'name',
        'resource_type',
        'region',
        'sku',
        'cost_eur',
    ];

    protected $casts = [
        'cost_eur' => 'decimal:2',
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