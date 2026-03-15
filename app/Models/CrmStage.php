<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmStage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'order',
        'is_active',
        'is_closed',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];
}
