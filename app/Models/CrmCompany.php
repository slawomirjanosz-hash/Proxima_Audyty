<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'short_name',
        'nip',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'postal_code',
        'country',
        'type',
        'status',
        'notes',
        'owner_id',
        'source',
        'added_by',
        'customer_type_id',
        'system_company_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CrmCustomerType::class, 'customer_type_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'company_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'company_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'company_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function systemCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'system_company_id');
    }
}
