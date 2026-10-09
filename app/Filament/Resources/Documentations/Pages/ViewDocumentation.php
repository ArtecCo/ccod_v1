<?php

namespace App\Filament\Resources\Documentations\Pages;

use App\Filament\Resources\Documentations\DocumentationResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewDocumentation extends ViewRecord
{
    protected static string $resource = DocumentationResource::class;

    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextEntry::make('title')
                        ->label('')
                        ->size('3xl')
                        ->weight('bold')
                        ->extraAttributes([
                            'class' => 'tracking-tight',
                        ]),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('author.name')
                                ->label('Author')
                                ->badge()
                                ->color('gray'),

                            TextEntry::make('created_at')
                                ->label('Published')
                                ->dateTime()
                                ->badge()
                                ->color('gray'),

                            TextEntry::make('updated_at')
                                ->label('Last updated')
                                ->dateTime()
                                ->badge()
                                ->color('gray'),
                        ]),
                ])
                ->columnSpanFull(),

            Section::make('Article')
                ->schema([
                    TextEntry::make('content')
                        ->label('')
                        ->markdown()
                        ->columnSpanFull()
                        ->extraAttributes([
                            'class' => implode(' ', [
                                'text-base leading-7 text-gray-700 dark:text-gray-300',
                                '[&_p]:mb-5',
                                '[&_p:last-child]:mb-0',
                                '[&_strong]:font-semibold [&_b]:font-semibold',
                                '[&_em]:italic [&_i]:italic',
                                '[&_u]:underline',
                                '[&_h1]:mb-5 [&_h1]:mt-8 [&_h1]:text-3xl [&_h1]:font-bold [&_h1]:leading-tight',
                                '[&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:leading-tight',
                                '[&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:leading-tight',
                                '[&_h4]:mb-2 [&_h4]:mt-5 [&_h4]:text-lg [&_h4]:font-semibold',
                                '[&_ul]:mb-5 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-6',
                                '[&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:space-y-1 [&_ol]:pl-6',
                                '[&_li]:pl-1',
                                '[&_blockquote]:my-6 [&_blockquote]:border-l-4 [&_blockquote]:border-gray-300 [&_blockquote]:pl-5 [&_blockquote]:italic [&_blockquote]:text-gray-600 dark:[&_blockquote]:border-gray-600 dark:[&_blockquote]:text-gray-400',
                                '[&_a]:font-medium [&_a]:text-primary-600 [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:text-primary-500',
                                '[&_code]:rounded-md [&_code]:bg-gray-100 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-sm dark:[&_code]:bg-gray-800',
                                '[&_pre]:my-6 [&_pre]:overflow-x-auto [&_pre]:rounded-xl [&_pre]:bg-gray-950 [&_pre]:p-5 [&_pre]:font-mono [&_pre]:text-sm [&_pre]:leading-6 [&_pre]:text-gray-100',
                                '[&_pre_code]:bg-transparent [&_pre_code]:p-0 [&_pre_code]:text-inherit',
                                '[&_hr]:my-8 [&_hr]:border-gray-200 dark:[&_hr]:border-gray-700',
                                '[&_img]:my-6 [&_img]:max-w-full [&_img]:rounded-xl [&_img]:shadow-sm',
                                '[&_table]:my-6 [&_table]:w-full [&_table]:overflow-hidden [&_table]:rounded-xl [&_table]:border [&_table]:border-gray-200 dark:[&_table]:border-gray-700',
                                '[&_th]:bg-gray-50 [&_th]:px-4 [&_th]:py-3 [&_th]:text-left [&_th]:font-semibold dark:[&_th]:bg-gray-800',
                                '[&_td]:border-t [&_td]:border-gray-200 [&_td]:px-4 [&_td]:py-3 dark:[&_td]:border-gray-700',
                            ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
