<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyAudit extends Model
{
    use HasFactory;

    // All valid statuses
    public const STATUSES = [
        'wysłany'              => 'Wysłany',
        'rozpoczęty'           => 'Rozpoczęty',
        'do_analizy'           => 'Do analizy',
        'zwrócony_do_poprawy'  => 'Zwrócony do poprawy',
        'zaakceptowany'        => 'Zaakceptowany',
        'zakończony'           => 'Zakończony',
        'zafakturowany'        => 'Zafakturowany',
        'zapłacony'            => 'Zapłacony',
        // Legacy (kept for backward compatibility)
        'new'                  => 'Nowy',
        'in_progress'          => 'W toku',
        'completed'            => 'Zakończony',
    ];

    // Statuses considered "active/in-progress" for the index tab
    public const ACTIVE_STATUSES = [
        'new', 'in_progress', 'wysłany', 'rozpoczęty', 'do_analizy', 'zwrócony_do_poprawy', 'zaakceptowany',
    ];

    // Statuses considered "done/finished" for the completed tab
    public const DONE_STATUSES = [
        'completed', 'zakończony', 'zafakturowany', 'zapłacony',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    protected $fillable = [
        'title',
        'audit_type',
        'audit_type_id',
        'agent_type',
        'status',
        'completed_at',
        'data_payload',
        'company_id',
        'auditor_id',
        'questionnaire_answers',
        'questionnaire_completed',
    ];

    protected $casts = [
        'completed_at'            => 'datetime',
        'data_payload'            => 'array',
        'questionnaire_answers'   => 'array',
        'questionnaire_completed' => 'boolean',
    ];

    public function auditType(): BelongsTo
    {
        return $this->belongsTo(AuditType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
