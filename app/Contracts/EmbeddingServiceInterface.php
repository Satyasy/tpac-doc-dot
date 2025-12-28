<?php

namespace App\Contracts;

interface EmbeddingServiceInterface
{
    /**
     * Generate embedding vector for given text
     *
     * @param string $text Text to embed
     * @return array<float> Embedding vector
     */
    public function embed(string $text): array;

    /**
     * Generate embeddings for multiple texts in batch
     *
     * @param array<string> $texts Array of texts to embed
     * @return array<array<float>> Array of embedding vectors
     */
    public function embedBatch(array $texts): array;

    /**
     * Get the dimension of the embedding vectors
     *
     * @return int Vector dimension size
     */
    public function getDimension(): int;

    /**
     * Get the model name used for embeddings
     *
     * @return string Model identifier
     */
    public function getModel(): string;
}
