<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\AzureSubscription;
use App\Models\Project;
use App\Models\ProjectSubscription;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    private static function subscriptionDefault(?Project $record, string $environment): ?string
    {
        if (!$record) return null;
        return ProjectSubscription::query()->where('project_id',$record->getKey())->where('environment',$environment)->value('subscription_id');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                Select::make('business_unit_id')->label('Business Unit')->relationship('businessUnit','name')->required()->searchable()->preload(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]),
            Section::make('Environment subscriptions')
                ->description('Every environment is optional. A project may have Prod only, Prod + Dev, Prod + QA, all three, or any supported combination.')
                ->schema([
                    Select::make('subscriptionAssignments.prod')->label('Production subscription')->options(fn()=>AzureSubscription::query()->orderBy('display_name')->pluck('display_name','subscription_id')->all())->default(fn(?Project $record)=>self::subscriptionDefault($record,'prod'))->searchable()->preload(),
                    Select::make('subscriptionAssignments.dev')->label('Development subscription')->options(fn()=>AzureSubscription::query()->orderBy('display_name')->pluck('display_name','subscription_id')->all())->default(fn(?Project $record)=>self::subscriptionDefault($record,'dev'))->searchable()->preload(),
                    Select::make('subscriptionAssignments.qa')->label('QA subscription')->options(fn()=>AzureSubscription::query()->orderBy('display_name')->pluck('display_name','subscription_id')->all())->default(fn(?Project $record)=>self::subscriptionDefault($record,'qa'))->searchable()->preload(),
                ])->columns(3),
        ]);
    }
}
