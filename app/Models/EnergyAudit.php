<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'audit_type',
        'audit_type_id',
        'status',
        'completed_at',
        'data_payload',
        'company_id',
        'auditor_id',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'data_payload' => 'array',
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
