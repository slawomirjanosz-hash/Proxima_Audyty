<?php

namespace App\Http\Controllers;

use App\Models\ClientInquiry;
use App\Models\CrmCompany;
use App\Models\CrmDeal;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OffersController extends Controller
{
    public function index()
    {
        return view('offers.index');
    }

    public function portfolio()
    {
        $offers = Offer::where('status', 'portfolio')->with('crmDeal')->latest()->get();
        return view('offers.portfolio', compact('offers'));
    }

    public function inprogress()
    {
        $offers = Offer::where('status', 'inprogress')->with('crmDeal')->latest()->get();
        return view('offers.inprogress', compact('offers'));
    }

    public function archived()
    {
        $offers = Offer::where('status', 'archived')->with('crmDeal')->latest()->get();
        return view('offers.archived', compact('offers'));
    }

    public function create(Request $request)
    {
        $nextNumber = $this->generateOfferNumber();
        $crmDeals = CrmDeal::orderBy('name')->get();
        $crmCompanies = CrmCompany::orderBy('name')->get();

        $prefill = null;
        if ($request->filled('from_company')) {
            $prefill = \App\Models\Company::find($request->integer('from_company'));
        }

        $fromInquiry = $request->filled('inquiry_id')
            ? ClientInquiry::find($request->integer('inquiry_id'))
            : null;

        return view('offers.create', compact('nextNumber', 'crmDeals', 'crmCompanies', 'prefill', 'fromInquiry'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'offer_title' => 'required|string|max:255',
            'offer_number' => 'nullable|string|max:100',
            'offer_date' => 'nullable|date',
            'status' => 'required|in:portfolio,inprogress',
        ]);

        $data = $this->prepareOfferData($request);
        $data['created_by'] = auth()->id();
        if ($request->filled('company_id')) {
            $data['company_id'] = $request->integer('company_id');
        }

        $offer = Offer::create($data);

        // If created from inquiry, link the offer
        if ($request->filled('inquiry_id')) {
            ClientInquiry::where('id', $request->integer('inquiry_id'))
                ->update(['status' => 'accepted', 'offer_id' => $offer->id]);
        }

        return redirect()->route('offers.index')
            ->with('status', 'Oferta została zapisana.');
    }

    public function edit(Offer $offer)
    {
        $crmDeals = CrmDeal::orderBy('name')->get();
        $crmCompanies = CrmCompany::orderBy('name')->get();
        return view('offers.edit', compact('offer', 'crmDeals', 'crmCompanies'));
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'offer_title' => 'required|string|max:255',
        ]);

        $data = $this->prepareOfferData($request);
        $offer->update($data);

        return redirect()->route('offers.edit', $offer)
            ->with('status', 'Oferta została zaktualizowana.');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('offers.index')
            ->with('status', 'Oferta została usunięta.');
    }

    public function archive(Offer $offer)
    {
        $offer->update(['status' => 'archived']);
        return back()->with('status', 'Oferta została zarchiwizowana.');
    }

    public function copy(Offer $offer)
    {
        $newOffer = $offer->replicate();
        $newOffer->offer_number = $this->generateOfferNumber();
        $newOffer->offer_title = 'Kopia – ' . $offer->offer_title;
        $newOffer->created_by = auth()->id();
        $newOffer->save();

        return redirect()->route('offers.edit', $newOffer)
            ->with('status', 'Oferta została skopiowana. Edytuj kopię.');
    }

    public function copyForCompany(Request $request, \App\Models\Company $company)
    {
        $request->validate(['offer_id' => 'required|exists:offers,id']);
        $source = Offer::findOrFail($request->integer('offer_id'));

        $newOffer = $source->replicate();
        $newOffer->offer_number    = $this->generateOfferNumber();
        $newOffer->offer_title     = $source->offer_title . ' – ' . $company->name;
        $newOffer->status          = 'inprogress';
        $newOffer->company_id      = $company->id;
        $newOffer->customer_name   = $company->name;
        $newOffer->customer_nip    = $company->nip;
        $newOffer->customer_address= $company->street;
        $newOffer->customer_city   = $company->city;
        $newOffer->customer_postal_code = $company->postal_code;
        $newOffer->customer_phone  = $company->phone;
        $newOffer->customer_email  = $company->email ?? $company->client?->email;
        $newOffer->created_by      = auth()->id();
        $newOffer->save();

        if ($request->filled('inquiry_id')) {
            ClientInquiry::where('id', $request->integer('inquiry_id'))
                ->where('company_id', $company->id)
                ->update(['status' => 'accepted', 'offer_id' => $newOffer->id]);
        }

        return redirect()->route('offers.edit', $newOffer)
            ->with('status', 'Oferta z portfolio skopiowana i personalizowana dla klienta. Sprawdź i dostosuj.');
    }

    public function sendToClient(Offer $offer)
    {
        $email = $offer->customer_email;
        if (! $email && $offer->company) {
            $email = $offer->company->email ?? $offer->company->client?->email;
        }

        if (! $email) {
            return back()->with('error', 'Brak adresu e-mail klienta w ofercie. Uzupełnij dane klienta.');
        }

        $offerLabel = ($offer->offer_number ? $offer->offer_number . ': ' : '') . $offer->offer_title;
        $subject    = 'Masz nową ofertę – ' . $offerLabel;
        $clientZoneUrl = url('/strefa-klienta');
        $body = "Dzień dobry,\n\n"
            . "Przygotowaliśmy dla Ciebie ofertę: {$offerLabel}\n\n"
            . "Aby ją zobaczyć, wygenerować PDF lub zaakceptować, zaloguj się do Strefy klienta:\n"
            . "{$clientZoneUrl}\n\n"
            . "Jeśli masz pytania, napisz do nas przez chat w Strefie klienta.\n\n"
            . "Pozdrawiamy,\nZespół ENESA";

        Mail::send([], [], function ($msg) use ($email, $subject, $body) {
            $msg->to($email)
                ->subject($subject)
                ->text($body);
        });

        // Mark the linked inquiry offer as sent
        ClientInquiry::where('offer_id', $offer->id)
            ->update(['status' => 'in_review']);

        return back()->with('status', 'Oferta wysłana do klienta na adres ' . $email . '.');
    }

    public function generatePdf(Offer $offer)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('offers.print', compact('offer'));
        return $pdf->download('oferta-' . ($offer->offer_number ?: $offer->id) . '.pdf');
    }

    public function generateWord(Offer $offer)
    {
        $html = view('offers.print', ['offer' => $offer, 'forWord' => true])->render();
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-word',
            'Content-Disposition' => 'attachment; filename="oferta-' . ($offer->offer_number ?: $offer->id) . '.doc"',
        ]);
    }

    public function settings()
    {
        $settings = \Illuminate\Support\Facades\Schema::hasTable('offer_settings')
            ? DB::table('offer_settings')->first()
            : null;
        return view('offers.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->only([
            'element1_type', 'element1_value', 'separator1',
            'element2_type', 'element2_value', 'separator2',
            'element3_type', 'element3_value', 'separator3',
            'element4_type', 'element4_value',
            'start_number',
        ]);

        $existing = DB::table('offer_settings')->first();
        if ($existing) {
            DB::table('offer_settings')->update($data);
        } else {
            DB::table('offer_settings')->insert($data);
        }

        return back()->with('status', 'Ustawienia numeracji zostały zapisane.');
    }

    // ─────────────────────────── Helpers ───────────────────────────

    private function prepareOfferData(Request $request): array
    {
        $services      = $this->processSectionItems($request->input('services', []));
        $works         = $this->processSectionItems($request->input('works', []));
        $materials     = $this->processSectionItems($request->input('materials', []));
        $customSections = array_values(array_filter(
            $request->input('custom_sections', []),
            fn($s) => !empty($s['name'])
        ));

        foreach ($customSections as &$cs) {
            $cs['items'] = $this->processSectionItems($cs['items'] ?? []);
        }
        unset($cs);

        $grandTotal = $this->sumItems($services)
            + $this->sumItems($works)
            + $this->sumItems($materials);

        foreach ($customSections as $cs) {
            $grandTotal += $this->sumItems($cs['items'] ?? []);
        }

        $profitAmount = (float) $request->input('profit_amount', 0);
        $totalPrice   = $grandTotal + $profitAmount;

        return [
            'offer_number'        => $request->input('offer_number'),
            'offer_title'         => $request->input('offer_title'),
            'offer_date'          => $request->input('offer_date') ?: null,
            'offer_description'   => $request->input('offer_description'),
            'services'            => $services ?: null,
            'works'               => $works ?: null,
            'materials'           => $materials ?: null,
            'custom_sections'     => $customSections ?: null,
            'total_price'         => $totalPrice,
            'status'              => $request->input('status', 'portfolio'),
            'crm_deal_id'         => $request->input('crm_deal_id') ?: null,
            'customer_name'       => $request->input('customer_name'),
            'customer_nip'        => $request->input('customer_nip'),
            'customer_address'    => $request->input('customer_address'),
            'customer_city'       => $request->input('customer_city'),
            'customer_postal_code'=> $request->input('customer_postal_code'),
            'customer_phone'      => $request->input('customer_phone'),
            'customer_email'      => $request->input('customer_email'),
            'profit_percent'      => (float) $request->input('profit_percent', 0),
            'profit_amount'       => $profitAmount,
            'schedule_enabled'    => $request->boolean('schedule_enabled'),
            'schedule'            => $request->input('schedule') ?: null,
            'payment_terms'       => $request->input('payment_terms') ?: null,
            'show_unit_prices'    => $request->boolean('show_unit_prices', true),
        ];
    }

    private function processSectionItems(array $items): array
    {
        $cleaned = array_map(function ($item) {
            foreach (['price', 'catalog_price', 'value'] as $field) {
                if (isset($item[$field])) {
                    // strip formatting: spaces, currency, replace comma decimal separator
                    $raw = str_replace([' ', 'zł', '\u00a0'], '', (string) $item[$field]);
                    $raw = str_replace(',', '.', $raw);
                    $item[$field] = (float) $raw;
                }
            }
            return $item;
        }, $items);
        return array_values(array_filter($cleaned, fn($item) => !empty($item['name'])));
    }

    private function sumItems(array $items): float
    {
        return array_sum(array_map(fn($item) => (float) ($item['value'] ?? 0), $items));
    }

    public function generateOfferNumber(): string
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('offer_settings')) {
            $count = Offer::count() + 1;
            return 'OF-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }
        $settings = DB::table('offer_settings')->first();
        if (!$settings) {
            $count = Offer::count() + 1;
            return 'OF-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        $yearCount = Offer::whereYear('created_at', now()->year)->count() + 1;
        $startNum  = (int) ($settings->start_number ?? 1);
        $seqNum    = $startNum + $yearCount - 1;

        $elements = [
            ['type' => $settings->element1_type, 'value' => $settings->element1_value ?? ''],
            ['type' => $settings->element2_type, 'value' => $settings->element2_value ?? ''],
            ['type' => $settings->element3_type, 'value' => $settings->element3_value ?? ''],
            ['type' => $settings->element4_type, 'value' => $settings->element4_value ?? ''],
        ];
        $separators = [
            $settings->separator1 ?? '-',
            $settings->separator2 ?? '-',
            $settings->separator3 ?? '-',
        ];

        $parts = [];
        foreach ($elements as $i => $el) {
            $val = match ($el['type']) {
                'text'   => $el['value'],
                'date'   => now()->format('Y-m-d'),
                'year'   => now()->format('Y'),
                'month'  => now()->format('m'),
                'time'   => now()->format('Hi'),
                'number' => str_pad($seqNum, 4, '0', STR_PAD_LEFT),
                default  => null,
            };
            if ($val !== null && $val !== '' && $el['type'] !== 'empty') {
                if (!empty($parts) && isset($separators[$i - 1])) {
                    $parts[] = $separators[$i - 1];
                }
                $parts[] = $val;
            }
        }

        $number = implode('', $parts);
        return $number ?: 'OF-' . str_pad(Offer::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
