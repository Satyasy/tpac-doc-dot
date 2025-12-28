<?php

namespace App\Console\Commands;

use App\Services\Rag\RagService;
use Illuminate\Console\Command;

class RagStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rag:stats';

    /**
     * The console command description.
     */
    protected $description = 'Show RAG service statistics';

    /**
     * Execute the console command.
     */
    public function handle(RagService $ragService): int
    {
        $this->info('RAG Service Statistics');
        $this->newLine();

        try {
            $stats = $ragService->getStats();

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Documents', $stats['total_documents']],
                    ['Processed Documents', $stats['processed_documents']],
                    ['Pending Documents', $stats['pending_documents']],
                    ['Failed Documents', $stats['failed_documents']],
                    ['Total Embeddings', $stats['total_embeddings']],
                    ['Embedding Model', $stats['embedding_model']],
                    ['LLM Model', $stats['llm_model']],
                ]
            );

            if (!empty($stats['vector_store'])) {
                $this->newLine();
                $this->info('Vector Store Stats:');
                $this->line(json_encode($stats['vector_store'], JSON_PRETTY_PRINT));
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to get stats: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
