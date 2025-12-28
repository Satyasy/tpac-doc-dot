<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Rag\DocumentParser;
use App\Models\MedicalDocument;
use Illuminate\Support\Facades\Storage;

class TestPdfParser extends Command
{
    protected $signature = 'test:pdf {id? : Document ID to test}';
    protected $description = 'Test PDF parsing';

    public function handle()
    {
        $id = $this->argument('id') ?? 1;
        $doc = MedicalDocument::find($id);

        if (!$doc) {
            $this->error("Document ID {$id} not found");
            return 1;
        }

        $this->info("Document: {$doc->title}");
        $this->info("File path: {$doc->file_path}");

        // Check file exists
        $fullPath = Storage::disk('local')->path($doc->file_path);
        $this->info("Full path: {$fullPath}");
        $this->info("File exists: " . (file_exists($fullPath) ? 'Yes' : 'No'));

        if (!file_exists($fullPath)) {
            $this->error("File not found!");
            return 1;
        }

        $this->info("File size: " . filesize($fullPath) . " bytes");

        // Try parsing
        $this->newLine();
        $this->info("Parsing PDF...");

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            
            $text = $pdf->getText();
            $this->info("Extracted text length: " . strlen($text) . " characters");
            $this->newLine();
            
            $this->info("First 2000 characters:");
            $this->line(substr($text, 0, 2000));
            
            // Get page count
            $pages = $pdf->getPages();
            $this->newLine();
            $this->info("Total pages: " . count($pages));

        } catch (\Exception $e) {
            $this->error("Parse error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
