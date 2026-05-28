<?php

namespace App\Models;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferTemplate extends Model
{
    const CATEGORIES = [
        'global'            => 'Szablony globalne',
        'energetyczny'      => 'Audyt Energetyczny',
        'iso50001'          => 'Audyt ISO 50001',
        'biale_certyfikaty' => 'Białe Certyfikaty',
    ];

    protected $fillable = [
        'name',
        'type_code',
        'audit_category',
        'description',
        'html_content',
        'default_km_rate',
        'default_hour_rate',
        'default_auditor_hours',
        'default_items',
        'default_fields',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'default_items'         => 'array',
        'default_fields'        => 'array',
        'is_active'             => 'boolean',
        'default_km_rate'      => 'float',
        'default_hour_rate'    => 'float',
        'default_auditor_hours' => 'float',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /** Generate final HTML by replacing placeholders with offer data. */
    public function renderForOffer(array $data): string
    {
        $html = $this->html_content ?? '';
        $df   = $this->default_fields ?? [];

        // Build items table HTML
        $itemsHtml = $this->buildItemsTable($data['items'] ?? []);

        // Travel cost
        $distKm     = (float) ($data['distance_km'] ?: $df['distance_km'] ?? 0);
        $kmRate     = (float) ($data['km_rate'] ?? $this->default_km_rate);
        $travelH    = (float) ($data['travel_hours'] ?: $df['travel_hours'] ?? 0);
        $hourRate   = (float) ($data['hour_rate'] ?? $this->default_hour_rate);
        $travelCost = ($distKm * $kmRate * 2) + ($travelH * $hourRate * 2);

        // VAT calc
        $totalNet   = (float) ($data['total_price'] ?? 0);
        $vatRate    = (float) ($data['vat_rate'] ?? $df['vat_rate'] ?? 23);
        $vatAmt     = round($totalNet * $vatRate / 100, 2);
        $totalGross = $totalNet + $vatAmt;

        $paymentHtml = '';
        foreach ($data['payment_terms'] ?? [] as $pt) {
            $paymentHtml .= '<li>' . e($pt['description'] ?? '') . ($pt['percent'] ? ' (' . $pt['percent'] . '%)' : '') . ($pt['deadline'] ? ' — ' . $pt['deadline'] : '') . '</li>';
        }
        if (!$paymentHtml) {
            $paymentHtml = $df['payment_terms_text'] ?? 'Płatność na podstawie faktury VAT, 14 dni od wystawienia.';
        } else {
            $paymentHtml = '<ul>' . $paymentHtml . '</ul>';
        }

        $map = [
            '{{offer_number}}'         => e($data['offer_number'] ?? ''),
            '{{offer_title}}'          => e($data['offer_title'] ?: $df['offer_title'] ?? ''),
            '{{offer_date}}'           => $data['offer_date'] ?? '',
            '{{offer_subject}}'        => e($data['offer_subject'] ?: $df['offer_subject'] ?? ''),
            '{{customer_name}}'        => e($data['customer_name'] ?: $df['customer_name'] ?? ''),
            '{{customer_type}}'        => e($data['customer_type'] ?: $df['customer_type'] ?? ''),
            '{{customer_nip}}'         => e($data['customer_nip'] ?: $df['customer_nip'] ?? ''),
            '{{customer_address}}'     => e($data['customer_address'] ?: $df['customer_address'] ?? ''),
            '{{customer_postal_code}}' => e($data['customer_postal_code'] ?: $df['customer_postal_code'] ?? ''),
            '{{customer_city}}'        => e($data['customer_city'] ?: $df['customer_city'] ?? ''),
            '{{customer_phone}}'       => e($data['customer_phone'] ?: $df['customer_phone'] ?? ''),
            '{{customer_email}}'       => e($data['customer_email'] ?: $df['customer_email'] ?? ''),
            '{{description}}'          => $data['description'] ?: $df['offer_description'] ?? '',
            '{{items_table}}'          => $itemsHtml,
            '{{distance_km}}'          => number_format($distKm, 1, ',', ' '),
            '{{km_rate}}'              => number_format($kmRate, 2, ',', ' '),
            '{{travel_hours}}'         => number_format($travelH, 1, ',', ' '),
            '{{hour_rate}}'            => number_format($hourRate, 2, ',', ' '),
            '{{travel_cost}}'          => number_format($travelCost, 2, ',', ' '),
            '{{total_price_net}}'      => number_format($totalNet, 2, ',', ' '),
            '{{vat_rate}}'             => $vatRate . '%',
            '{{total_price_vat}}'      => number_format($vatAmt, 2, ',', ' '),
            '{{total_price}}'          => number_format($totalGross, 2, ',', ' '),
            '{{auditor_hours}}'        => number_format((float) ($data['auditor_hours'] ?? $this->default_auditor_hours), 1, ',', ' '),
            '{{payment_terms}}'        => $paymentHtml,
            '{{offer_validity}}'       => e($data['offer_validity'] ?? $df['offer_validity'] ?? '30 dni'),
            '{{delivery_deadline}}'    => e($data['delivery_deadline'] ?? $df['delivery_deadline'] ?? ''),
            // ENESA data from SystemSetting (template defaults used as fallback)
            '{{enesa_name}}'           => e(SystemSetting::get('enesa_name',            '') ?: $df['enesa_name']   ?? 'Enesa Sp. z o.o.'),
            '{{enesa_nip}}'            => e(SystemSetting::get('enesa_nip',             '') ?: $df['enesa_nip']    ?? ''),
            '{{enesa_street}}'         => e(SystemSetting::get('enesa_street',          '') ?: $df['enesa_street'] ?? 'ul. Konarskiego 18C'),
            '{{enesa_city}}'           => e(SystemSetting::get('enesa_city',            '') ?: $df['enesa_city']   ?? 'Gliwice'),
            '{{enesa_postal}}'         => e(SystemSetting::get('enesa_postal',          '') ?: $df['enesa_postal'] ?? '44-100'),
            '{{enesa_email}}'          => e(SystemSetting::get('company_contact_email', '') ?: $df['enesa_email']  ?? 'biuro@enesa.pl'),
            '{{enesa_phone}}'          => e(SystemSetting::get('enesa_phone',           '') ?: $df['enesa_phone']  ?? ''),
        ];

        return str_replace(array_keys($map), array_values($map), $html);
    }

    private function buildItemsTable(array $allItems): string
    {
        if (empty($allItems)) {
            return '<p style="color:#888;font-style:italic;">Brak pozycji.</p>';
        }

        $rows  = '';
        $total = 0;
        foreach ($allItems as $i => $item) {
            $val    = (float) ($item['value'] ?? 0);
            $total += $val;
            $rows  .= '<tr>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:center;">' . ($i + 1) . '</td>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;font-weight:600;">' . e($item['name'] ?? '') . '</td>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;color:#555;">' . e($item['type'] ?? '') . '</td>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:center;">' . ($item['quantity'] ?? 1) . '</td>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:right;">' . number_format((float)($item['price'] ?? 0), 2, ',', ' ') . ' zł</td>'
                . '<td style="padding:8px 10px;border-bottom:1px solid #e4edf3;text-align:right;font-weight:700;">' . number_format($val, 2, ',', ' ') . ' zł</td>'
                . '</tr>';
        }

        return '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
            . '<thead><tr>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:center;width:40px;">Nr</th>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:left;">Nazwa pozycji</th>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:left;">Opis / Typ</th>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:center;width:60px;">Ilość</th>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:right;width:110px;">Cena jedn.</th>'
            . '<th style="background:#1A4D3A;color:#fff;padding:10px;text-align:right;width:110px;">Wartość</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '<tfoot><tr>'
            . '<td colspan="5" style="padding:10px;text-align:right;font-weight:700;background:#1A4D3A;color:#fff;font-size:14px;">Razem (netto)</td>'
            . '<td style="padding:10px;text-align:right;font-weight:800;font-size:15px;background:#1A4D3A;color:#fff;">' . number_format($total, 2, ',', ' ') . ' zł</td>'
            . '</tr></tfoot>'
            . '</table>';
    }
}
