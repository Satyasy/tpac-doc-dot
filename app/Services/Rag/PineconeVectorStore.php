<?php

namespace App\Services\Rag;

use App\Contracts\VectorStoreInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PineconeVectorStore implements VectorStoreInterface
{
    private string $apiKey;
    private string $host;
    private string $indexName;

    public function __construct()
    {
        $this->apiKey = config('services.pinecone.api_key');
        $this->host = config('services.pinecone.host');
        $this->indexName = config('services.pinecone.index', 'kusuma');
    }

    /**
     * Get HTTP client with common options
     */
    private function http()
    {
        return Http::withOptions(['verify' => false]) // Disable SSL verify for development
            ->withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * Insert a single vector with metadata
     */
    public function upsert(string $id, array $vector, array $metadata = [], ?string $namespace = null): bool
    {
        return $this->upsertBatch([
            [
                'id' => $id,
                'vector' => $vector,
                'metadata' => $metadata,
            ]
        ], $namespace);
    }

    /**
     * Insert multiple vectors in batch
     */
    public function upsertBatch(array $vectors, ?string $namespace = null): bool
    {
        $formattedVectors = array_map(function ($v) {
            return [
                'id' => $v['id'],
                'values' => $v['vector'],
                'metadata' => $v['metadata'] ?? [],
            ];
        }, $vectors);

        $payload = ['vectors' => $formattedVectors];

        if ($namespace) {
            $payload['namespace'] = $namespace;
        }

        try {
            $response = $this->http()
                ->timeout(60)
                ->retry(3, 1000)
                ->post("{$this->host}/vectors/upsert", $payload);

            if ($response->failed()) {
                Log::error('Pinecone upsert failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Pinecone upsert exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Query for similar vectors
     */
    public function query(array $vector, int $topK = 5, array $filter = [], ?string $namespace = null): array
    {
        $payload = [
            'vector' => $vector,
            'topK' => $topK,
            'includeMetadata' => true,
        ];

        if (!empty($filter)) {
            $payload['filter'] = $filter;
        }

        if ($namespace) {
            $payload['namespace'] = $namespace;
        }

        try {
            $response = $this->http()
                ->timeout(30)
                ->retry(3, 1000)
                ->post("{$this->host}/query", $payload);

            if ($response->failed()) {
                Log::error('Pinecone query failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $result = $response->json();
            
            return array_map(function ($match) {
                return [
                    'id' => $match['id'],
                    'score' => $match['score'] ?? 0,
                    'metadata' => $match['metadata'] ?? [],
                ];
            }, $result['matches'] ?? []);
        } catch (\Exception $e) {
            Log::error('Pinecone query exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Delete vectors by IDs
     */
    public function delete(array $ids, ?string $namespace = null): bool
    {
        $payload = ['ids' => $ids];

        if ($namespace) {
            $payload['namespace'] = $namespace;
        }

        try {
            $response = $this->http()
                ->timeout(30)
                ->retry(3, 1000)
                ->post("{$this->host}/vectors/delete", $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Pinecone delete exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Delete all vectors in a namespace
     */
    public function deleteNamespace(string $namespace): bool
    {
        try {
            $response = $this->http()
                ->timeout(30)
                ->retry(3, 1000)
                ->post("{$this->host}/vectors/delete", [
                    'deleteAll' => true,
                    'namespace' => $namespace,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Pinecone deleteNamespace exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get vector by ID
     */
    public function fetch(string $id, ?string $namespace = null): ?array
    {
        $params = ['ids' => $id];

        if ($namespace) {
            $params['namespace'] = $namespace;
        }

        try {
            $response = $this->http()
                ->timeout(30)
                ->retry(3, 1000)
                ->get("{$this->host}/vectors/fetch", $params);

            if ($response->failed()) {
                return null;
            }

            $result = $response->json();
            return $result['vectors'][$id] ?? null;
        } catch (\Exception $e) {
            Log::error('Pinecone fetch exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get index statistics
     */
    public function stats(): array
    {
        try {
            $response = $this->http()
                ->timeout(30)
                ->get("{$this->host}/describe_index_stats");

            if ($response->failed()) {
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Pinecone stats exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Delete vectors by metadata filter
     */
    public function deleteByFilter(array $filter, ?string $namespace = null): bool
    {
        $payload = ['filter' => $filter];

        if ($namespace) {
            $payload['namespace'] = $namespace;
        }

        try {
            $response = $this->http()
                ->timeout(30)
                ->retry(3, 1000)
                ->post("{$this->host}/vectors/delete", $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Pinecone deleteByFilter exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
