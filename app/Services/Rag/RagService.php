<?php

namespace App\Services\Rag;

use App\Contracts\EmbeddingServiceInterface;
use App\Contracts\VectorStoreInterface;
use App\Contracts\LlmServiceInterface;
use App\Contracts\DocumentParserInterface;
use App\Models\MedicalDocument;
use App\Models\MedicalEmbedding;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RagService
{
    private EmbeddingServiceInterface $embeddingService;
    private VectorStoreInterface $vectorStore;
    private LlmServiceInterface $llmService;
    private DocumentParserInterface $documentParser;

    // Chunking configuration
    private int $chunkSize = 1000; // characters
    private int $chunkOverlap = 200; // characters
    private string $namespace = 'medical_documents';

    public function __construct(
        EmbeddingServiceInterface $embeddingService,
        VectorStoreInterface $vectorStore,
        LlmServiceInterface $llmService,
        DocumentParserInterface $documentParser
    ) {
        $this->embeddingService = $embeddingService;
        $this->vectorStore = $vectorStore;
        $this->llmService = $llmService;
        $this->documentParser = $documentParser;
    }

    /**
     * Process and embed a medical document
     */
    public function processDocument(MedicalDocument $document): void
    {
        try {
            $document->markAsProcessing();

            // Parse document content
            $pages = $this->getDocumentContent($document);

            // Create chunks from content
            $chunks = $this->createChunks($pages);

            if (empty($chunks)) {
                throw new \RuntimeException('No content extracted from document');
            }

            // Delete existing embeddings if re-processing
            $this->deleteDocumentEmbeddings($document);

            // Generate embeddings and store in vector database
            $this->embedAndStore($document, $chunks);

            $document->markAsCompleted();

            Log::info('Document processed successfully', [
                'document_id' => $document->id,
                'chunks_count' => count($chunks),
            ]);
        } catch (\Exception $e) {
            $document->markAsFailed($e->getMessage());

            Log::error('Document processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Detect user intent from message
     */
    public function detectIntent(string $message): string
    {
        $message = strtolower(trim($message));

        // 1. Sapaan / Sosial ringan
        $greetings = [
            'halo',
            'hai',
            'hi',
            'hello',
            'hey',
            'pagi',
            'siang',
            'sore',
            'malam',
            'selamat pagi',
            'selamat siang',
            'selamat sore',
            'selamat malam',
            'apa kabar',
            'gimana',
            'lagi apa',
            'dok',
            'dokter',
            'assalamualaikum'
        ];
        foreach ($greetings as $greeting) {
            if (str_contains($message, $greeting) && strlen($message) < 50) {
                return 'greeting';
            }
        }

        // 2. Navigasi / Fitur aplikasi
        $navigation = [
            'fitur',
            'cara',
            'bagaimana',
            'apa saja',
            'bisa apa',
            'menu',
            'tracking',
            'track',
            'riwayat',
            'history',
            'bantuan',
            'help'
        ];
        foreach ($navigation as $nav) {
            if (str_contains($message, $nav) && !$this->containsHealthKeyword($message)) {
                return 'navigation';
            }
        }

        // 3. Di luar konteks kesehatan
        $offTopic = [
            'cuaca',
            'weather',
            'politik',
            'bola',
            'sepak bola',
            'film',
            'movie',
            'game',
            'musik',
            'resep masakan',
            'programming',
            'coding'
        ];
        foreach ($offTopic as $topic) {
            if (str_contains($message, $topic)) {
                return 'off_topic';
            }
        }

        // Default: health question
        return 'health';
    }

    /**
     * Check if message contains health keywords
     */
    private function containsHealthKeyword(string $message): bool
    {
        $healthKeywords = [
            'sakit',
            'nyeri',
            'demam',
            'pusing',
            'mual',
            'batuk',
            'flu',
            'obat',
            'vitamin',
            'gejala',
            'penyakit',
            'sehat',
            'kesehatan',
            'dokter',
            'rumah sakit',
            'diagnosis',
            'terapi',
            'diet',
            'nutrisi'
        ];

        foreach ($healthKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate greeting response
     */
    private function generateGreetingResponse(string $userName = null): string
    {
        $hour = (int) date('H');
        $timeGreeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Selamat pagi',
            $hour >= 12 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            default => 'Selamat malam'
        };

        $name = $userName ? ", **{$userName}**" : '';

        return "{$timeGreeting}{$name}! 👋\n\n" .
            "Saya **DocDot**, asisten kesehatan digital Anda. Ada yang bisa saya bantu hari ini?\n\n" .
            "Anda bisa:\n" .
            "- 🩺 **Konsultasi gejala** yang Anda rasakan\n" .
            "- 💊 **Cari informasi obat** di katalog kami\n" .
            "- 📊 **Track kesehatan** harian Anda\n" .
            "- 😊 **Track mood** dan kesehatan mental\n" .
            "- 📖 **Baca artikel kesehatan** terkini\n\n" .
            "Silakan ketik pertanyaan atau pilih salah satu opsi di atas! 😊";
    }

    /**
     * Generate navigation response
     */
    private function generateNavigationResponse(): string
    {
        return "Berikut fitur-fitur yang tersedia di **DocDot**: 🌟\n\n" .
            "### Fitur Utama:\n" .
            "1. **💬 Konsultasi AI** - Tanyakan gejala atau keluhan kesehatan Anda\n" .
            "2. **💊 Katalog Obat** - Cari informasi obat, dosis, dan efek samping\n" .
            "3. **📊 Health Tracking** - Pantau kesehatan fisik harian Anda\n" .
            "4. **😊 Mood Tracking** - Catat dan pantau kesehatan mental Anda\n" .
            "5. **📖 Artikel Kesehatan** - Baca artikel dan tips kesehatan terkini\n\n" .
            "### Cara Menggunakan:\n" .
            "- Ketik gejala atau pertanyaan kesehatan Anda di kolom chat\n" .
            "- Gunakan menu navigasi untuk akses fitur lainnya\n" .
            "- Kunjungi **/drug-catalog** untuk katalog obat\n" .
            "- Kunjungi **/articles** untuk artikel kesehatan\n\n" .
            "Ada yang ingin Anda tanyakan lebih lanjut? 😊";
    }

    /**
     * Generate off-topic response
     */
    private function generateOffTopicResponse(): string
    {
        return "Hmm, sepertinya pertanyaan Anda di luar konteks kesehatan 🤔\n\n" .
            "Saya adalah **DocDot**, asisten yang berfokus pada kesehatan. " .
            "Saya dapat membantu Anda dengan:\n\n" .
            "- 🩺 Informasi tentang gejala dan kondisi kesehatan\n" .
            "- 💊 Pencarian obat dan informasi farmasi\n" .
            "- 🥗 Tips gaya hidup sehat dan nutrisi\n" .
            "- 😊 Dukungan kesehatan mental\n\n" .
            "Apakah ada pertanyaan kesehatan yang bisa saya bantu? 😊";
    }

    /**
     * Query the RAG system with intent detection
     */
    public function query(string $question, int $topK = 5, ?array $filter = null, ?string $userRole = 'patient', ?string $userName = null): array
    {
        // Detect intent first
        $intent = $this->detectIntent($question);

        // Handle non-health intents without RAG
        if ($intent === 'greeting') {
            return [
                'answer' => $this->generateGreetingResponse($userName),
                'sources' => [],
                'context_count' => 0,
                'intent' => 'greeting',
            ];
        }

        if ($intent === 'navigation') {
            return [
                'answer' => $this->generateNavigationResponse(),
                'sources' => [],
                'context_count' => 0,
                'intent' => 'navigation',
            ];
        }

        if ($intent === 'off_topic') {
            return [
                'answer' => $this->generateOffTopicResponse(),
                'sources' => [],
                'context_count' => 0,
                'intent' => 'off_topic',
            ];
        }

        // For health questions, proceed with RAG
        // Generate embedding for the query
        $queryEmbedding = $this->embeddingService instanceof GeminiEmbeddingService
            ? $this->embeddingService->embedQuery($question)
            : $this->embeddingService->embed($question);

        // Search for similar vectors
        $results = $this->vectorStore->query(
            $queryEmbedding,
            $topK,
            $filter ?? [],
            $this->namespace
        );

        // Extract contexts from results
        $contexts = [];
        $sources = [];

        foreach ($results as $result) {
            $embedding = MedicalEmbedding::where('vector_id', $result['id'])->first();

            if ($embedding) {
                $contexts[] = $embedding->chunk_text;
                $sources[] = [
                    'document_id' => $embedding->document_id,
                    'document_title' => $embedding->document->title ?? 'Unknown',
                    'page' => $embedding->page_number,
                    'chunk_index' => $embedding->chunk_index,
                    'score' => $result['score'],
                ];
            }
        }

        // Generate response with context
        $response = '';
        if (!empty($contexts)) {
            $response = $this->llmService->generateWithContext($question, $contexts, null, $userRole);
        } else {
            // No documents found - use AI knowledge with disclaimer (interactive fallback)
            $response = $this->llmService->generateFallbackResponse($question, $userRole);
        }

        return [
            'answer' => $response,
            'sources' => $sources,
            'context_count' => count($contexts),
            'intent' => 'health',
        ];
    }

    /**
     * Search similar documents without generating response
     */
    public function search(string $query, int $topK = 10): array
    {
        $queryEmbedding = $this->embeddingService instanceof GeminiEmbeddingService
            ? $this->embeddingService->embedQuery($query)
            : $this->embeddingService->embed($query);

        $results = $this->vectorStore->query(
            $queryEmbedding,
            $topK,
            [],
            $this->namespace
        );

        $documents = [];

        foreach ($results as $result) {
            $embedding = MedicalEmbedding::with('document')
                ->where('vector_id', $result['id'])
                ->first();

            if ($embedding && $embedding->document) {
                $documents[] = [
                    'document' => $embedding->document,
                    'chunk_text' => $embedding->chunk_text,
                    'page' => $embedding->page_number,
                    'score' => $result['score'],
                ];
            }
        }

        return $documents;
    }

    /**
     * Get document content from file or content field
     */
    private function getDocumentContent(MedicalDocument $document): array
    {
        // Check both storage paths (private and public)
        $possiblePaths = [
            storage_path('app/private/' . $document->file_path),
            storage_path('app/' . $document->file_path),
        ];

        foreach ($possiblePaths as $path) {
            if ($document->file_path && file_exists($path)) {
                return $this->documentParser->parseWithPages($path);
            }
        }

        if ($document->content && !empty(strip_tags($document->content))) {
            return [['page' => 1, 'content' => strip_tags($document->content)]];
        }

        throw new \RuntimeException('Document has no content or file');
    }

    /**
     * Create chunks from document pages
     */
    private function createChunks(array $pages): array
    {
        $chunks = [];
        $chunkIndex = 0;

        foreach ($pages as $page) {
            $content = $page['content'];
            $pageNumber = $page['page'];

            // Split by sentences first for better chunks
            $sentences = preg_split('/(?<=[.!?])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
            $currentChunk = '';

            foreach ($sentences as $sentence) {
                if (strlen($currentChunk) + strlen($sentence) <= $this->chunkSize) {
                    $currentChunk .= ' ' . $sentence;
                } else {
                    if (!empty(trim($currentChunk))) {
                        $chunks[] = [
                            'index' => $chunkIndex++,
                            'page' => $pageNumber,
                            'text' => trim($currentChunk),
                            'token_count' => $this->estimateTokens($currentChunk),
                        ];
                    }

                    // Start new chunk with overlap
                    $overlapText = $this->getOverlapText($currentChunk);
                    $currentChunk = $overlapText . ' ' . $sentence;
                }
            }

            // Add remaining content
            if (!empty(trim($currentChunk))) {
                $chunks[] = [
                    'index' => $chunkIndex++,
                    'page' => $pageNumber,
                    'text' => trim($currentChunk),
                    'token_count' => $this->estimateTokens($currentChunk),
                ];
            }
        }

        return $chunks;
    }

    /**
     * Get overlap text from previous chunk
     */
    private function getOverlapText(string $text): string
    {
        if (strlen($text) <= $this->chunkOverlap) {
            return $text;
        }

        return substr($text, -$this->chunkOverlap);
    }

    /**
     * Estimate token count
     */
    private function estimateTokens(string $text): int
    {
        // Rough estimate: 1 token ≈ 3 characters for Indonesian/mixed content
        return (int) ceil(strlen($text) / 3);
    }

    /**
     * Embed chunks and store in vector database
     */
    private function embedAndStore(MedicalDocument $document, array $chunks): void
    {
        $batchSize = 10;
        $batches = array_chunk($chunks, $batchSize);

        foreach ($batches as $batch) {
            $texts = array_column($batch, 'text');
            $embeddings = $this->embeddingService->embedBatch($texts);

            $vectors = [];

            foreach ($batch as $i => $chunk) {
                if (empty($embeddings[$i])) {
                    continue; // Skip failed embeddings
                }

                $vectorId = Str::uuid()->toString();

                // Create local embedding record
                $medicalEmbedding = MedicalEmbedding::create([
                    'document_id' => $document->id,
                    'vector_id' => $vectorId,
                    'chunk_index' => $chunk['index'],
                    'chunk_text' => $chunk['text'],
                    'page_number' => $chunk['page'],
                    'token_count' => $chunk['token_count'],
                    'metadata' => [
                        'document_type' => $document->type,
                        'document_title' => $document->title,
                    ],
                ]);

                $vectors[] = [
                    'id' => $vectorId,
                    'vector' => $embeddings[$i],
                    'metadata' => [
                        'document_id' => $document->id,
                        'document_title' => $document->title,
                        'document_type' => $document->type,
                        'chunk_index' => $chunk['index'],
                        'page_number' => $chunk['page'],
                    ],
                ];
            }

            // Batch upsert to vector store
            if (!empty($vectors)) {
                $this->vectorStore->upsertBatch($vectors, $this->namespace);
            }

            // Small delay between batches to avoid rate limiting
            usleep(200000); // 200ms
        }
    }

    /**
     * Delete embeddings for a document
     */
    public function deleteDocumentEmbeddings(MedicalDocument $document): void
    {
        $embeddings = $document->embeddings;

        if ($embeddings->isEmpty()) {
            return;
        }

        $vectorIds = $embeddings->pluck('vector_id')->toArray();

        // Delete from vector store
        $this->vectorStore->delete($vectorIds, $this->namespace);

        // Delete local records
        MedicalEmbedding::where('document_id', $document->id)->delete();

        Log::info('Document embeddings deleted', [
            'document_id' => $document->id,
            'vectors_deleted' => count($vectorIds),
        ]);
    }

    /**
     * Re-process all documents
     */
    public function reprocessAllDocuments(): void
    {
        $documents = MedicalDocument::all();

        foreach ($documents as $document) {
            try {
                $this->processDocument($document);
            } catch (\Exception $e) {
                Log::error('Failed to reprocess document', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get service statistics
     */
    public function getStats(): array
    {
        $vectorStats = $this->vectorStore->stats();

        return [
            'total_documents' => MedicalDocument::count(),
            'processed_documents' => MedicalDocument::completed()->count(),
            'pending_documents' => MedicalDocument::pending()->count(),
            'failed_documents' => MedicalDocument::failed()->count(),
            'total_embeddings' => MedicalEmbedding::count(),
            'vector_store' => $vectorStats,
            'embedding_model' => $this->embeddingService->getModel(),
            'llm_model' => $this->llmService->getModel(),
        ];
    }

    /**
     * Set chunking configuration
     */
    public function setChunkConfig(int $size, int $overlap): self
    {
        $this->chunkSize = $size;
        $this->chunkOverlap = $overlap;

        return $this;
    }

    /**
     * Set namespace
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }
}
