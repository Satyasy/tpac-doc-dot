<?php

namespace App\Services\Rag;

use App\Contracts\EmbeddingServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class GeminiEmbeddingService implements EmbeddingServiceInterface
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private int $dimension = 768;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.embedding_model', 'gemini-embedding-001');
    }

    /**
     * Generate embedding vector for given text
     */
    public function embed(string $text): array
    {
        $response = $this->makeRequest($text, 'RETRIEVAL_DOCUMENT');

        return $response['embedding']['values'] ?? [];
    }

    /**
     * Generate embeddings for multiple texts in batch
     */
    public function embedBatch(array $texts): array
    {
        $embeddings = [];

        // Process in batches with rate limiting
        foreach (array_chunk($texts, 5) as $chunk) {
            foreach ($chunk as $text) {
                try {
                    $embeddings[] = $this->embed($text);
                    // Delay to avoid rate limiting (Gemini free tier: 1500 RPM)
                    usleep(50000); // 50ms
                } catch (\Exception $e) {
                    Log::error('Gemini embedding failed for text chunk', [
                        'error' => $e->getMessage(),
                        'text_preview' => substr($text, 0, 100),
                    ]);
                    // Return empty array for failed embeddings
                    $embeddings[] = [];
                }
            }
        }

        return $embeddings;
    }

    /**
     * Get the dimension of the embedding vectors
     */
    public function getDimension(): int
    {
        return $this->dimension;
    }

    /**
     * Get the model name used for embeddings
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Make API request to Gemini Embedding
     */
    private function makeRequest(string $text, string $taskType = 'RETRIEVAL_DOCUMENT'): array
    {
        $url = "{$this->baseUrl}/models/{$this->model}:embedContent";

        // Truncate text if too long (max ~10k tokens)
        $text = mb_substr($text, 0, 30000);

        try {
            $response = Http::timeout(60)
                ->retry(3, 2000)
                ->withOptions(['verify' => false]) // Disable SSL verify for development
                ->withQueryParameters(['key' => $this->apiKey])
                ->post($url, [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ],
                    'taskType' => $taskType,
                    'outputDimensionality' => $this->dimension,
                ]);

            if ($response->failed()) {
                Log::error('Gemini Embedding API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'model' => $this->model,
                ]);
                throw new \RuntimeException('Gemini API request failed: ' . $response->body());
            }

            return $response->json();
        } catch (RequestException $e) {
            Log::error('Gemini request exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate embedding optimized for queries (retrieval)
     */
    public function embedQuery(string $query): array
    {
        $response = $this->makeRequest($query, 'RETRIEVAL_QUERY');

        return $response['embedding']['values'] ?? [];
    }

    /**
     * Test connection to Gemini API
     */
    public function testConnection(): array
    {
        try {
            $testText = "Test embedding connection";
            $embedding = $this->embed($testText);

            return [
                'success' => !empty($embedding),
                'model' => $this->model,
                'dimension' => count($embedding),
                'expected_dimension' => $this->dimension,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
