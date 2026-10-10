<?php

namespace App\Models;

use App\Enums\AccessRequestTargetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccessGrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_type',
        'team_id',
        'subscription_id',
        'target_name',
        'role',
        'granted_by_type',
        'granted_by_id',
        'source_request_id',
        'starts_at',
        'expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }

    public function sourceRequest(): BelongsTo
    {
        return $this->belongsTo(AccessRequest::class, 'source_request_id');
    }

    protected function casts(): array
    {
        return [
            'target_type' => AccessRequestTargetType::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        $now = now();

        return $this->starts_at <= $now
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function getAuditLabel(): string
    {
        return 'Access grant #'.$this->getKey();
    }
}
