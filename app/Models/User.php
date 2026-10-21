<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','is_active'];
    protected $hidden = ['password','remember_token'];

    public function userGroups(): BelongsToMany { return $this->belongsToMany(UserGroup::class, 'user_group_user')->withPivot(['starts_at', 'expires_at'])->withTimestamps(); }
    public function accessRequests(): HasMany { return $this->hasMany(AccessRequest::class); }
    public function canAccessPanel(Panel $panel): bool { return $panel->getId() === 'admin' && $this->is_active; }
    public static function getGloballySearchableAttributes(): array { return ['name','email']; }
    public static function getGlobalSearchResultDetails($record): array { return ['Email'=>$record->email]; }
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean']; }
}
