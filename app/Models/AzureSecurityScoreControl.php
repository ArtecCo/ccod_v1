<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureSecurityScoreControl extends Model
{
    protected $table = 'azure_security_score_controls';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'control_id',
        'display_name',
        'current_score',
        'max_score',
        'percentage_score',
        'healthy_resource_count',
        'unhealthy_resource_count',
        'not_applicable_resource_count',
        'weight',
        'captured_at',
        'synced_at',
    ];

    protected $casts = [
        'current_score' => 'decimal:4',
        'max_score' => 'decimal:4',
        'percentage_score' => 'decimal:5',
        'healthy_resource_count' => 'integer',
        'unhealthy_resource_count' => 'integer',
        'not_applicable_resource_count' => 'integer',
        'weight' => 'integer',
        'captured_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
