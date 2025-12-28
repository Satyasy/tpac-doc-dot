<?php

namespace App\Filament\Resources\MedicalDocuments\Tables;

use App\Jobs\ProcessDocumentEmbedding;
use App\Models\MedicalDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class MedicalDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'disease' => 'danger',
                        'symptom' => 'warning',
                        'drug' => 'success',
                        'procedure' => 'info',
                        'guideline' => 'primary',
                        'research' => 'gray',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('file_type')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Text'),

                Tables\Columns\TextColumn::make('embedding_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-arrow-path',
                        'completed' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('embeddings_count')
                    ->counts('embeddings')
                    ->label('Chunks')
                    ->sortable(),

                Tables\Columns\IconColumn::make('verified')
                    ->boolean(),

                Tables\Columns\TextColumn::make('embedded_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'disease' => 'Disease',
                        'symptom' => 'Symptom',
                        'drug' => 'Drug',
                        'procedure' => 'Procedure',
                        'guideline' => 'Guideline',
                        'research' => 'Research',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('embedding_status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\TernaryFilter::make('verified'),
            ])
            ->recordActions([
                Action::make('process_embedding')
                    ->label('Process')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Process Document Embedding')
                    ->modalDescription('This will extract text from the document and create embeddings for RAG. This may take 1-3 minutes depending on document size. Please wait...')
                    ->action(function (MedicalDocument $record) {
                        // Set max execution time for large documents
                        set_time_limit(300);

                        // Use dispatchSync to process immediately without queue
                        try {
                            ProcessDocumentEmbedding::dispatchSync($record);

                            $record->refresh();
                            $chunksCount = $record->embeddings()->count();

                            Notification::make()
                                ->title('Processing Completed')
                                ->body("Document embedded successfully with {$chunksCount} chunks.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Processing Failed')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(
                        fn(MedicalDocument $record): bool =>
                        in_array($record->embedding_status, ['pending', 'failed'])
                    ),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('process_embeddings')
                        ->label('Process Embeddings')
                        ->icon('heroicon-o-cpu-chip')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $count = 0;
                            $failed = 0;
                            foreach ($records as $record) {
                                if (in_array($record->embedding_status, ['pending', 'failed'])) {
                                    try {
                                        ProcessDocumentEmbedding::dispatchSync($record);
                                        $count++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                    }
                                }
                            }

                            if ($failed > 0) {
                                Notification::make()
                                    ->title('Processing Completed with Errors')
                                    ->body("{$count} document(s) processed. {$failed} failed.")
                                    ->warning()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Processing Completed')
                                    ->body("{$count} document(s) processed successfully.")
                                    ->success()
                                    ->send();
                            }
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}