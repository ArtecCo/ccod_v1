<?php

namespace App\Models;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_group_id',
        'target_type',
        'target_name',
        'requested_role',
        'reason',
        'duration',
        'requested_until',
        'status',
        'decided_by_type',
        'decided_by_id',
        'decided_at',
        'decision_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    protected function casts(): array
    {
        return [
            'target_type' => AccessRequestTargetType::class,
            'duration' => AccessRequestDuration::class,
            'status' => AccessRequestStatus::class,
            'requested_until' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function getAuditLabel(): string
    {
        return 'Access request #'.$this->getKey();
    }
}
