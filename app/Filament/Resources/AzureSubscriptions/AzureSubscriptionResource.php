<?php

namespace App\Filament\Resources\AzureSubscriptions;

use App\Filament\Resources\AzureSubscriptions\Pages\CreateAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\EditAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Pages\ListAzureSubscriptions;
use App\Filament\Resources\AzureSubscriptions\Pages\ViewAzureSubscription;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionForm;
use App\Filament\Resources\AzureSubscriptions\Schemas\AzureSubscriptionInfolist;
use App\Filament\Resources\AzureSubscriptions\Tables\AzureSubscriptionsTable;
use App\Models\AzureSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AzureSubscriptionResource extends Resource
{
    protected static ?string $model = AzureSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'display_name';

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
            //
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
