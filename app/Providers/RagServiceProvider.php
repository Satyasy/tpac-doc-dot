<?php

namespace App\Providers;

use App\Contracts\DocumentParserInterface;
use App\Contracts\EmbeddingServiceInterface;
use App\Contracts\LlmServiceInterface;
use App\Contracts\VectorStoreInterface;
use App\Services\Rag\DocumentParser;
use App\Services\Rag\GeminiEmbeddingService;
use App\Services\Rag\GeminiLlmService;
use App\Services\Rag\PineconeVectorStore;
use App\Services\Rag\RagService;
use Illuminate\Support\ServiceProvider;

class RagServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register contracts to implementations
        $this->app->singleton(EmbeddingServiceInterface::class, GeminiEmbeddingService::class);
        $this->app->singleton(VectorStoreInterface::class, PineconeVectorStore::class);
        $this->app->singleton(LlmServiceInterface::class, GeminiLlmService::class);
        $this->app->singleton(DocumentParserInterface::class, DocumentParser::class);

        // Register RagService as singleton
        $this->app->singleton(RagService::class, function ($app) {
            return new RagService(
                $app->make(EmbeddingServiceInterface::class),
                $app->make(VectorStoreInterface::class),
                $app->make(LlmServiceInterface::class),
                $app->make(DocumentParserInterface::class)
            );
        });

        // Alias for easier access
        $this->app->alias(RagService::class, 'rag');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            // Add any console commands here if needed
        }
    }
}
