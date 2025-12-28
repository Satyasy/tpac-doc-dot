<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDocumentEmbedding;
use App\Models\MedicalDocument;
use App\Services\Rag\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RagController extends Controller
{
    public function __construct(
        private RagService $ragService
    ) {}

    /**
     * Query the RAG system
     */
    public function query(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:1000',
            'top_k' => 'integer|min:1|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->ragService->query(
                $request->input('question'),
                $request->input('top_k', 5)
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Query failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search for similar documents
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:500',
            'limit' => 'integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $this->ragService->search(
                $request->input('query'),
                $request->input('limit', 10)
            );

            return response()->json([
                'success' => true,
                'data' => array_map(fn ($r) => [
                    'document_id' => $r['document']->id,
                    'title' => $r['document']->title,
                    'type' => $r['document']->type,
                    'chunk_preview' => substr($r['chunk_text'], 0, 200) . '...',
                    'page' => $r['page'],
                    'score' => $r['score'],
                ], $results),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process a document for embedding
     */
    public function processDocument(Request $request, MedicalDocument $document): JsonResponse
    {
        $sync = $request->boolean('sync', false);

        if ($sync) {
            try {
                $this->ragService->processDocument($document);

                return response()->json([
                    'success' => true,
                    'message' => 'Document processed successfully',
                    'data' => [
                        'embedding_status' => $document->fresh()->embedding_status,
                        'embeddings_count' => $document->embeddings()->count(),
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Processing failed: ' . $e->getMessage(),
                ], 500);
            }
        }

        ProcessDocumentEmbedding::dispatch($document);

        return response()->json([
            'success' => true,
            'message' => 'Document queued for processing',
        ]);
    }

    /**
     * Get RAG statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->ragService->getStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage(),
            ], 500);
        }
    }
}
