<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Iso50001Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_id',
        'created_by_user_id',
        'reviewer_id',
        'status',
        'current_step',
        'due_date',
        'answers',
        'questionnaire_answers',
        'questionnaire_completed',
        'reviewer_notes',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'questionnaire_answers' => 'array',
        'questionnaire_completed' => 'boolean',
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'draft' => 'Szkic',
            'in_progress' => 'W trakcie',
            'submitted' => 'Przeslany do audytora',
            'in_review' => 'W trakcie weryfikacji',
            'changes_required' => 'Wymaga poprawek',
            'approved' => 'Zatwierdzony',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
