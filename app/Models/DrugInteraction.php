<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrugInteraction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'drug_id',
        'interacting_drug',
        'risk_level',
        'description',
    ];

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }
}
