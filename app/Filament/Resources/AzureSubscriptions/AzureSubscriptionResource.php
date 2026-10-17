<?php

namespace App\Filament\Resources\AzureSubscriptions;

use App\Filament\Resources\AzureSubscriptions\Pages\CreateAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\EditAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\ListAzureSubscriptions;
use App\Filament\Resources\AzureSubscriptions\Pages\ViewAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\BillingResourcesRelationManager;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionForm;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionInfolist;
use App\Filament\Resources\AzureSubscriptions\Tables\AzureSubscriptionsTable;
use App\Models\AzureSubscription;
use App\Models\User;
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

    public static function canViewAny(): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasAnyResourceAccess($user);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasResourceAccess($user, $record);
    }

    public static function canEdit($record): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, 'subscriptions.edit', $record);
    }

    public static function canDelete($record): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, 'subscriptions.delete', $record);
    }

    public static function getGloballySearchableAttributes(): array { return ['display_name', 'subscription_id']; }
    public static function getGlobalSearchResultDetails($record): array { return ['Subscription ID' => $record->subscription_id]; }
    public static function getGlobalSearchResultUrl($record): string { return static::getUrl('view', ['record' => $record]); }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return parent::getEloquentQuery();
        }

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return app(AccessAuthorizationService::class)->accessibleSubscriptionQuery($user);
    }

    public static function form(Schema $schema): Schema { return AzureSubscriptionForm::configure($schema); }
    public static function infolist(Schema $schema): Schema { return AzureSubscriptionInfolist::configure($schema); }
    public static function table(Table $table): Table { return AzureSubscriptionsTable::configure($table); }
    public static function getRelations(): array { return [BillingResourcesRelationManager::class]; }
    public static function getPages(): array { return ['index' => ListAzureSubscriptions::route('/'), 'create' => CreateAzureSubscription::route('/create'),'view'=>ViewAzureSubscription::route('/{record}'),'edit'=>EditAzureSubscription::route('/{record}/edit')]; }
}
