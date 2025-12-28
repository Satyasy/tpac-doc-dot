<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'users_profiles';

    protected $fillable = [
        'user_id',
        'gender',
        'photo_profile',
        'birth_date',
        'height',
        'weight',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'height' => 'integer',
        'weight' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate BMI (Body Mass Index)
     */
    public function getBmiAttribute(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }

    /**
     * Get BMI category
     */
    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;

        if (!$bmi)
            return null;

        if ($bmi < 18.5)
            return 'Underweight';
        if ($bmi < 25)
            return 'Normal';
        if ($bmi < 30)
            return 'Overweight';
        return 'Obese';
    }

    /**
     * Calculate age from birth date
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
    }
}
