<?php

namespace App\Services\Rag;

use App\Contracts\DocumentParserInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentParser implements DocumentParserInterface
{
    private array $supportedExtensions = ['pdf', 'txt', 'docx', 'doc', 'md'];

    /**
     * Parse document and extract text content
     */
    public function parse(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!$this->supports($extension)) {
            throw new \InvalidArgumentException("Unsupported file type: {$extension}");
        }

        return match ($extension) {
            'pdf' => $this->parsePdf($filePath),
            'docx', 'doc' => $this->parseDocx($filePath),
            'txt', 'md' => $this->parseText($filePath),
            default => throw new \InvalidArgumentException("Unsupported file type: {$extension}"),
        };
    }

    /**
     * Parse document and extract text with page information
     */
    public function parseWithPages(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!$this->supports($extension)) {
            throw new \InvalidArgumentException("Unsupported file type: {$extension}");
        }

        return match ($extension) {
            'pdf' => $this->parsePdfWithPages($filePath),
            'docx', 'doc' => $this->parseDocxWithPages($filePath),
            'txt', 'md' => [['page' => 1, 'content' => $this->parseText($filePath)]],
            default => throw new \InvalidArgumentException("Unsupported file type: {$extension}"),
        };
    }

    /**
     * Check if the parser supports a given file type
     */
    public function supports(string $extension): bool
    {
        return in_array(strtolower($extension), $this->supportedExtensions);
    }

    /**
     * Get list of supported file extensions
     */
    public function getSupportedExtensions(): array
    {
        return $this->supportedExtensions;
    }

    /**
     * Get metadata from the document
     */
    public function getMetadata(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $metadata = [
            'filename' => basename($filePath),
            'extension' => $extension,
            'size' => filesize($filePath),
            'modified' => filemtime($filePath),
        ];

        if ($extension === 'pdf') {
            $metadata = array_merge($metadata, $this->getPdfMetadata($filePath));
        }

        return $metadata;
    }

    /**
     * Parse PDF file
     */
    private function parsePdf(string $filePath): string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \RuntimeException('smalot/pdfparser package is required. Run: composer require smalot/pdfparser');
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);

            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to parse PDF: {$e->getMessage()}");
        }
    }

    /**
     * Parse PDF with page information
     */
    private function parsePdfWithPages(string $filePath): array
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \RuntimeException('smalot/pdfparser package is required');
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);

            $pages = [];
            $pageNumber = 1;

            foreach ($pdf->getPages() as $page) {
                $content = $page->getText();
                if (!empty(trim($content))) {
                    $pages[] = [
                        'page' => $pageNumber,
                        'content' => $this->cleanText($content),
                    ];
                }
                $pageNumber++;
            }

            return $pages;
        } catch (\Exception $e) {
            Log::error('PDF page parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to parse PDF pages: {$e->getMessage()}");
        }
    }

    /**
     * Get PDF metadata
     */
    private function getPdfMetadata(string $filePath): array
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $details = $pdf->getDetails();

            return [
                'title' => $details['Title'] ?? null,
                'author' => $details['Author'] ?? null,
                'creator' => $details['Creator'] ?? null,
                'pages' => $details['Pages'] ?? count($pdf->getPages()),
                'created' => $details['CreationDate'] ?? null,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Parse DOCX file
     */
    private function parseDocx(string $filePath): string
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            throw new \RuntimeException('phpoffice/phpword package is required. Run: composer require phpoffice/phpword');
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractTextFromElement($element) . "\n";
                }
            }

            return $this->cleanText($text);
        } catch (\Exception $e) {
            Log::error('DOCX parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to parse DOCX: {$e->getMessage()}");
        }
    }

    /**
     * Parse DOCX with pages (approximated by sections)
     */
    private function parseDocxWithPages(string $filePath): array
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            throw new \RuntimeException('phpoffice/phpword package is required');
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $pages = [];
            $pageNumber = 1;

            foreach ($phpWord->getSections() as $section) {
                $text = '';
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractTextFromElement($element) . "\n";
                }

                if (!empty(trim($text))) {
                    $pages[] = [
                        'page' => $pageNumber,
                        'content' => $this->cleanText($text),
                    ];
                }
                $pageNumber++;
            }

            return $pages;
        } catch (\Exception $e) {
            Log::error('DOCX page parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to parse DOCX pages: {$e->getMessage()}");
        }
    }

    /**
     * Extract text from PhpWord element
     */
    private function extractTextFromElement($element): string
    {
        $text = '';

        if (method_exists($element, 'getText')) {
            $text .= $element->getText();
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractTextFromElement($child);
            }
        }

        return $text;
    }

    /**
     * Parse plain text file
     */
    private function parseText(string $filePath): string
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new \RuntimeException("Failed to read file: {$filePath}");
        }

        return $this->cleanText($content);
    }

    /**
     * Clean and normalize text
     */
    private function cleanText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove null bytes
        $text = str_replace("\0", '', $text);

        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Remove excessive line breaks
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Parse from storage path
     */
    public function parseFromStorage(string $path, string $disk = 'local'): string
    {
        $fullPath = Storage::disk($disk)->path($path);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        return $this->parse($fullPath);
    }

    /**
     * Parse from storage with pages
     */
    public function parseFromStorageWithPages(string $path, string $disk = 'local'): array
    {
        $fullPath = Storage::disk($disk)->path($path);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        return $this->parseWithPages($fullPath);
    }
}
