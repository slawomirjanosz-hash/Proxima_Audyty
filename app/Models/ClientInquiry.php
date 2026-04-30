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
        'offer_id',
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

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'new'            => 'Nowe',
            'in_review'      => 'Oferta do oceny',
            'offer_accepted' => 'Oferta zaakceptowana',
            'accepted'       => 'Przyjęte',
            'rejected'       => 'Odrzucone',
            'closed'         => 'Zamknięte',
            default          => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'new'            => '#1d4f73',
            'in_review'      => '#875605',
            'offer_accepted' => '#065f46',
            'accepted'       => '#1f6a3c',
            'rejected'       => '#9f1f1f',
            'closed'         => '#6b7280',
            default          => '#4c6373',
        };
    }

    public function statusBg(): string
    {
        return match ($this->status) {
            'new'            => '#e9f4ff',
            'in_review'      => '#fff4df',
            'offer_accepted' => '#d1fae5',
            'accepted'       => '#e6f8ed',
            'rejected'       => '#fff0f0',
            'closed'         => '#f3f4f6',
            default          => '#f7fafc',
        };
    }
}
