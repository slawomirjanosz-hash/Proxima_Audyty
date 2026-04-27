<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeatRecoveryCalculation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'fuel_type',
        'boiler_power',
        'boiler_efficiency',
        'flue_temp_in',
        'mass_flow',
        'xh2o',
        'medium_type',
        'medium_temp_supply',
        'medium_temp_return',
        'medium_pressure',
        'medium_flow',
        'exchangers',
        'result_dry_kw',
        'result_wet_kw',
        'result_total_kw',
    ];

    protected $casts = [
        'exchangers'         => 'array',
        'boiler_power'       => 'float',
        'boiler_efficiency'  => 'float',
        'flue_temp_in'       => 'float',
        'mass_flow'          => 'float',
        'xh2o'               => 'float',
        'medium_temp_supply' => 'float',
        'medium_temp_return' => 'float',
        'medium_pressure'    => 'float',
        'medium_flow'        => 'float',
        'result_dry_kw'      => 'float',
        'result_wet_kw'      => 'float',
        'result_total_kw'    => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fuelLabel(): string
    {
        return match ($this->fuel_type) {
            'coal'  => 'Węgiel kamienny',
            default => 'Gaz ziemny GZ-50',
        };
    }

    public function mediumLabel(): string
    {
        return match ($this->medium_type) {
            'steam'  => 'Para wodna',
            'glycol' => 'Glikol',
            'air'    => 'Powietrze',
            'other'  => 'Inne',
            default  => 'Woda',
        };
    }
}
