<?php

namespace App\Filament\Resources\MedicalDocuments\Schemas;

use App\Models\MedicalDocument;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class MedicalDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Select::make('type')
                            ->options([
                                'disease' => 'Disease',
                                'symptom' => 'Symptom',
                                'drug' => 'Drug',
                                'procedure' => 'Procedure',
                                'guideline' => 'Guideline',
                                'research' => 'Research',
                                'other' => 'Other',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('source')
                            ->label('Source / Publisher'),

                        Forms\Components\Toggle::make('verified')
                            ->label('Verified by Medical Reviewer')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Content')
                    ->schema([
                        Tabs::make('content_tabs')
                            ->tabs([
                                Tab::make('Upload File')
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('Document File')
                                            ->disk('local')
                                            ->directory('medical-documents')
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'text/plain',
                                                'text/markdown',
                                            ])
                                            ->maxSize(10240) // 10MB
                                            ->helperText('Supported formats: PDF, DOCX, TXT, MD (max 10MB)'),

                                        Forms\Components\Hidden::make('file_type'),
                                    ]),

                                Tab::make('Manual Content')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content')
                                            ->label('Content')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Embedding Status')
                    ->schema([
                        Forms\Components\Placeholder::make('embedding_status_display')
                            ->label('Status')
                            ->content(fn (?MedicalDocument $record): string => 
                                $record?->embedding_status ?? 'pending'
                            ),

                        Forms\Components\Placeholder::make('embedded_at_display')
                            ->label('Last Embedded')
                            ->content(fn (?MedicalDocument $record): string => 
                                $record?->embedded_at?->diffForHumans() ?? 'Never'
                            ),

                        Forms\Components\Placeholder::make('embeddings_count')
                            ->label('Total Chunks')
                            ->content(fn (?MedicalDocument $record): string => 
                                (string) ($record?->embeddings()->count() ?? 0)
                            ),

                        Forms\Components\Placeholder::make('embedding_error_display')
                            ->label('Error')
                            ->content(fn (?MedicalDocument $record): string => 
                                $record?->embedding_error ?? '-'
                            )
                            ->visible(fn (?MedicalDocument $record): bool => 
                                !empty($record?->embedding_error)
                            ),
                    ])
                    ->columns(3)
                    ->visible(fn (?MedicalDocument $record): bool => $record !== null),
            ]);
    }
}