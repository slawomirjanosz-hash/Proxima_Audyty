<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'formulas',
        'variables',
    ];

    protected $casts = [
        'formulas' => 'array',
        'variables' => 'array',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(AuditTypeSection::class)->orderBy('position');
    }
}
