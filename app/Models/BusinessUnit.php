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

    protected static function booted(): void
    {
        static::created(function (self $businessUnit): void {
            try {
                app(\App\Services\NotificationRuleService::class)->trigger('business_unit.created', [
                    'business_unit_id' => (int) $businessUnit->getKey(),
                    'business_unit_name' => (string) $businessUnit->name,
                ]);
            } catch (\Throwable $exception) {
                report($exception);
            }
        });

        static::deleted(function (self $businessUnit): void {
            try {
                app(\App\Services\NotificationRuleService::class)->trigger('business_unit.deleted', [
                    'business_unit_id' => (int) $businessUnit->getKey(),
                    'business_unit_name' => (string) $businessUnit->name,
                ]);
            } catch (\Throwable $exception) {
                report($exception);
            }
        });
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
