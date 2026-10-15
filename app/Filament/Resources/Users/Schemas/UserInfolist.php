<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Services\AccessAuthorizationService;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User')->schema([
                TextEntry::make('name'), TextEntry::make('email'), TextEntry::make('roleAssignments.role.name')->label('Roles')->listWithLineBreaks(),
                IconEntry::make('is_active')->boolean(), TextEntry::make('businessUnits.name')->label('Business Units')->listWithLineBreaks(), TextEntry::make('created_at')->dateTime()->label('Activation Date'), TextEntry::make('updated_at')->dateTime(),
            ])->columns(2),
            Section::make('Resource Access')->schema([
                RepeatableEntry::make('resourceAccesses')->hiddenLabel()->schema([
                    TextEntry::make('access_type')->label('Level')->badge(),
                    TextEntry::make('businessUnit.name')->label('Business Unit')->placeholder('—'),
                    TextEntry::make('project.name')->label('Project')->placeholder('—'),
                    TextEntry::make('subscription.display_name')->label('Subscription')->placeholder('—'),
                ])->columns(4),
            ])->collapsible(),
            Section::make('Accessible Subscriptions')->schema([
                RepeatableEntry::make('accessible_subscriptions')->hiddenLabel()->state(fn($record):array=>app(AccessAuthorizationService::class)->accessibleSubscriptions($record)->map(fn($s):array=>['name'=>$s->display_name,'subscription_id'=>$s->subscription_id,'role'=>$s->access_role?->name??'—','source'=>implode(', ',$s->access_sources??[])])->all())->schema([
                    TextEntry::make('name')->label('Subscription'), TextEntry::make('subscription_id')->label('Subscription ID')->copyable(), TextEntry::make('role')->badge(), TextEntry::make('source')->label('Access source'),
                ])->columns(4),
            ])->collapsible(),
        ]);
    }
}
