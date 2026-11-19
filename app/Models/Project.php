<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','business_unit_id'];

    private array $notificationGroupIdsBeforeDelete = [];

    protected static function booted(): void
    {
        static::deleting(function (self $project): void {
            $project->notificationGroupIdsBeforeDelete = $project->userGroups()
                ->pluck('user_groups.id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        });

        static::deleted(function (self $project): void {
            $groupIds = $project->notificationGroupIdsBeforeDelete;

            if ($groupIds === []) {
                return;
            }

            DB::afterCommit(static function () use ($groupIds, $project): void {
                $service = app(\\App\\Services\\MandatoryNotificationService::class);

                foreach (UserGroup::query()->whereKey($groupIds)->get() as $group) {
                    $service->groupProjectChanged($group, $project, false);
                }
            });
        });
    }

    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function subscriptionAssignments(): HasMany { return $this->hasMany(ProjectSubscription::class); }
    public function subscriptions(): BelongsToMany { return $this->belongsToMany(AzureSubscription::class,'project_subscriptions','project_id','subscription_id','id','subscription_id'); }
    public function userGroups(): BelongsToMany { return $this->belongsToMany(UserGroup::class, 'project_user_group')->withTimestamps(); }
}
