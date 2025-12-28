<?php

namespace App\Contracts;

interface DocumentParserInterface
{
    /**
     * Parse document and extract text content
     *
     * @param string $filePath Path to the document file
     * @return string Extracted text content
     */
    public function parse(string $filePath): string;

    /**
     * Parse document and extract text with page information
     *
     * @param string $filePath Path to the document file
     * @return array<array{page: int, content: string}> Array of pages with content
     */
    public function parseWithPages(string $filePath): array;

    /**
     * Check if the parser supports a given file type
     *
     * @param string $extension File extension (without dot)
     * @return bool Whether the file type is supported
     */
    public function supports(string $extension): bool;

    /**
     * Get list of supported file extensions
     *
     * @return array<string> Supported extensions
     */
    public function getSupportedExtensions(): array;

    /**
     * Get metadata from the document
     *
     * @param string $filePath Path to the document file
     * @return array<string, mixed> Document metadata (author, title, etc.)
     */
    public function getMetadata(string $filePath): array;
}
