<?php

namespace App\Contracts;

interface LlmServiceInterface
{
    /**
     * Generate a response from the LLM
     *
     * @param string $prompt The user prompt/question
     * @param string|null $systemPrompt Optional system prompt for context
     * @param array<array{role: string, content: string}> $history Conversation history
     * @return string Generated response text
     */
    public function generate(string $prompt, ?string $systemPrompt = null, array $history = []): string;

    /**
     * Generate a response with context from retrieved documents
     *
     * @param string $query User's query
     * @param array<string> $contexts Retrieved context chunks
     * @param string|null $systemPrompt Optional system prompt
     * @return string Generated response
     */
    public function generateWithContext(string $query, array $contexts, ?string $systemPrompt = null): string;

    /**
     * Stream a response (for real-time output)
     *
     * @param string $prompt The user prompt
     * @param string|null $systemPrompt Optional system prompt
     * @param callable $onChunk Callback for each chunk: fn(string $chunk): void
     * @return void
     */
    public function stream(string $prompt, ?string $systemPrompt = null, callable $onChunk = null): void;

    /**
     * Count tokens in text (approximate)
     *
     * @param string $text Text to count tokens for
     * @return int Approximate token count
     */
    public function countTokens(string $text): int;

    /**
     * Get the model name
     *
     * @return string Model identifier
     */
    public function getModel(): string;

    /**
     * Get maximum context length
     *
     * @return int Maximum tokens allowed
     */
    public function getMaxContextLength(): int;
}
