<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\AzureSubscription;
use App\Models\UserResourceAccess;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Toggle::make('is_active')->default(true),
                TextInput::make('password')->password()->revealable()->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn (?string $state): bool => filled($state))->minLength(12),
                Select::make('businessUnits')->relationship('businessUnits', 'name')->label('Business Units')->multiple()->searchable()->preload()->columnSpanFull(),
            ]),
            Section::make('Resource Access')
                ->description('Resource access defines where the user can see resources. Business Unit includes all projects and subscriptions below it; Project includes all subscriptions assigned to that project; Subscription grants access to one subscription only.')
                ->schema([
                    Repeater::make('resourceAccesses')->relationship()->addActionLabel('Add resource access')->collapsible()->schema([
                        Select::make('access_type')->label('Access level')->options([
                            'business_unit' => 'Business Unit',
                            'project' => 'Project',
                            'subscription' => 'Subscription',
                        ])->required()->live(),
                        Select::make('business_unit_id')->label('Business Unit')->relationship('businessUnit', 'name')->searchable()->preload()->visible(fn (Get $get): bool => $get('access_type') === 'business_unit')->required(fn (Get $get): bool => $get('access_type') === 'business_unit'),
                        Select::make('project_id')->label('Project')->relationship('project', 'name')->searchable()->preload()->visible(fn (Get $get): bool => $get('access_type') === 'project')->required(fn (Get $get): bool => $get('access_type') === 'project'),
                        Select::make('subscription_id')->label('Subscription')->options(fn () => AzureSubscription::query()->orderBy('display_name')->pluck('display_name', 'subscription_id')->all())->searchable()->preload()->visible(fn (Get $get): bool => $get('access_type') === 'subscription')->required(fn (Get $get): bool => $get('access_type') === 'subscription'),
                    ])->columns(2)->columnSpanFull(),
                ]),
            Section::make('Role Assignments')
                ->description('Roles define what the user can do. Assignments point to one of the user resource-access entries above.')
                ->schema([
                    Repeater::make('roleAssignments')->relationship()->addActionLabel('Add role assignment')->collapsible()->schema([
                        Select::make('role_id')->relationship('role', 'name')->required()->searchable()->preload(),
                        Select::make('resource_access_id')->label('Resource access')->relationship('resourceAccess', 'id')->getOptionLabelFromRecordUsing(fn (UserResourceAccess $record): string => $record->label())->required()->searchable()->preload(),
                        DateTimePicker::make('starts_at')->default(now()),
                        DateTimePicker::make('expires_at'),
                    ])->columns(2)->columnSpanFull(),
                ]),
        ]);
    }
}
