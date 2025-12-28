<?php

use App\Http\Controllers\Api\RagController;
use App\Http\Controllers\HealthDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Protected API routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Mental health log API
    Route::post('/mental-health/log', [HealthDashboardController::class, 'storeMentalApi'])
        ->name('api.mental-health.log');
    
    // Physical health log API
    Route::post('/physical-health/log', [HealthDashboardController::class, 'storePhysicalApi'])
        ->name('api.physical-health.log');
});

Route::prefix('v1')->group(function () {
    // RAG endpoints
    Route::prefix('rag')->group(function () {
        Route::post('/query', [RagController::class, 'query'])->name('api.rag.query');
        Route::post('/search', [RagController::class, 'search'])->name('api.rag.search');
        Route::get('/stats', [RagController::class, 'stats'])->name('api.rag.stats');
        Route::post('/documents/{document}/process', [RagController::class, 'processDocument'])
            ->name('api.rag.process');
    });
});
