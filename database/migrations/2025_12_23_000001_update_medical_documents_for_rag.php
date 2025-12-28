<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('source');
            $table->string('file_type')->nullable()->after('file_path');
            $table->enum('embedding_status', ['pending', 'processing', 'completed', 'failed'])
                ->default('pending')->after('verified');
            $table->text('embedding_error')->nullable()->after('embedding_status');
            $table->timestamp('embedded_at')->nullable()->after('embedding_error');
        });

        Schema::table('medical_embeddings', function (Blueprint $table) {
            $table->text('chunk_text')->after('chunk_index');
            $table->integer('page_number')->nullable()->after('chunk_text');
            $table->integer('token_count')->nullable()->after('page_number');
            $table->json('metadata')->nullable()->after('token_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_type', 'embedding_status', 'embedding_error', 'embedded_at']);
        });

        Schema::table('medical_embeddings', function (Blueprint $table) {
            $table->dropColumn(['chunk_text', 'page_number', 'token_count', 'metadata']);
        });
    }
};
