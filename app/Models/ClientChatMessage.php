<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientChatMessage extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'message',
        'is_from_admin',
        'read_at',
    ];

    protected $casts = [
        'is_from_admin' => 'boolean',
        'read_at'       => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
