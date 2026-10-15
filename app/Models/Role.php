<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system_role', 'is_active'];
    protected $casts = ['is_system_role' => 'boolean', 'is_active' => 'boolean'];
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class, 'permission_role'); }
    public function assignments(): HasMany { return $this->hasMany(UserRoleAssignment::class); }
}
