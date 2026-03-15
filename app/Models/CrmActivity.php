<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'activity_date',
        'duration',
        'outcome',
        'company_id',
        'deal_id',
        'user_id',
        'attachments',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'company_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'deal_id');
    }
}
