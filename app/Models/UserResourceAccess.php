<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserResourceAccess extends Model
{
    protected $table = 'user_resource_access';
    protected $fillable = ['user_id', 'access_type', 'business_unit_id', 'project_id', 'subscription_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id'); }
    public function roleAssignments(): HasMany { return $this->hasMany(UserRoleAssignment::class, 'resource_access_id'); }

    public function label(): string
    {
        return match ($this->access_type) {
            'business_unit' => 'Business Unit: ' . ($this->businessUnit?->name ?? 'Unknown'),
            'project' => 'Project: ' . ($this->project?->name ?? 'Unknown'),
            'subscription' => 'Subscription: ' . ($this->subscription?->display_name ?? $this->subscription_id),
            default => ucfirst(str_replace('_', ' ', $this->access_type)),
        };
    }
}
