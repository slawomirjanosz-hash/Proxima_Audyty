<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $fillable = [
        'user_id',
        'context_type',
        'context_id',
        'title',
        'status',
        'protocol_data',
        'protocol_generated_at',
    ];

    protected $casts = [
        'protocol_data'          => 'array',
        'protocol_generated_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class)->orderBy('created_at');
    }

    public function contextModel(): mixed
    {
        return match ($this->context_type) {
            'energy_audit' => EnergyAudit::find($this->context_id),
            'iso50001'     => Iso50001Audit::find($this->context_id),
            'offer'        => Offer::find($this->context_id),
            default        => null,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
