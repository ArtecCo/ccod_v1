<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleAssignment extends Model
{
    protected $fillable = ['user_id','role_id','resource_access_id','granted_by_user_id','starts_at','expires_at'];
    protected $casts = ['starts_at'=>'datetime','expires_at'=>'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function resourceAccess(): BelongsTo { return $this->belongsTo(UserResourceAccess::class, 'resource_access_id'); }
    public function grantedBy(): BelongsTo { return $this->belongsTo(User::class, 'granted_by_user_id'); }
    public function isActive(): bool { return ($this->starts_at === null || $this->starts_at->lte(now())) && ($this->expires_at === null || $this->expires_at->gt(now())); }
}
