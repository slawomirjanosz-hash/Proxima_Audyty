<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Co2IndicatorHistory extends Model
{
    protected $table = 'co2_indicators_history';

    protected $fillable = ['year', 'comb_factor', 'nat_factor', 'source_url', 'created_by'];

    protected $casts = [
        'year'        => 'integer',
        'comb_factor' => 'integer',
        'nat_factor'  => 'integer',
    ];
}
