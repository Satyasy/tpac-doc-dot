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

        // 2. Cek apakah user meminta saran/analisis berdasarkan data personal (mood, health tracking)
        // Ini harus dicek SEBELUM navigation check
        if ($this->isPersonalHealthDataQuery($message)) {
            return 'personal_health_advice';
        }

        // 3. Navigasi / Fitur aplikasi (hanya jika bukan health query)
        $navigation = [
            'fitur',
            'cara pakai',
            'apa saja fitur',
            'bisa apa saja',
            'menu',
            'bantuan',
            'help',
            'cara kerja',
            'tutorial'
        ];
        foreach ($navigation as $nav) {
            if (str_contains($message, $nav) && !$this->containsHealthKeyword($message)) {
                return 'navigation';
            }
        }

        // 4. Di luar konteks kesehatan - dengan deteksi lebih baik
        if ($this->isOffTopicQuery($message)) {
            return 'off_topic';
        }

        // Default: health question
        return 'health';
    }

    /**
     * Check if message is asking for personal health advice based on tracked data
     */
    private function isPersonalHealthDataQuery(string $message): bool
    {
        $personalDataKeywords = [
            'mood saya',
            'tracking mood',
            'mood tracking',
            'health tracking',
            'tracking saya',
            'data saya',
            'berdasarkan tracking',
            'berdasarkan data',
            'stress saya',
            'tidur saya',
            'aktivitas saya',
            'kondisi saya',
            'skor saya',
            'rata-rata saya',
            'riwayat mood',
            'riwayat kesehatan',
            'hasil tracking',
            'data tracking',
        ];

        // Juga cek pattern yang menunjukkan user sharing personal data
        $personalDataPatterns = [
            '/mood\s*:?\s*\d+\s*\/\s*\d+/i',           // mood 1/5 atau mood: 1/5
            '/stress\s*:?\s*\d+\s*\/\s*\d+/i',         // stress 5/5
            '/tidur\s*:?\s*\d+\s*(jam|hours?)/i',      // tidur 6 jam
            '/skor\s*(rata-rata|average)?\s*:?\s*\d+/i', // skor rata-rata: 2
            '/perasaan\s*:?\s*(sad|sedih|happy|senang|cemas|anxious)/i', // perasaan: sad
        ];

        foreach ($personalDataKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        foreach ($personalDataPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if message is off-topic (not health related)
     */
    private function isOffTopicQuery(string $message): bool
    {
        $offTopicKeywords = [
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
            'coding',
            'bahasa korea',
            'bahasa jepang',
            'bahasa mandarin',
            'bahasa inggris',
            'bahasa arab',
            'translate',
            'terjemahkan',
            'artinya apa',
            'apa artinya',
            'lirik lagu',
            'berita',
            'gosip',
            'drama',
            'anime',
            'crypto',
            'bitcoin',
            'saham',
            'forex',
            'matematika',
            'fisika',
            'kimia',
            'sejarah',
            'geografi',
            'agama',
            'hukum',
        ];

        foreach ($offTopicKeywords as $topic) {
            if (str_contains($message, $topic)) {
                return true;
            }
        }

        // Deteksi pattern terjemahan: "bahasa X nya Y" atau "Y bahasa X"
        if (preg_match('/bahasa\s*[a-z]+\s*(nya|dari|ke|dalam)/i', $message)) {
            return true;
        }

        // Deteksi "X nya apa dalam bahasa Y" atau sejenisnya
        if (preg_match('/(apa|gimana|bagaimana).*bahasa\s*[a-z]+/i', $message)) {
            return true;
        }

        return false;
    }

    /**
     * Check if message contains health keywords
     */
    private function containsHealthKeyword(string $message): bool
    {
        $healthKeywords = [
            // Physical health
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
            'nutrisi',
            'alergi',
            'infeksi',
            'luka',
            'patah',
            'bengkak',
            // Mental health
            'mood',
            'stress',
            'stres',
            'cemas',
            'anxiety',
            'depresi',
            'depression',
            'tidur',
            'insomnia',
            'mental',
            'psikolog',
            'psikiater',
            'sedih',
            'sad',
            'bahagia',
            'happy',
            'emosi',
            'perasaan',
            'burnout',
            'panik',
            'trauma',
            'ptsd',
            'bipolar',
            'overthinking',
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
     * Generate personalized health advice based on user's tracked data
     */
    private function generatePersonalHealthAdvice(string $question, string $userRole = 'patient', ?string $userName = null): string
    {
        $nameGreeting = $userName ? "**{$userName}**" : 'Anda';

        $systemPrompt = <<<PROMPT
Kamu adalah asisten kesehatan mental DocDot yang berempati dan suportif.

NAMA USER: {$nameGreeting}

TUGAS:
User telah membagikan data tracking mood/kesehatan mereka. Berikan saran yang SPESIFIK dan PERSONAL berdasarkan data yang mereka berikan dalam pesan.

ATURAN PENTING:
1. FOKUS pada data yang user berikan (mood score, stress level, jam tidur, aktivitas, perasaan)
2. Berikan saran KONKRET dan ACTIONABLE yang bisa dilakukan HARI INI
3. Gunakan bahasa yang hangat, empatik, dan suportif
4. Jangan memberikan diagnosis klinis
5. Untuk kondisi serius (mood ≤2/5 atau stress ≥4/5), SELALU sarankan konsultasi profesional
6. Pertimbangkan korelasi antar data (misal: tidur kurang + stress tinggi)

FORMAT RESPONS:
1. 💭 **Pemahaman** - Akui dan validasi kondisi user berdasarkan data mereka
2. 🔍 **Analisis Singkat** - Jelaskan apa yang mungkin terjadi dan korelasi antar data
3. 💡 **Saran Praktis** - 3-5 tips konkret yang bisa dilakukan:
   - Untuk tidur kurang: saran sleep hygiene
   - Untuk stress tinggi: teknik relaksasi cepat
   - Untuk mood rendah: aktivitas mood booster
   - Untuk aktivitas work: saran work-life balance
4. 🌟 **Motivasi** - Kata-kata penyemangat yang personal
5. ⚠️ **Catatan Penting** - Jika kondisi serius, sarankan bantuan profesional

CONTOH RESPONS YANG BAIK untuk Mood 1/5, Stress 5/5, Tidur 6 jam:
"Hai {$nameGreeting}, saya melihat hari ini cukup berat untuk Anda... 💙

💭 **Pemahaman**
Dengan mood 1/5 dan stress 5/5, ditambah tidur hanya 6 jam, tubuh dan pikiran Anda pasti sedang kelelahan...

🔍 **Yang Mungkin Terjadi**
Kurang tidur dapat memperburuk stress dan menurunkan mood. Aktivitas kerja yang intens juga berkontribusi...

💡 **Yang Bisa Anda Lakukan Hari Ini**
1. **Istirahat mikro** - Ambil 5 menit setiap jam untuk bernapas dalam
2. **Hidrasi** - Minum air putih, dehidrasi memperburuk stress
3. **Jalan kaki singkat** - 10 menit di luar ruangan jika memungkinkan
4. **Tidur lebih awal** - Targetkan 7-8 jam malam ini
5. **Batasi kafein** - Hindari kopi setelah jam 2 siang

🌟 **Ingat**
Perasaan ini tidak akan selamanya. Anda sudah hebat mau memantau kondisi Anda!

⚠️ **Penting**
Jika perasaan ini berlanjut lebih dari 2 minggu, pertimbangkan untuk berbicara dengan psikolog atau konselor profesional."

PROMPT;

        return $this->llmService->generate($question, $systemPrompt);
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

        // Handle personal health advice (mood tracking, health data)
        if ($intent === 'personal_health_advice') {
            return [
                'answer' => $this->generatePersonalHealthAdvice($question, $userRole, $userName),
                'sources' => [],
                'context_count' => 0,
                'intent' => 'personal_health_advice',
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
