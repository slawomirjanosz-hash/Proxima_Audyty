<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iso50001Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'steps',
    ];

    protected $casts = [
        'steps' => 'array',
    ];
}
