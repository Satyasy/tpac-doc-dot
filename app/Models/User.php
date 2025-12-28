<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'google_id',
        'otp_code',
        'otp_expires_at',
        'otp_resend_count',
        'otp_last_resend_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'otp_last_resend_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Determine if the user can access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        // Super Admin panel - only for super_admin role
        if ($panelId === 'admin') {
            return $this->hasRole('super_admin');
        }

        // Doctor panel - only for doctor role
        if ($panelId === 'doctor') {
            return $this->hasRole('doctor');
        }

        return false;
    }

    /**
     * Check if user is a doctor
     */
    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is a regular patient (no special role)
     */
    public function isPatient(): bool
    {
        return !$this->hasRole('super_admin') && !$this->hasRole('doctor');
    }

    /**
     * Generate OTP code
     */
    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        return $otp;
    }

    /**
     * Check if can resend OTP
     */
    public function canResendOtp(): array
    {
        $maxResendPerDay = 5;
        $cooldowns = [30, 60, 120, 300, 300]; // seconds: 30s, 1m, 2m, 5m, 5m

        // Reset count if last resend was yesterday
        if ($this->otp_last_resend_at && !$this->otp_last_resend_at->isToday()) {
            $this->update(['otp_resend_count' => 0]);
        }

        $resendCount = $this->otp_resend_count ?? 0;

        // Check daily limit
        if ($resendCount >= $maxResendPerDay) {
            return [
                'can_resend' => false,
                'reason' => 'Anda telah mencapai batas maksimal resend OTP hari ini (5x).',
                'wait_seconds' => 0,
                'remaining_today' => 0,
            ];
        }

        // Check cooldown
        if ($this->otp_last_resend_at) {
            $cooldownIndex = min($resendCount, count($cooldowns) - 1);
            $cooldownSeconds = $cooldowns[$cooldownIndex];
            $nextResendAt = $this->otp_last_resend_at->addSeconds($cooldownSeconds);

            if (now()->lt($nextResendAt)) {
                return [
                    'can_resend' => false,
                    'reason' => 'Tunggu sebentar sebelum mengirim ulang.',
                    'wait_seconds' => now()->diffInSeconds($nextResendAt),
                    'remaining_today' => $maxResendPerDay - $resendCount,
                ];
            }
        }

        return [
            'can_resend' => true,
            'reason' => null,
            'wait_seconds' => 0,
            'remaining_today' => $maxResendPerDay - $resendCount,
        ];
    }

    /**
     * Resend OTP with tracking
     */
    public function resendOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_resend_count' => ($this->otp_resend_count ?? 0) + 1,
            'otp_last_resend_at' => now(),
        ]);

        return $otp;
    }

    /**
     * Get next cooldown seconds
     */
    public function getNextCooldown(): int
    {
        $cooldowns = [30, 60, 120, 300, 300];
        $resendCount = ($this->otp_resend_count ?? 0) + 1;
        $cooldownIndex = min($resendCount, count($cooldowns) - 1);
        return $cooldowns[$cooldownIndex];
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $otp): bool
    {
        if ($this->otp_code === $otp && $this->otp_expires_at && $this->otp_expires_at->isFuture()) {
            $this->update([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_resend_count' => 0,
                'otp_last_resend_at' => null,
                'email_verified_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function aiAuditLogs(): HasMany
    {
        return $this->hasMany(AiAuditLog::class);
    }

    public function mentalHealthLogs(): HasMany
    {
        return $this->hasMany(MentalHealthLog::class);
    }

    public function physicalHealthLogs(): HasMany
    {
        return $this->hasMany(PhysicalHealthLog::class);
    }

    public function healthInsights(): HasMany
    {
        return $this->hasMany(HealthInsight::class);
    }

    /**
     * Get patients for this doctor (accepted relationships)
     */
    public function patients(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'doctor_id')->accepted();
    }

    /**
     * Get all patient requests (pending)
     */
    public function patientRequests(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'doctor_id')->pending();
    }

    /**
     * Get all doctor-patient relationships as doctor
     */
    public function doctorPatientRelations(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'doctor_id');
    }

    /**
     * Get doctors for this patient (accepted relationships)
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'patient_id')->accepted();
    }

    /**
     * Get pending doctor requests sent by this patient
     */
    public function pendingDoctorRequests(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'patient_id')->pending();
    }

    /**
     * Get all doctor-patient relationships as patient
     */
    public function patientDoctorRelations(): HasMany
    {
        return $this->hasMany(DoctorPatient::class, 'patient_id');
    }

    /**
     * Check if user has a specific doctor
     */
    public function hasDoctor(int $doctorId): bool
    {
        return $this->doctors()->where('doctor_id', $doctorId)->exists();
    }

    /**
     * Check if user has pending request to a doctor
     */
    public function hasPendingRequestTo(int $doctorId): bool
    {
        return $this->pendingDoctorRequests()->where('doctor_id', $doctorId)->exists();
    }

    /**
     * Get active doctor for this patient (first accepted)
     */
    public function getActiveDoctor(): ?DoctorPatient
    {
        return $this->doctors()->with('doctor')->first();
    }
}
