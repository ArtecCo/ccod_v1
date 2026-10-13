<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureAlert extends Model
{
    protected $table = 'azure_alerts';

    protected $fillable = [
        'azure_alert_id',
        'name',
        'severity',
        'affected_resource',
        'target_resource_type',
        'alert_condition',
        'user_response',
        'monitor_service',
        'signal_type',
        'fire_time',
        'last_modified_time',
        'subscription_id',
        'target_resource_group',
        'suppression_status',
    ];

    protected $casts = [
        'fire_time' => 'datetime',
        'last_modified_time' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
