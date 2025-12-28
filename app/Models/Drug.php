<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'category',
        'description',
        'dosage_info',
        'side_effects',
        'warnings',
        'pregnancy_safe',
    ];

    protected $casts = [
        'pregnancy_safe' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(DrugPrice::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(DrugInteraction::class);
    }
}
