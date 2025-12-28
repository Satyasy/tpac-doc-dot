<?php

namespace App\Contracts;

interface VectorStoreInterface
{
    /**
     * Insert a single vector with metadata
     *
     * @param string $id Unique identifier for the vector
     * @param array<float> $vector Embedding vector
     * @param array<string, mixed> $metadata Associated metadata
     * @param string|null $namespace Optional namespace for organization
     * @return bool Success status
     */
    public function upsert(string $id, array $vector, array $metadata = [], ?string $namespace = null): bool;

    /**
     * Insert multiple vectors in batch
     *
     * @param array<array{id: string, vector: array<float>, metadata?: array<string, mixed>}> $vectors
     * @param string|null $namespace Optional namespace
     * @return bool Success status
     */
    public function upsertBatch(array $vectors, ?string $namespace = null): bool;

    /**
     * Query for similar vectors
     *
     * @param array<float> $vector Query vector
     * @param int $topK Number of results to return
     * @param array<string, mixed> $filter Optional metadata filter
     * @param string|null $namespace Optional namespace to search in
     * @return array<array{id: string, score: float, metadata: array<string, mixed>}>
     */
    public function query(array $vector, int $topK = 5, array $filter = [], ?string $namespace = null): array;

    /**
     * Delete vectors by IDs
     *
     * @param array<string> $ids Vector IDs to delete
     * @param string|null $namespace Optional namespace
     * @return bool Success status
     */
    public function delete(array $ids, ?string $namespace = null): bool;

    /**
     * Delete all vectors in a namespace
     *
     * @param string $namespace Namespace to clear
     * @return bool Success status
     */
    public function deleteNamespace(string $namespace): bool;

    /**
     * Get vector by ID
     *
     * @param string $id Vector ID
     * @param string|null $namespace Optional namespace
     * @return array|null Vector data or null if not found
     */
    public function fetch(string $id, ?string $namespace = null): ?array;

    /**
     * Get index statistics
     *
     * @return array<string, mixed> Index stats including vector count
     */
    public function stats(): array;
}
