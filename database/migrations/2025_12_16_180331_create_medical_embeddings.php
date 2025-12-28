<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('medical_documents')->cascadeOnDelete();
            $table->string('vector_id');
            $table->integer('chunk_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_embeddings');
    }
};
