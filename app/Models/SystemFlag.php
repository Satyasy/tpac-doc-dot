<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemFlag extends Model
{
   use HasFactory;

   public $timestamps = false;

   protected $fillable = [
      'keyword',
      'severity',
      'action',
   ];
}
