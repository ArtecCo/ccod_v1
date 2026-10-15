<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserResourceAccess extends Model
{
    protected $table = 'user_resource_access';

    protected $fillable = [
        'user_id',
        'access_type',
        'business_unit_id',
        'project_id',
        'subscription_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }

    public function roleAssignments(): HasMany
    {
        return $this->hasMany(UserRoleAssignment::class, 'resource_access_id');
    }

    public function label(): string
    {
        return match ($this->access_type) {
            'business_unit' => $this->businessUnit?->name ?? 'Business Unit',
            'project' => $this->project?->name ?? 'Project',
            'subscription' => $this->subscription?->display_name ?? $this->subscription_id ?? 'Subscription',
            default => 'Resource',
        };
    }
}
