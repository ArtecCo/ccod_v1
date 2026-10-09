<?php

namespace App\Filament\Resources\Documentations\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set): void {
                    if (filled($state)) {
                        $set('slug', str($state)->slug()->toString());
                    }
                }),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            MarkdownEditor::make('content')
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
