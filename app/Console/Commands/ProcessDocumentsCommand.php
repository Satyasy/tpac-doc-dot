<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDocumentEmbedding;
use App\Models\MedicalDocument;
use App\Services\Rag\RagService;
use Illuminate\Console\Command;

class ProcessDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rag:process 
                            {--id= : Process a specific document by ID}
                            {--pending : Process only pending documents}
                            {--failed : Reprocess failed documents}
                            {--all : Process all documents}
                            {--sync : Process synchronously instead of queuing}';

    /**
     * The console command description.
     */
    protected $description = 'Process medical documents for RAG embedding';

    /**
     * Execute the console command.
     */
    public function handle(RagService $ragService): int
    {
        if ($this->option('id')) {
            return $this->processById($ragService);
        }

        if ($this->option('all')) {
            return $this->processAll($ragService);
        }

        if ($this->option('pending')) {
            return $this->processPending($ragService);
        }

        if ($this->option('failed')) {
            return $this->processFailed($ragService);
        }

        $this->error('Please specify an option: --id, --pending, --failed, or --all');
        return self::FAILURE;
    }

    private function processById(RagService $ragService): int
    {
        $document = MedicalDocument::find($this->option('id'));

        if (!$document) {
            $this->error('Document not found.');
            return self::FAILURE;
        }

        $this->info("Processing document: {$document->title}");

        if ($this->option('sync')) {
            try {
                $ragService->processDocument($document);
                $this->info('Document processed successfully.');
            } catch (\Exception $e) {
                $this->error("Failed: {$e->getMessage()}");
                return self::FAILURE;
            }
        } else {
            ProcessDocumentEmbedding::dispatch($document);
            $this->info('Document queued for processing.');
        }

        return self::SUCCESS;
    }

    private function processAll(RagService $ragService): int
    {
        $documents = MedicalDocument::all();
        $count = $documents->count();

        if ($count === 0) {
            $this->warn('No documents found.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Process all {$count} documents?")) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($documents as $document) {
            if ($this->option('sync')) {
                try {
                    $ragService->processDocument($document);
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Failed to process {$document->title}: {$e->getMessage()}");
                }
            } else {
                ProcessDocumentEmbedding::dispatch($document);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Processed/queued {$count} documents.");

        return self::SUCCESS;
    }

    private function processPending(RagService $ragService): int
    {
        $documents = MedicalDocument::pending()->get();
        $count = $documents->count();

        if ($count === 0) {
            $this->info('No pending documents.');
            return self::SUCCESS;
        }

        $this->info("Processing {$count} pending documents...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($documents as $document) {
            if ($this->option('sync')) {
                try {
                    $ragService->processDocument($document);
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Failed: {$e->getMessage()}");
                }
            } else {
                ProcessDocumentEmbedding::dispatch($document);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        return self::SUCCESS;
    }

    private function processFailed(RagService $ragService): int
    {
        $documents = MedicalDocument::failed()->get();
        $count = $documents->count();

        if ($count === 0) {
            $this->info('No failed documents.');
            return self::SUCCESS;
        }

        $this->info("Reprocessing {$count} failed documents...");

        foreach ($documents as $document) {
            $this->line("- {$document->title}: {$document->embedding_error}");
        }

        if (!$this->confirm('Reprocess these documents?')) {
            return self::SUCCESS;
        }

        foreach ($documents as $document) {
            if ($this->option('sync')) {
                try {
                    $ragService->processDocument($document);
                    $this->info("✓ {$document->title}");
                } catch (\Exception $e) {
                    $this->error("✗ {$document->title}: {$e->getMessage()}");
                }
            } else {
                ProcessDocumentEmbedding::dispatch($document);
                $this->info("Queued: {$document->title}");
            }
        }

        return self::SUCCESS;
    }
}
