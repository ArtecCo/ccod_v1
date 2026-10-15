<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleAssignment extends Model
{
    protected $fillable = ['user_id','role_id','scope_type','business_unit_id','project_id','application_id','environment','subscription_id','granted_by_user_id','starts_at','expires_at'];
    protected $casts = ['starts_at'=>'datetime','expires_at'=>'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function grantedBy(): BelongsTo { return $this->belongsTo(User::class, 'granted_by_user_id'); }
    public function isActive(): bool { return ($this->starts_at === null || $this->starts_at->lte(now())) && ($this->expires_at === null || $this->expires_at->gt(now())); }
}
