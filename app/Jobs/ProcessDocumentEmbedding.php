<?php

namespace App\Jobs;

use App\Models\MedicalDocument;
use App\Services\Rag\RagService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The maximum number of seconds the job should run.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MedicalDocument $document
    ) {}

    /**
     * Execute the job.
     */
    public function handle(RagService $ragService): void
    {
        Log::info('Starting document embedding job', [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
        ]);

        try {
            $ragService->processDocument($this->document);

            Log::info('Document embedding job completed', [
                'document_id' => $this->document->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Document embedding job failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Document embedding job permanently failed', [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);

        $this->document->markAsFailed('Job failed after all retries: ' . $exception->getMessage());
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'document-embedding',
            'document:' . $this->document->id,
        ];
    }
}
