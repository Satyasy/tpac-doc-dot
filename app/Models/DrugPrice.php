<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrugPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'drug_id',
        'pharmacy_name',
        'price_min',
        'price_max',
    ];

    protected $casts = [
        'price_min' => 'integer',
        'price_max' => 'integer',
    ];

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }
}
