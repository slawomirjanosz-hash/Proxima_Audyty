<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTypeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_type_id',
        'name',
        'position',
        'tasks',
        'data_fields',
    ];

    protected $casts = [
        'tasks' => 'array',
        'data_fields' => 'array',
    ];

    public function auditType(): BelongsTo
    {
        return $this->belongsTo(AuditType::class);
    }
}
