<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalEmbedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'vector_id',
        'chunk_index',
        'chunk_text',
        'page_number',
        'token_count',
        'metadata',
    ];

    protected $casts = [
        'chunk_index' => 'integer',
        'page_number' => 'integer',
        'token_count' => 'integer',
        'metadata' => 'array',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class, 'document_id');
    }

    public function getContentPreview(int $length = 100): string
    {
        return strlen($this->chunk_text) > $length 
            ? substr($this->chunk_text, 0, $length) . '...' 
            : $this->chunk_text;
    }
}
