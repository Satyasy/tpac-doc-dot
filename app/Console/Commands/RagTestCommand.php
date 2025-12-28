<?php

namespace App\Console\Commands;

use App\Services\Rag\GeminiEmbeddingService;
use App\Services\Rag\PineconeVectorStore;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RagTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rag:test 
                            {--gemini : Test Gemini embedding only}
                            {--pinecone : Test Pinecone connection only}
                            {--full : Run full integration test}';

    /**
     * The console command description.
     */
    protected $description = 'Test RAG service connections (Gemini & Pinecone)';

    /**
     * Execute the console command.
     */
    public function handle(
        GeminiEmbeddingService $embeddingService,
        PineconeVectorStore $vectorStore
    ): int {
        if ($this->option('gemini')) {
            return $this->testGemini($embeddingService);
        }

        if ($this->option('pinecone')) {
            return $this->testPinecone($vectorStore);
        }

        if ($this->option('full')) {
            return $this->testFull($embeddingService, $vectorStore);
        }

        // Default: test both
        $this->testGemini($embeddingService);
        $this->newLine();
        $this->testPinecone($vectorStore);
        
        return self::SUCCESS;
    }

    private function testGemini(GeminiEmbeddingService $embeddingService): int
    {
        $this->info('🧪 Testing Gemini Embedding API...');
        $this->newLine();

        try {
            $result = $embeddingService->testConnection();

            if ($result['success']) {
                $this->info('✅ Gemini Connection: SUCCESS');
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['Model', $result['model']],
                        ['Dimension', $result['dimension']],
                        ['Expected Dimension', $result['expected_dimension']],
                        ['Match', $result['dimension'] === $result['expected_dimension'] ? '✓ Yes' : '✗ No'],
                    ]
                );

                if ($result['dimension'] !== $result['expected_dimension']) {
                    $this->warn("⚠️  Dimension mismatch! Expected {$result['expected_dimension']}, got {$result['dimension']}");
                    return self::FAILURE;
                }

                return self::SUCCESS;
            } else {
                $this->error('❌ Gemini Connection: FAILED');
                $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Gemini Connection: FAILED');
            $this->error('Exception: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function testPinecone(PineconeVectorStore $vectorStore): int
    {
        $this->info('🧪 Testing Pinecone Connection...');
        $this->newLine();

        try {
            $stats = $vectorStore->stats();

            if (!empty($stats)) {
                $this->info('✅ Pinecone Connection: SUCCESS');
                
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['Index', config('services.pinecone.index')],
                        ['Total Vectors', $stats['totalVectorCount'] ?? 0],
                        ['Dimension', $stats['dimension'] ?? 'N/A'],
                        ['Namespaces', count($stats['namespaces'] ?? [])],
                    ]
                );

                if (!empty($stats['namespaces'])) {
                    $this->newLine();
                    $this->info('Namespaces:');
                    foreach ($stats['namespaces'] as $ns => $data) {
                        $this->line("  - {$ns}: {$data['vectorCount']} vectors");
                    }
                }

                return self::SUCCESS;
            } else {
                $this->warn('⚠️  Pinecone returned empty stats (might be a new index)');
                return self::SUCCESS;
            }
        } catch (\Exception $e) {
            $this->error('❌ Pinecone Connection: FAILED');
            $this->error('Exception: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function testFull(
        GeminiEmbeddingService $embeddingService,
        PineconeVectorStore $vectorStore
    ): int {
        $this->info('🧪 Running Full Integration Test...');
        $this->newLine();

        // Step 1: Test embedding
        $this->info('Step 1: Generate test embedding...');
        try {
            $testText = "Ini adalah teks pengujian untuk sistem RAG medis DocDot.";
            $embedding = $embeddingService->embed($testText);

            if (empty($embedding)) {
                $this->error('Failed to generate embedding');
                return self::FAILURE;
            }

            $this->info("  ✓ Generated embedding with {$embedding[0]} dimensions");
            $this->line("  First 5 values: " . implode(', ', array_map(fn($v) => round($v, 4), array_slice($embedding, 0, 5))));
        } catch (\Exception $e) {
            $this->error('Embedding failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Step 2: Upsert to Pinecone
        $this->newLine();
        $this->info('Step 2: Upsert to Pinecone...');
        try {
            $testId = 'test-' . Str::uuid()->toString();
            $success = $vectorStore->upsert(
                $testId,
                $embedding,
                [
                    'text' => $testText,
                    'test' => true,
                    'timestamp' => now()->toIso8601String(),
                ],
                'test_namespace'
            );

            if (!$success) {
                $this->error('Failed to upsert vector');
                return self::FAILURE;
            }

            $this->info("  ✓ Upserted vector with ID: {$testId}");
        } catch (\Exception $e) {
            $this->error('Upsert failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Step 3: Query similar vectors
        $this->newLine();
        $this->info('Step 3: Query for similar vectors...');
        sleep(2); // Wait for index to update
        
        try {
            $queryText = "Pengujian sistem kesehatan";
            $queryEmbedding = $embeddingService->embedQuery($queryText);
            
            $results = $vectorStore->query(
                $queryEmbedding,
                5,
                [],
                'test_namespace'
            );

            $this->info("  ✓ Query returned " . count($results) . " results");

            if (!empty($results)) {
                $this->table(
                    ['ID', 'Score', 'Test'],
                    array_map(fn($r) => [
                        substr($r['id'], 0, 30) . '...',
                        round($r['score'], 4),
                        $r['metadata']['test'] ?? 'N/A',
                    ], array_slice($results, 0, 5))
                );
            }
        } catch (\Exception $e) {
            $this->error('Query failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Step 4: Cleanup
        $this->newLine();
        $this->info('Step 4: Cleanup test vectors...');
        try {
            $deleted = $vectorStore->delete([$testId], 'test_namespace');
            $this->info("  ✓ Cleaned up test vector");
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Cleanup failed: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Full Integration Test: PASSED');

        return self::SUCCESS;
    }
}
