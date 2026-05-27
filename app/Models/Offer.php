<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = [
        'offer_number',
        'offer_title',
        'offer_date',
        'offer_description',
        'html_content',
        'services',
        'works',
        'materials',
        'custom_sections',
        'total_price',
        'status',
        'crm_deal_id',
        'offer_template_id',
        'company_id',
        'customer_name',
        'customer_nip',
        'customer_address',
        'customer_city',
        'customer_postal_code',
        'customer_phone',
        'customer_email',
        'profit_percent',
        'profit_amount',
        'schedule_enabled',
        'schedule',
        'payment_terms',
        'show_unit_prices',
        'km_rate',
        'hour_rate',
        'distance_km',
        'travel_hours',
        'travel_cost',
        'auditor_hours',
        'created_by',
    ];

    protected $casts = [
        'services'         => 'array',
        'works'            => 'array',
        'materials'        => 'array',
        'custom_sections'  => 'array',
        'schedule'         => 'array',
        'payment_terms'    => 'array',
        'schedule_enabled' => 'boolean',
        'show_unit_prices' => 'boolean',
        'offer_date'       => 'date',
        'km_rate'          => 'float',
        'hour_rate'        => 'float',
        'distance_km'      => 'float',
        'travel_hours'     => 'float',
        'travel_cost'      => 'float',
        'auditor_hours'    => 'float',
    ];

    public function crmDeal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function offerTemplate(): BelongsTo
    {
        return $this->belongsTo(OfferTemplate::class);
    }
}
