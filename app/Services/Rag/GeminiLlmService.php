<?php

namespace App\Services\Rag;

use App\Contracts\LlmServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiLlmService implements LlmServiceInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private int $maxContextLength = 30720; // ~30k tokens for Gemini 1.5 Flash

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.llm_model', 'gemini-1.5-flash');
    }

    /**
     * Generate a response from the LLM
     */
    public function generate(string $prompt, ?string $systemPrompt = null, array $history = []): string
    {
        $contents = [];

        // Add conversation history
        foreach ($history as $message) {
            $contents[] = [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $message['content']]],
            ];
        }

        // Add current prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]],
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        // Add system instruction if provided
        if ($systemPrompt) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]],
            ];
        }

        // Add safety settings
        $payload['safetySettings'] = [
            ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
        ];

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(60)
                ->retry(3, 1000)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent", $payload);

            if ($response->failed()) {
                Log::error('Gemini LLM API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('Gemini API request failed: ' . $response->body());
            }

            $result = $response->json();

            return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        } catch (\Exception $e) {
            Log::error('Gemini LLM exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get system prompt based on user role
     */
    private function getSystemPromptForRole(string $role, string $contextText): string
    {
        if ($role === 'doctor') {
            return <<<PROMPT
Kamu adalah asisten AI medis bernama DocDot untuk DOKTER dan TENAGA KESEHATAN PROFESIONAL.

PERAN KHUSUS DOKTER:
- Kamu adalah partner diskusi klinis untuk dokter
- Berikan informasi yang lebih teknis dan mendalam
- Gunakan terminologi medis yang sesuai
- Sertakan referensi atau guideline jika relevan

KEMAMPUAN UNTUK DOKTER:
1. **Analisis Penyakit Mendalam** - Bantu analisis differential diagnosis
2. **Farmakologi & Interaksi Obat** - Informasi detail tentang mekanisme obat
3. **Guideline Klinis** - Ringkasan pedoman klinis terkini
4. **Riset & Evidence-Based** - Informasi berbasis bukti ilmiah

FORMAT JAWABAN UNTUK DOKTER:
- Gunakan **heading** dan **subheading** untuk struktur
- Sertakan **tabel** jika perlu untuk perbandingan
- Gunakan terminologi medis dengan penjelasan
- Struktur: Background → Analysis → Recommendation → References (jika ada)
- Boleh menyertakan kemungkinan/probabilitas jika relevan

BATASAN:
- Informasi bersifat edukatif dan pendukung keputusan klinis
- Keputusan akhir tetap di tangan dokter yang merawat
- Selalu pertimbangkan konteks pasien spesifik

KONTEKS DOKUMEN:
{$contextText}
PROMPT;
        }

        // Default: Patient role
        return <<<PROMPT
Kamu adalah asisten kesehatan AI bernama DocDot yang membantu PASIEN memahami informasi kesehatan.

PERAN DAN GAYA KOMUNIKASI:
- Kamu adalah teman yang peduli dan berempati
- Gunakan bahasa Indonesia yang SEDERHANA dan MUDAH DIPAHAMI
- Hindari jargon medis yang membingungkan
- Bersikap RAMAH, MENENANGKAN, dan SUPORTIF
- Jangan membuat pasien takut atau cemas berlebihan

ATURAN PENTING:
1. JANGAN memberikan diagnosis pasti
2. JANGAN memberikan resep obat spesifik
3. Jawab berdasarkan konteks dokumen yang tersedia
4. Jika informasi tidak ada, tetap interaktif dan arahkan ke langkah selanjutnya
5. SELALU sarankan konsultasi dengan dokter untuk keluhan serius

FORMAT JAWABAN UNTUK PASIEN:
- Gunakan emoji secukupnya untuk kesan ramah 😊
- Gunakan **bold** untuk poin penting
- Gunakan bullet points yang mudah dibaca
- Berikan langkah-langkah praktis yang bisa dilakukan
- Akhiri dengan pertanyaan lanjutan atau saran aksi

CONTOH RESPONS YANG BAIK:
"Berdasarkan yang Anda ceritakan, ini bisa jadi tanda... Beberapa hal yang bisa Anda lakukan:
1. ...
2. ...
Jika gejala berlanjut lebih dari X hari, sebaiknya konsultasikan ke dokter ya! 🏥"

KONTEKS DOKUMEN:
{$contextText}
PROMPT;
    }

    /**
     * Generate a response with context from retrieved documents
     */
    public function generateWithContext(string $query, array $contexts, ?string $systemPrompt = null, string $userRole = 'patient'): string
    {
        $contextText = implode("\n\n---\n\n", $contexts);

        $finalSystemPrompt = $systemPrompt ?? $this->getSystemPromptForRole($userRole, $contextText);

        return $this->generate($query, $finalSystemPrompt);
    }

    /**
     * Generate fallback response when no RAG documents found
     */
    public function generateFallbackResponse(string $query, string $userRole = 'patient'): string
    {
        if ($userRole === 'doctor') {
            $systemPrompt = <<<PROMPT
Kamu adalah asisten AI medis DocDot untuk dokter. Tidak ditemukan dokumen spesifik dalam database untuk pertanyaan ini.

INSTRUKSI:
- Berikan informasi umum berbasis pengetahuan medis
- Gunakan format yang terstruktur dan profesional
- Sebutkan bahwa ini adalah pengetahuan umum, bukan dari dokumen spesifik
- Sarankan sumber referensi yang bisa dikonsultasikan
- Tetap gunakan terminologi medis yang sesuai

FORMAT:
1. Jawab pertanyaan dengan informasi medis umum
2. Berikan disclaimer bahwa ini bukan dari dokumen terverifikasi
3. Sarankan referensi atau langkah selanjutnya
PROMPT;
        } else {
            $systemPrompt = <<<PROMPT
Kamu adalah asisten kesehatan DocDot yang ramah. Tidak ditemukan dokumen spesifik untuk pertanyaan ini, tapi kamu tetap akan membantu.

INSTRUKSI PENTING:
- Tetap INTERAKTIF dan MEMBANTU
- Gunakan bahasa yang sederhana dan ramah
- Berikan informasi umum yang aman dan bermanfaat
- JANGAN pernah mendiagnosis atau memberikan resep obat
- Selalu arahkan ke konsultasi profesional untuk hal serius

FORMAT RESPONS:
1. Akui bahwa kamu tidak menemukan data spesifik di sistem
2. Berikan informasi umum yang bermanfaat
3. Tawarkan bantuan lanjutan atau arahkan ke fitur lain
4. Gunakan emoji untuk kesan ramah 😊

CONTOH PEMBUKA:
"Untuk pertanyaan ini, saya tidak menemukan data spesifik di sistem kami. Tapi saya bisa bantu jelaskan secara umum..."
PROMPT;
        }

        return $this->generate($query, $systemPrompt);
    }

    /**
     * Stream a response (for real-time output)
     */
    public function stream(string $prompt, ?string $systemPrompt = null, callable $onChunk = null): void
    {
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        if ($systemPrompt) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]],
            ];
        }

        try {
            $response = Http::withOptions(['verify' => false, 'stream' => true])
                ->timeout(120)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/models/{$this->model}:streamGenerateContent", $payload);

            $body = $response->getBody();

            while (!$body->eof()) {
                $line = $body->read(1024);

                if ($onChunk && !empty($line)) {
                    // Parse SSE data
                    if (preg_match('/\"text\":\s*\"([^\"]+)\"/', $line, $matches)) {
                        $onChunk($matches[1]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gemini stream exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Count tokens in text (approximate)
     */
    public function countTokens(string $text): int
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(30)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/models/{$this->model}:countTokens", [
                    'contents' => [
                        [
                            'parts' => [['text' => $text]]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                return $response->json()['totalTokens'] ?? 0;
            }
        } catch (\Exception $e) {
            Log::warning('Token count failed, using estimate', [
                'message' => $e->getMessage(),
            ]);
        }

        // Fallback: rough estimate (1 token ≈ 4 chars for English, ~2.5 for Indonesian)
        return (int) ceil(strlen($text) / 3);
    }

    /**
     * Get the model name
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get maximum context length
     */
    public function getMaxContextLength(): int
    {
        return $this->maxContextLength;
    }

    /**
     * Generate structured JSON response
     */
    public function generateJson(string $prompt, array $schema, ?string $systemPrompt = null): array
    {
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]],
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
                'responseSchema' => $schema,
            ],
        ];

        if ($systemPrompt) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]],
            ];
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(60)
                ->retry(3, 1000)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent", $payload);

            if ($response->failed()) {
                throw new \RuntimeException('Gemini API request failed: ' . $response->body());
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            return json_decode($text, true) ?? [];
        } catch (\Exception $e) {
            Log::error('Gemini JSON generation exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
