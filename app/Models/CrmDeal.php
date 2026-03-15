<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmDeal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'company_id',
        'value',
        'currency',
        'stage',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'owner_id',
        'description',
        'lost_reason',
        'user_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'company_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'deal_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'deal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'crm_deal_user');
    }

    public function getWeightedValueAttribute(): float
    {
        return (float) $this->value * ((int) $this->probability / 100);
    }
}
