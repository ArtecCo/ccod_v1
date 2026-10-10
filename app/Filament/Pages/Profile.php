<?php

namespace App\Filament\Pages;

use App\Enums\AccessRequestTargetType;
use App\Models\AzureSubscription;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Services\AccessAuthorizationService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Profile extends Page
{
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $title = 'Profile';
    protected static ?string $slug = 'profile';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected string $view = 'filament.pages.profile';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getUser(): User
    {
        return auth()->guard('web')->user();
    }

    public function getTeams(): Collection
    {
        return $this->getUser()->teams()->with('subscriptions')->orderBy('name')->get();
    }

    public function getGrants(): Collection
    {
        return UserAccessGrant::query()
            ->where('user_id', $this->getUser()->getKey())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('starts_at')
            ->get();
    }

    public function effectiveRoleForTeam(int $teamId): ?string
    {
        $role = app(AccessAuthorizationService::class)->effectiveRole(
            $this->getUser(),
            AccessRequestTargetType::Team,
            $teamId,
        );

        return $role?->label();
    }

    public function effectiveRoleForSubscription(string $subscriptionId): ?string
    {
        $role = app(AccessAuthorizationService::class)->effectiveRole(
            $this->getUser(),
            AccessRequestTargetType::Subscription,
            $subscriptionId,
        );

        return $role?->label();
    }
}
