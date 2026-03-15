<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCustomerType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(CrmCompany::class, 'customer_type_id');
    }
}
