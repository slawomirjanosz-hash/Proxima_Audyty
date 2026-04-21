<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRegistration extends Model
{
    protected $fillable = [
        'nip',
        'name',
        'short_name',
        'city',
        'street',
        'postal_code',
        'phone',
        'email',
        'status',
    ];
}
