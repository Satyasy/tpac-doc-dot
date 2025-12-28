<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorPatient extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'status',
        'request_message',
        'response_message',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the doctor user
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the patient user
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get alerts for this relationship
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(PatientAlert::class);
    }

    /**
     * Check if relationship is active (accepted)
     */
    public function isActive(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if relationship is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Accept the request
     */
    public function accept(?string $message = null): void
    {
        $this->update([
            'status' => 'accepted',
            'response_message' => $message,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the request
     */
    public function reject(?string $message = null): void
    {
        $this->update([
            'status' => 'rejected',
            'response_message' => $message,
            'rejected_at' => now(),
        ]);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted relationships
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
