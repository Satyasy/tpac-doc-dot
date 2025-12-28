<?php

namespace App\Console\Commands;

use App\Services\Rag\RagService;
use Illuminate\Console\Command;

class RagQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rag:query 
                            {question : The question to ask}
                            {--top-k=5 : Number of context chunks to retrieve}';

    /**
     * The console command description.
     */
    protected $description = 'Query the RAG system with a question';

    /**
     * Execute the console command.
     */
    public function handle(RagService $ragService): int
    {
        $question = $this->argument('question');
        $topK = (int) $this->option('top-k');

        $this->info("Querying: {$question}");
        $this->newLine();

        try {
            $result = $ragService->query($question, $topK);

            $this->info('Answer:');
            $this->line($result['answer']);
            $this->newLine();

            if (!empty($result['sources'])) {
                $this->info('Sources:');
                $this->table(
                    ['Document', 'Page', 'Score'],
                    array_map(fn ($s) => [
                        $s['document_title'],
                        $s['page'] ?? '-',
                        round($s['score'], 4),
                    ], $result['sources'])
                );
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Query failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
