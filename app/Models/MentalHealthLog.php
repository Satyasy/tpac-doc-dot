<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalHealthLog extends Model
{
   use HasFactory;

   protected $fillable = [
      'user_id',
      'mood',
      'stress_level',
      'sleep_hours',
      'note',
      'logged_at',
   ];

   protected $casts = [
      'mood' => 'integer',
      'stress_level' => 'integer',
      'sleep_hours' => 'float',
      'logged_at' => 'date',
   ];

   public function user(): BelongsTo
   {
      return $this->belongsTo(User::class);
   }
}
