<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAlert extends Model
{
   protected $fillable = [
      'doctor_patient_id',
      'chat_message_id',
      'alert_type',
      'triggered_text',
      'matched_keywords',
      'is_read',
      'read_at',
   ];

   protected $casts = [
      'matched_keywords' => 'array',
      'is_read' => 'boolean',
      'read_at' => 'datetime',
   ];

   // Forbidden words that trigger alerts
   public const FORBIDDEN_WORDS = [
      // Bahasa Indonesia
      'mau mati',
      'ingin mati',
      'bunuh diri',
      'mengakhiri hidup',
      'tidak ingin hidup',
      'gak mau hidup',
      'ga mau hidup',
      'ngerasa sekarat',
      'merasa sekarat',
      'putus asa',
      'menyerah',
      'sudah tidak kuat',
      'tidak sanggup lagi',
      'lebih baik mati',
      'mending mati',
      'capek hidup',
      'lelah hidup',
      'benci hidup',
      'sakiti diri',
      'melukai diri',
      'potong nadi',
      'gantung diri',
      // English
      'want to die',
      'kill myself',
      'suicide',
      'end my life',
      'self harm',
      'hurt myself',
      'give up on life',
   ];

   /**
    * Get the doctor-patient relationship
    */
   public function doctorPatient(): BelongsTo
   {
      return $this->belongsTo(DoctorPatient::class);
   }

   /**
    * Get the chat message that triggered the alert
    */
   public function chatMessage(): BelongsTo
   {
      return $this->belongsTo(ChatMessage::class);
   }

   /**
    * Mark alert as read
    */
   public function markAsRead(): void
   {
      $this->update([
         'is_read' => true,
         'read_at' => now(),
      ]);
   }

   /**
    * Check text for forbidden words and return matches
    */
   public static function checkForForbiddenWords(string $text): array
   {
      $text = strtolower($text);
      $matches = [];

      foreach (self::FORBIDDEN_WORDS as $word) {
         if (str_contains($text, strtolower($word))) {
            $matches[] = $word;
         }
      }

      return $matches;
   }

   /**
    * Create alert if forbidden words found
    */
   public static function createAlertIfNeeded(
      int $doctorPatientId,
      string $text,
      ?int $chatMessageId = null
   ): ?self {
      $matches = self::checkForForbiddenWords($text);

      if (empty($matches)) {
         return null;
      }

      return self::create([
         'doctor_patient_id' => $doctorPatientId,
         'chat_message_id' => $chatMessageId,
         'alert_type' => 'forbidden_word',
         'triggered_text' => $text,
         'matched_keywords' => $matches,
      ]);
   }

   /**
    * Scope for unread alerts
    */
   public function scopeUnread($query)
   {
      return $query->where('is_read', false);
   }
}
