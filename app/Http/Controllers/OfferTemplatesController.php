<?php

namespace App\Http\Controllers;

use App\Models\OfferTemplate;
use Illuminate\Http\Request;

class OfferTemplatesController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $validCategories = array_keys(OfferTemplate::CATEGORIES);

        if (!$category || !in_array($category, $validCategories)) {
            return redirect()->route('audits.index');
        }

        $templates = OfferTemplate::withCount('offers')
            ->where('audit_category', $category)
            ->latest()
            ->get();

        $categoryLabel = OfferTemplate::CATEGORIES[$category];

        $audits = \App\Models\EnergyAudit::with('company')
            ->whereIn('status', \App\Models\EnergyAudit::ACTIVE_STATUSES)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('offer-templates.index', compact('templates', 'category', 'categoryLabel', 'audits'));
    }

    public function create(Request $request)
    {
        $category = $request->input('category');
        $validCategories = array_keys(OfferTemplate::CATEGORIES);
        if (!$category || !in_array($category, $validCategories)) {
            return redirect()->route('audits.index');
        }
        $categoryLabel = OfferTemplate::CATEGORIES[$category];
        $defaultHtml = $this->defaultTemplateHtml();
        return view('offer-templates.create', compact('defaultHtml', 'category', 'categoryLabel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'type_code' => 'required|string|max:100|unique:offer_templates,type_code|regex:/^[a-z0-9_]+$/',
            'default_km_rate'       => 'nullable|numeric|min:0',
            'default_hour_rate'     => 'nullable|numeric|min:0',
            'default_auditor_hours' => 'nullable|numeric|min:0',
        ]);

        $category = $request->input('audit_category');

        OfferTemplate::create([
            'name'                  => $request->input('name'),
            'type_code'             => $request->input('type_code'),
            'audit_category'        => $category,
            'description'           => $request->input('description'),
            'html_content'          => $request->input('html_content'),
            'default_km_rate'       => $request->input('default_km_rate', 1.50),
            'default_hour_rate'     => $request->input('default_hour_rate', 80.00),
            'default_auditor_hours' => $request->input('default_auditor_hours', 8.00),
            'default_items'         => $this->parseDefaultItems($request),
            'default_fields'        => $this->parseDefaultFields($request),
            'is_active'             => $request->boolean('is_active', true),
            'created_by'            => auth()->id(),
        ]);

        return redirect()->route('offer-templates.index', ['category' => $category])
            ->with('status', 'Szablon oferty został utworzony.');
    }

    public function edit(OfferTemplate $offerTemplate)
    {
        return view('offer-templates.edit', ['template' => $offerTemplate]);
    }

    public function update(Request $request, OfferTemplate $offerTemplate)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'type_code' => 'required|string|max:100|regex:/^[a-z0-9_]+$/|unique:offer_templates,type_code,' . $offerTemplate->id,
            'default_km_rate'       => 'nullable|numeric|min:0',
            'default_hour_rate'     => 'nullable|numeric|min:0',
            'default_auditor_hours' => 'nullable|numeric|min:0',
        ]);

        $offerTemplate->update([
            'name'                  => $request->input('name'),
            'type_code'             => $request->input('type_code'),
            'audit_category'        => $request->input('audit_category', $offerTemplate->audit_category),
            'description'           => $request->input('description'),
            'html_content'          => $request->input('html_content'),
            'default_km_rate'       => $request->input('default_km_rate', 1.50),
            'default_hour_rate'     => $request->input('default_hour_rate', 80.00),
            'default_auditor_hours' => $request->input('default_auditor_hours', 8.00),
            'default_items'         => $this->parseDefaultItems($request),
            'default_fields'        => $this->parseDefaultFields($request),
            'is_active'             => $request->boolean('is_active', true),
        ]);

        return redirect()->route('offer-templates.edit', $offerTemplate)
            ->with('status', 'Szablon został zaktualizowany.');
    }

    public function destroy(OfferTemplate $offerTemplate)
    {
        if ($offerTemplate->offers()->exists()) {
            return back()->with('error', 'Nie można usunąć szablonu — istnieją oferty z tym szablonem.');
        }
        $category = $offerTemplate->audit_category;
        $offerTemplate->delete();
        return redirect()->route('offer-templates.index', ['category' => $category])
            ->with('status', 'Szablon został usunięty.');
    }

    /** Duplicate a global template into an audit category */
    public function duplicate(Request $request, OfferTemplate $offerTemplate)
    {
        $targetCategory = $request->input('target_category');
        $validCategories = ['energetyczny', 'iso50001', 'biale_certyfikaty'];

        if (!in_array($targetCategory, $validCategories)) {
            return back()->with('error', 'Nieprawidłowa kategoria docelowa.');
        }

        $categoryLabel = OfferTemplate::CATEGORIES[$targetCategory] ?? $targetCategory;

        OfferTemplate::create([
            'name'                  => $offerTemplate->name . ' (kopia — ' . $categoryLabel . ')',
            'type_code'             => $offerTemplate->type_code . '_' . $targetCategory . '_' . time(),
            'audit_category'        => $targetCategory,
            'description'           => $offerTemplate->description,
            'html_content'          => $offerTemplate->html_content,
            'default_km_rate'       => $offerTemplate->default_km_rate,
            'default_hour_rate'     => $offerTemplate->default_hour_rate,
            'default_auditor_hours' => $offerTemplate->default_auditor_hours,
            'default_items'         => $offerTemplate->default_items,
            'is_active'             => true,
            'created_by'            => auth()->id(),
        ]);

        return redirect()->route('offer-templates.index', ['category' => $targetCategory])
            ->with('status', 'Szablon skopiowany do: ' . $categoryLabel . '.');
    }

    /** HTML preview of the template with demo data */
    public function preview(OfferTemplate $offerTemplate)
    {
        if (!$offerTemplate->html_content) {
            return back()->with('error', 'Szablon nie ma zdefiniowanego HTML.');
        }

        // Use template's own default_items + default rates for preview;
        // text fields (customer, enesa) come from default_fields so edits are visible immediately.
        $demoItems = $offerTemplate->default_items ?? [
            ['name' => 'Przykładowa pozycja', 'type' => 'Usługa', 'quantity' => 1, 'price' => 8000, 'value' => 8000],
        ];
        $demoTotalNet = array_sum(array_column($demoItems, 'value'));

        $demoHtml = $offerTemplate->renderForOffer([
            'offer_number'  => 'OF-DEMO-001',
            'offer_date'    => now()->format('Y-m-d'),
            'items'         => $demoItems,
            'total_price'   => $demoTotalNet,
            'auditor_hours' => $offerTemplate->default_auditor_hours,
            'km_rate'       => $offerTemplate->default_km_rate,
            'hour_rate'     => $offerTemplate->default_hour_rate,
        ], keepEmptyPlaceholders: true);

        return response($demoHtml, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /** AJAX – return template JSON for the offer form */
    public function apiGet(OfferTemplate $offerTemplate)
    {
        return response()->json([
            'id'                    => $offerTemplate->id,
            'name'                  => $offerTemplate->name,
            'html_content'          => $offerTemplate->html_content,
            'default_km_rate'       => $offerTemplate->default_km_rate,
            'default_hour_rate'     => $offerTemplate->default_hour_rate,
            'default_auditor_hours' => $offerTemplate->default_auditor_hours,
            'default_items'         => $offerTemplate->default_items ?? [],
        ]);
    }

    // ─────────────────────── Helpers ───────────────────────────

    private function parseDefaultFields(Request $request): array
    {
        $keys = ['offer_title','offer_subject','offer_description','customer_type',
                 'payment_terms_text','offer_validity','delivery_deadline','vat_rate',
                 'distance_km','travel_hours',
                 'customer_name','customer_nip','customer_address','customer_postal_code',
                 'customer_city','customer_phone','customer_email',
                 'enesa_name','enesa_nip','enesa_street','enesa_city',
                 'enesa_postal','enesa_email','enesa_phone'];
        $result = [];
        foreach ($keys as $k) {
            $v = $request->input('df_' . $k);
            if ($v !== null && $v !== '') {
                $result[$k] = $k === 'vat_rate' ? (float) $v : (string) $v;
            }
        }
        return $result;
    }

    private function parseDefaultItems(Request $request): ?array
    {
        $names = $request->input('di_name', []);
        if (empty($names)) return null;

        $items = [];
        foreach ($names as $i => $name) {
            if (empty(trim($name))) continue;
            $items[] = [
                'name'     => trim($name),
                'type'     => trim($request->input('di_type.' . $i, '')),
                'quantity' => (float) ($request->input('di_qty.' . $i, 1)),
                'price'    => (float) ($request->input('di_price.' . $i, 0)),
                'value'    => (float) ($request->input('di_qty.' . $i, 1))
                            * (float) ($request->input('di_price.' . $i, 0)),
            ];
        }
        return $items ?: null;
    }

    // ─────────────── Default HTML template ─────────────────────

    private function defaultTemplateHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a1a; background: #fff; padding: 32px 40px; max-width: 800px; margin: 0 auto; }
  /* Header */
  .hdr { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1A4D3A; padding-bottom: 24px; margin-bottom: 36px; }
  .logo-wrap .logo { font-size: 36px; font-weight: 900; color: #1A4D3A; letter-spacing: -1px; }
  .logo-wrap .logo-tagline { font-size: 12px; color: #888; margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }
  .enesa-addr { text-align: right; font-size: 12px; color: #555; line-height: 1.9; }
  .enesa-addr strong { font-size: 13px; color: #1A4D3A; display: block; margin-bottom: 2px; }
  /* Offer title block */
  .title-block { text-align: center; padding: 32px 0; border-bottom: 1px solid #e4edf3; margin-bottom: 32px; }
  .badge { display: inline-block; background: #1A4D3A; color: #fff; padding: 6px 22px; border-radius: 30px; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 16px; }
  .offer-title { font-size: 26px; font-weight: 800; color: #1a1a1a; margin-bottom: 10px; }
  .offer-meta { font-size: 13px; color: #888; }
  .offer-meta span { margin: 0 12px; }
  /* Client block */
  .client-block { background: #f7faf9; border: 1px solid #c3ddd4; border-radius: 12px; padding: 20px 24px; margin-bottom: 32px; }
  .client-block .label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #1A4D3A; font-weight: 700; margin-bottom: 10px; }
  .client-block .c-name { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
  .client-block .c-detail { font-size: 13px; color: #555; line-height: 1.9; }
  /* Section headings */
  .sec-title { font-size: 15px; font-weight: 700; color: #1A4D3A; border-left: 4px solid #1A4D3A; padding-left: 12px; margin: 32px 0 14px; }
  /* Description */
  .description { font-size: 14px; line-height: 1.85; color: #333; margin-bottom: 32px; }
  .description p { margin-bottom: 10px; }
  /* Travel block */
  .travel-box { background: #eef8f3; border: 1px solid #c3ddd4; border-radius: 12px; padding: 20px 24px; margin-bottom: 32px; }
  .travel-box table { width: 100%; font-size: 13px; border-collapse: collapse; }
  .travel-box td { padding: 7px 4px; vertical-align: top; }
  .travel-box td:first-child { color: #555; width: 55%; }
  .travel-box td:last-child { font-weight: 600; }
  .travel-total td { border-top: 2px solid #c3ddd4; padding-top: 12px !important; font-weight: 700 !important; font-size: 15px !important; color: #1A4D3A !important; }
  /* Payment */
  .payment-block { font-size: 14px; line-height: 1.85; margin-bottom: 32px; }
  .payment-block ul { padding-left: 20px; }
  .payment-block li { margin-bottom: 6px; }
  /* Footer */
  .footer { border-top: 2px solid #e4edf3; margin-top: 40px; padding-top: 24px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 12px; color: #888; }
  .sign-box { text-align: center; }
  .sign-line { border-top: 1px solid #bbb; width: 180px; margin: 48px auto 8px; }
  .footer-center { text-align: center; }
  @media print {
    body { padding: 0; }
    .footer { page-break-inside: avoid; }
  }
</style>
</head>
<body>

<!-- ═══ NAGŁÓWEK ═══ -->
<div class="hdr">
  <div class="logo-wrap">
    <div class="logo">ENESA</div>
    <div class="logo-tagline">Audyty Energetyczne</div>
  </div>
  <div class="enesa-addr">
    <strong>ENESA Sp. z o.o.</strong>
    ul. Energetyczna 15<br>
    00-900 Warszawa<br>
    NIP: 123-456-78-90<br>
    biuro@enesa.pl | +48 22 123 45 67
  </div>
</div>

<!-- ═══ TYTUŁ OFERTY ═══ -->
<div class="title-block">
  <div class="badge">Oferta handlowa</div>
  <div class="offer-title">{{offer_title}}</div>
  <div class="offer-meta">
    <span>Nr oferty: <strong>{{offer_number}}</strong></span>
    <span>Data: <strong>{{offer_date}}</strong></span>
    <span>Godzin audytu: <strong>{{auditor_hours}} h</strong></span>
  </div>
</div>

<!-- ═══ ZAMAWIAJĄCY ═══ -->
<div class="client-block">
  <div class="label">Zamawiający</div>
  <div class="c-name">{{customer_name}}</div>
  <div class="c-detail">
    NIP: {{customer_nip}}<br>
    {{customer_address}}, {{customer_postal_code}} {{customer_city}}<br>
    Tel: {{customer_phone}} &nbsp;|&nbsp; E-mail: {{customer_email}}
  </div>
</div>

<!-- ═══ OPIS / PRZEDMIOT OFERTY ═══ -->
<div class="sec-title">Przedmiot oferty</div>
<div class="description">
  {{description}}
</div>

<!-- ═══ ZAKRES I WYCENA ═══ -->
<div class="sec-title">Zakres i wycena</div>
{{items_table}}

<!-- ═══ KOSZTY DOJAZDU ═══ -->
<div class="sec-title">Koszty dojazdu</div>
<div class="travel-box">
  <table>
    <tr>
      <td>Odległość od siedziby ENESA (w obie strony):</td>
      <td>{{distance_km}} km × 2 = <strong>{{distance_km}} km</strong></td>
    </tr>
    <tr>
      <td>Stawka za przejazd:</td>
      <td>{{km_rate}} zł/km</td>
    </tr>
    <tr>
      <td>Czas dojazdu (w obie strony):</td>
      <td>{{travel_hours}} h × 2</td>
    </tr>
    <tr>
      <td>Stawka za godzinę jazdy:</td>
      <td>{{hour_rate}} zł/h</td>
    </tr>
    <tr class="travel-total">
      <td>Koszt dojazdu łącznie (netto):</td>
      <td>{{travel_cost}} zł</td>
    </tr>
  </table>
</div>

<!-- ═══ WARUNKI PŁATNOŚCI ═══ -->
<div class="sec-title">Warunki płatności</div>
<div class="payment-block">
  {{payment_terms}}
</div>

<!-- ═══ STOPKA / PODPISY ═══ -->
<div class="footer">
  <div class="sign-box">
    <div class="sign-line"></div>
    <div>Sporządził</div>
  </div>
  <div class="footer-center">
    ENESA Sp. z o.o. &nbsp;|&nbsp; ul. Energetyczna 15, 00-900 Warszawa<br>
    biuro@enesa.pl &nbsp;|&nbsp; www.enesa.pl
  </div>
  <div class="sign-box">
    <div class="sign-line"></div>
    <div>Data i podpis Zamawiającego</div>
  </div>
</div>

</body>
</html>
HTML;
    }
}
