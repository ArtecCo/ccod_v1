<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureSecurityAssessment extends Model
{
    protected $table = 'azure_security_assessments';

    public $timestamps = false;

    protected $fillable = [
        'assessment_id',
        'subscription_id',
        'resource_id',
        'recommendation_id',
        'recommendation_name',
        'state',
        'severity',
        'description',
        'remediation_description',
        'assessment_type',
        'implementation_effort',
        'resource_source',
        'policy_definition_id',
        'categories',
        'threats',
        'user_impact',
        'status_cause',
        'status_description',
        'first_evaluated_at',
        'status_changed_at',
        'portal_link',
        'synced_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'threats' => 'array',
        'first_evaluated_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
