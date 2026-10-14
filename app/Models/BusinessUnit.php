<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessUnit extends Model
{
    use HasFactory;

    protected $table = 'business_units';

    protected $fillable = [
        'name',
        'description',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'business_unit_id', 'user_id')
            ->withTimestamps();
    }

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(
            AzureSubscription::class,
            'team_subscription',
            'business_unit_id',
            'subscription_id',
            'id',
            'subscription_id',
        )->withTimestamps();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'business_unit_id');
    }
}
