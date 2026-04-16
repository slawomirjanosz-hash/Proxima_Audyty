<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInquiry extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'audit_type_id',
        'audit_type_name',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function auditType(): BelongsTo
    {
        return $this->belongsTo(AuditType::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new'       => 'Nowe',
            'in_review' => 'W rozpatrzeniu',
            'accepted'  => 'Przyjęte',
            'rejected'  => 'Odrzucone',
            default     => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new'       => '#1d4f73',
            'in_review' => '#875605',
            'accepted'  => '#1f6a3c',
            'rejected'  => '#9f1f1f',
            default     => '#4c6373',
        };
    }

    public function statusBg(): string
    {
        return match ($this->status) {
            'new'       => '#e9f4ff',
            'in_review' => '#fff4df',
            'accepted'  => '#e6f8ed',
            'rejected'  => '#fff0f0',
            default     => '#f7fafc',
        };
    }
}
