<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscriptionAccessOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'override',
        'revoked_by_type',
        'revoked_by_id',
        'revoked_at',
        'reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }

    protected function casts(): array
    {
        return [
            'revoked_at' => 'datetime',
        ];
    }
}
