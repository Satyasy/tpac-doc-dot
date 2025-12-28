<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAuditLog extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'prompt',
      'retrieved_source',
      'model',
      'blocked',
      'reason',
   ];

   protected $casts = [
      'retrieved_source' => 'array',
      'blocked' => 'boolean',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }
}
