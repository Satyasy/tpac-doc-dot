<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalHealthLog extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'weight_kg',
      'blood_pressure',
      'activity_minutes',
      'logged_at',
   ];

   protected $casts = [
      'weight_kg' => 'float',
      'activity_minutes' => 'integer',
      'logged_at' => 'date',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }
}
