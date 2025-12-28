<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'source',
        'verified',
        'published_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'published_at' => 'datetime',
    ];
}
