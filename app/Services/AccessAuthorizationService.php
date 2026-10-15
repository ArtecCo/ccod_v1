<?php

namespace App\Services;

use App\Enums\AccessRequestTargetType;
use App\Models\AccessRequest;
use App\Models\Application;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRoleAssignment;
use App\Models\UserSubscriptionAccessOverride;
use Illuminate\Support\Collection;

class AccessAuthorizationService
{
    public function hasPermission(User $user,string $permission,BusinessUnit|Project|Application|AzureSubscription|null $target=null):bool
    {
        $query=UserRoleAssignment::query()->where('user_id',$user->getKey())->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->whereHas('role.permissions',fn($q)=>$q->where('slug',$permission)->where('is_active',true));
        if($target===null)return $query->exists();
        foreach($query->with(['role.permissions','businessUnit','project','application'])->get() as $assignment)if($this->assignmentApplies($assignment,$target))return true;
        return false;
    }
    public function effectiveRole(User $user,AccessRequestTargetType|string $targetType,BusinessUnit|AzureSubscription|int|string $target):?Role{$target=$this->resolveTarget($targetType,$target);return $this->activeAssignments($user)->get()->filter(fn(UserRoleAssignment $a)=>$this->assignmentApplies($a,$target))->sortByDesc(fn($a)=>$this->roleWeight($a->role))->first()?->role;}
    public function accessibleSubscriptions(User $user):Collection{return AzureSubscription::query()->orderBy('display_name')->get()->filter(fn(AzureSubscription $s)=>$this->hasPermission($user,'subscriptions.view',$s)&&!$this->isSubscriptionRevoked($user,$s))->values()->map(function(AzureSubscription $s)use($user):AzureSubscription{$s->setAttribute('access_role',$this->effectiveRole($user,AccessRequestTargetType::Subscription,$s));$s->setAttribute('access_sources',$this->accessSources($user,$s));$s->setAttribute('access_revoked',false);return $s;});}
    public function canRequestRole(User $user,AccessRequestTargetType|string $targetType,BusinessUnit|AzureSubscription|int|string $target,Role $requestedRole):bool{if(!$user->is_active)return false;$current=$this->effectiveRole($user,$targetType,$target);return $current===null||$this->roleWeight($requestedRole)>$this->roleWeight($current);}
    public function canApprove(User $approver,AccessRequest $request):bool{if($approver->getKey()===$request->user_id)return false;$target=$request->target_type===AccessRequestTargetType::Team?BusinessUnit::query()->find($request->business_unit_id):AzureSubscription::query()->whereKey($request->subscription_id)->first();return $target!==null&&$this->hasPermission($approver,'users.roles.assign',$target);}
    public function canRevokeSubscriptionAccess(User|Developer $actor,User $targetUser,AzureSubscription $subscription):bool{if($targetUser->getKey()===$actor->getKey())return false;return $actor instanceof Developer||$this->hasPermission($actor,'users.scopes.assign',$subscription);}
    public function isSubscriptionRevoked(User $user,AzureSubscription $subscription):bool{return UserSubscriptionAccessOverride::query()->where('user_id',$user->getKey())->where('subscription_id',$subscription->getKey())->where('override','revoked')->exists();}
    public function approvers(AccessRequest $request):Collection{$target=$request->target_type===AccessRequestTargetType::Team?BusinessUnit::query()->find($request->business_unit_id):AzureSubscription::query()->whereKey($request->subscription_id)->first();if(!$target)return collect();return User::query()->where('is_active',true)->where('id','<>',$request->user_id)->get()->filter(fn(User $u)=>$this->hasPermission($u,'users.roles.assign',$target))->values();}
    public function roleWeight(Role $role):int{return match($role->slug){'viewer'=>10,'customer'=>15,'operator'=>20,'admin'=>30,'super_admin'=>40,default=>$role->permissions()->count()};}
    private function activeAssignments(User $user){return UserRoleAssignment::query()->with('role')->where('user_id',$user->getKey())->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()));}
    private function resolveTarget(AccessRequestTargetType|string $type,BusinessUnit|AzureSubscription|int|string $target):BusinessUnit|AzureSubscription{$type=$type instanceof AccessRequestTargetType?$type:AccessRequestTargetType::from($type);return $type===AccessRequestTargetType::Team?($target instanceof BusinessUnit?$target:BusinessUnit::query()->findOrFail($target)):($target instanceof AzureSubscription?$target:AzureSubscription::query()->whereKey($target)->firstOrFail());}
    private function assignmentApplies(UserRoleAssignment $a,BusinessUnit|Project|Application|AzureSubscription $target):bool
    {
        if($target instanceof BusinessUnit)return $a->scope_type==='business_unit'&&(int)$a->business_unit_id===(int)$target->getKey();
        if($target instanceof Project)return ($a->scope_type==='project'&&(int)$a->project_id===(int)$target->getKey())||($a->scope_type==='business_unit'&&(int)$a->business_unit_id===(int)$target->business_unit_id);
        if($target instanceof Application){$project=$target->relationLoaded('project')?$target->project:$target->project()->first();return ($a->scope_type==='application'&&(int)$a->application_id===(int)$target->getKey())||($project&&$this->assignmentApplies($a,$project));}
        if($a->scope_type==='subscription'&&(string)$a->subscription_id===(string)$target->getKey())return true;
        if($a->scope_type==='environment'&&(string)$a->environment===(string)$target->environment&&(int)$a->application_id===(int)$target->application_id)return true;
        if($a->scope_type==='application'&&(int)$a->application_id===(int)$target->application_id)return true;
        $application=$target->relationLoaded('application')?$target->application:$target->application()->first();if($application&&$this->assignmentApplies($a,$application))return true;
        foreach($target->businessUnits()->pluck('business_units.id') as $id)if($a->scope_type==='business_unit'&&(int)$a->business_unit_id===(int)$id)return true;
        return false;
    }
    private function accessSources(User $user,AzureSubscription $s):array{$sources=[];foreach($this->activeAssignments($user)->get() as $a)if($this->assignmentApplies($a,$s))$sources[]=$a->scope_type.': '.$a->role->name;return array_values(array_unique($sources));}
}
