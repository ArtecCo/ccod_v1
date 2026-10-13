<?php

namespace App\Filament\Resources\AzureSubscriptions;

use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Filament\Resources\AzureSubscriptions\Pages\CreateAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\EditAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\ListAzureSubscriptions;
use App\Filament\Resources\AzureSubscriptions\Pages\ViewAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\BillingResourcesRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\SubscriptionWebUrlsRelationManager;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionForm;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionInfolist;
use App\Filament\Resources\AzureSubscriptions\Tables\AzureSubscriptionsTable;
use App\Models\AzureSubscription;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSubscriptionResource extends Resource
{
    protected static ?string $model = AzureSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function canCreate(): bool
    {
        return auth()->user()?->role === UserRole::GlobalOwner;
    }

    public static function canView($record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isGlobal()
            || app(AccessAuthorizationService::class)->effectiveRole(
                $user,
                AccessRequestTargetType::Subscription,
                $record,
            ) !== null;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->role === UserRole::GlobalOwner;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->role === UserRole::GlobalOwner;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'display_name',
            'subscription_id',
        ];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Subscription ID' => $record->subscription_id,
        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user || $user->isGlobal()) {
            return $query;
        }

        // Record pages must resolve the actual model before authorization is
        // evaluated. Otherwise an inaccessible existing subscription is
        // filtered out here and Filament turns it into a misleading 404.
        // The record-level canView() check below then produces the proper 403.
        if (request()->route('record') !== null) {
            return $query;
        }

        return $query
            ->where(function (Builder $builder) use ($user): void {
                $builder->whereHas('teams.users', fn ($teamUsers) => $teamUsers->whereKey($user->getKey()))
                    ->orWhereExists(function ($grantQuery) use ($user): void {
                        $grantQuery->selectRaw('1')
                            ->from('user_access_grants')
                            ->whereColumn('user_access_grants.subscription_id', 'azure_subscriptions.subscription_id')
                            ->where('user_access_grants.user_id', $user->getKey())
                            ->where('user_access_grants.target_type', AccessRequestTargetType::Subscription->value)
                            ->where('user_access_grants.starts_at', '<=', now())
                            ->where(function ($expiry): void {
                                $expiry->whereNull('user_access_grants.expires_at')
                                    ->orWhere('user_access_grants.expires_at', '>', now());
                            });
                    })
                    ->orWhereExists(function ($grantQuery) use ($user): void {
                        $grantQuery->selectRaw('1')
                            ->from('user_access_grants')
                            ->join('team_subscription', 'team_subscription.team_id', '=', 'user_access_grants.team_id')
                            ->whereColumn('team_subscription.subscription_id', 'azure_subscriptions.subscription_id')
                            ->where('user_access_grants.user_id', $user->getKey())
                            ->where('user_access_grants.target_type', AccessRequestTargetType::Team->value)
                            ->where('user_access_grants.starts_at', '<=', now())
                            ->where(function ($expiry): void {
                                $expiry->whereNull('user_access_grants.expires_at')
                                    ->orWhere('user_access_grants.expires_at', '>', now());
                            });
                    });
            })
            ->whereNotExists(function ($overrideQuery) use ($user): void {
                $overrideQuery->selectRaw('1')
                    ->from('user_subscription_access_overrides')
                    ->whereColumn(
                        'user_subscription_access_overrides.subscription_id',
                        'azure_subscriptions.subscription_id',
                    )
                    ->where('user_subscription_access_overrides.user_id', $user->getKey())
                    ->where('user_subscription_access_overrides.override', 'revoked');
            });
    }

    public static function form(Schema $schema): Schema
    {
        return AzureSubscriptionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AzureSubscriptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AzureSubscriptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BillingResourcesRelationManager::class,
            SubscriptionWebUrlsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAzureSubscriptions::route('/'),
            'create' => CreateAzureSubscription::route('/create'),
            'view' => ViewAzureSubscription::route('/{record}'),
            'edit' => EditAzureSubscription::route('/{record}/edit'),
        ];
    }
}
