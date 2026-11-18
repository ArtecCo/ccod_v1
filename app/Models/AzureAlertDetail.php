<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureAlertDetail extends Model
{
    protected $table = 'azure_alert_details';

    protected $fillable = [
        'alert_id',
        'subscription_id',
        'raw_properties',
        'fetched_at',
    ];

    protected $casts = [
        'raw_properties' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(AzureAlert::class, 'alert_id');
    }
}
