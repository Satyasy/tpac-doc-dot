<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalDocument extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'title',
        'type',
        'source',
        'content',
        'verified',
        'file_path',
        'file_type',
        'embedding_status',
        'embedding_error',
        'embedded_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'embedded_at' => 'datetime',
    ];

    protected $attributes = [
        'embedding_status' => self::STATUS_PENDING,
    ];

    public function embeddings(): HasMany
    {
        return $this->hasMany(MedicalEmbedding::class, 'document_id');
    }

    public function isPending(): bool
    {
        return $this->embedding_status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->embedding_status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->embedding_status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->embedding_status === self::STATUS_FAILED;
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'embedding_status' => self::STATUS_PROCESSING,
            'embedding_error' => null,
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'embedding_status' => self::STATUS_COMPLETED,
            'embedded_at' => now(),
            'embedding_error' => null,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'embedding_status' => self::STATUS_FAILED,
            'embedding_error' => $error,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('embedding_status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('embedding_status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('embedding_status', self::STATUS_FAILED);
    }
}
