<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookCategory;
use App\Models\TeamsWebhookTemplate;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTeamsWebhookTemplates extends ListRecords
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageCategories')
                ->label('Manage categories')
                ->icon('heroicon-o-rectangle-stack')
                ->modalHeading('Create or rename a category')
                ->modalDescription('Choose an existing category to rename it, or leave it blank to create a new category.')
                ->schema([
                    Select::make('category_id')
                        ->label('Existing category')
                        ->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('Create a new category'),
                    TextInput::make('name')
                        ->label('Category name')
                        ->required()
                        ->maxLength(80),
                ])
                ->action(function (array $data): void {
                    $category = filled($data['category_id'] ?? null)
                        ? TeamsWebhookCategory::query()->find($data['category_id'])
                        : null;

                    $duplicate = TeamsWebhookCategory::query()
                        ->where('name', trim($data['name']))
                        ->when($category, fn ($query) => $query->whereKeyNot($category->getKey()))
                        ->exists();

                    if ($duplicate) {
                        Notification::make()
                            ->title('Category name already exists')
                            ->body('Choose a different category name.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($category) {
                        $category->update(['name' => trim($data['name'])]);
                        $message = 'Category renamed successfully.';
                    } else {
                        TeamsWebhookCategory::query()->create(['name' => trim($data['name'])]);
                        $message = 'Category created successfully.';
                    }

                    Notification::make()->title($message)->success()->send();
                }),

            Action::make('deleteCategory')
                ->label('Delete category')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->modalHeading('Delete an unused category')
                ->schema([
                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    $category = TeamsWebhookCategory::query()->find($data['category_id']);

                    if (! $category) {
                        return;
                    }

                    $hasTemplates = TeamsWebhookTemplate::query()->where('category', $category->slug)->exists();
                    $hasWebhooks = TeamsWebhook::query()->where('category', $category->slug)->exists();

                    if ($hasTemplates || $hasWebhooks) {
                        Notification::make()
                            ->title('Category cannot be deleted')
                            ->body('Delete or move its message templates and reassign its webhooks before deleting this category.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $category->delete();

                    Notification::make()->title('Category deleted successfully.')->success()->send();
                }),

            CreateAction::make(),
        ];
    }
}
