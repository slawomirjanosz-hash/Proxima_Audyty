<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTaskChange extends Model
{
    protected $fillable = [
        'task_id',
        'entity_type',
        'entity_id',
        'user_id',
        'change_type',
        'change_details',
    ];

    protected $casts = [
        'change_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CrmTask::class, 'task_id');
    }
}
