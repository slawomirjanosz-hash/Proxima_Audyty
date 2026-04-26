<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iso50001QuestionnaireQuestion extends Model
{
    protected $fillable = [
        'block_key',
        'question_code',
        'question_text',
        'answer_hint',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static array $blockLabels = [
        'A' => 'BLOK A — Dane rejestrowe firmy',
        'B' => 'BLOK B — Dane energetyczne',
        'C' => 'BLOK C — Lokalizacje i obiekty',
        'D' => 'BLOK D — Istniejące systemy i dokumentacja',
        'E' => 'BLOK E — Zaangażowanie po stronie klienta',
        'F' => 'BLOK F — System energetyczny — szczegóły',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
