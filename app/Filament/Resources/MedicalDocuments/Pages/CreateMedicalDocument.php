<?php

namespace App\Filament\Resources\MedicalDocuments\Pages;

use App\Filament\Resources\MedicalDocuments\MedicalDocumentResource;
use App\Jobs\ProcessDocumentEmbedding;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMedicalDocument extends CreateRecord
{
    protected static string $resource = MedicalDocumentResource::class;

    protected function afterCreate(): void
    {
        // Set max execution time for large documents
        set_time_limit(300);

        try {
            // Automatically dispatch embedding job after document creation (sync)
            ProcessDocumentEmbedding::dispatchSync($this->record);

            // Refresh the record to get updated status
            $this->record->refresh();

            if ($this->record->embedding_status === 'completed') {
                Notification::make()
                    ->title('Document Created & Embedded')
                    ->body('The document has been successfully created and embedded.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Document Created')
                    ->body('Document created but embedding status: ' . $this->record->embedding_status)
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Document Created, Embedding Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
