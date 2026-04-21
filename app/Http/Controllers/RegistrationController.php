<?php

namespace App\Http\Controllers;

use App\Models\ClientRegistration;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        return view('rejestracja.index');
    }

    public function lookupNip(Request $request): JsonResponse
    {
        $nip = preg_replace('/\D/', '', (string) $request->input('nip', ''));

        if (strlen($nip) !== 10) {
            return response()->json(['error' => 'NIP musi mieć dokładnie 10 cyfr.'], 422);
        }

        if (!$this->nipChecksum($nip)) {
            return response()->json(['error' => 'Podany NIP ma nieprawidłową sumę kontrolną.'], 422);
        }

        $date = now()->format('Y-m-d');

        try {
            $response = Http::timeout(8)->get("https://wl-api.mf.gov.pl/api/search/nip/{$nip}", [
                'date' => $date,
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Nie znaleziono podmiotu dla podanego NIP w rejestrze podatników VAT.'], 404);
            }

            $data = $response->json();
            $subject = $data['result']['subject'] ?? null;

            if (!$subject) {
                return response()->json(['error' => 'Brak danych dla podanego NIP.'], 404);
            }

            $name = Company::normalizeLegalForm((string) ($subject['name'] ?? ''));
            $address = (string) ($subject['workingAddress'] ?? $subject['residenceAddress'] ?? '');

            $city       = '';
            $street     = '';
            $postalCode = '';

            if ($address) {
                // Format: "ul. Przykładowa 1, 00-001 Warszawa"
                if (preg_match('/(\d{2}-\d{3})\s+([^,]+)/', $address, $m)) {
                    $postalCode = $m[1];
                    $city       = trim($m[2]);
                    $pos        = (int) strpos($address, $m[0]);
                    $street     = rtrim(trim(substr($address, 0, $pos)), ',');
                }
            }

            return response()->json([
                'name'        => $name,
                'nip'         => $nip,
                'city'        => $city ? mb_convert_case($city, MB_CASE_TITLE, 'UTF-8') : '',
                'street'      => $street ? mb_convert_case($street, MB_CASE_TITLE, 'UTF-8') : '',
                'postal_code' => $postalCode,
            ]);
        } catch (\Exception) {
            return response()->json(['error' => 'Błąd połączenia z serwisem MF. Proszę wypełnić dane ręcznie.'], 503);
        }
    }

    public function store(Request $request): RedirectResponse|View
    {
        $validated = $request->validate([
            'nip'         => ['required', 'string', 'max:20'],
            'name'        => ['required', 'string', 'max:255'],
            'short_name'  => ['nullable', 'string', 'max:100'],
            'city'        => ['nullable', 'string', 'max:100'],
            'street'      => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone'       => ['required', 'string', 'max:30'],
            'email'       => ['required', 'email:rfc', 'max:200'],
        ]);

        $validated['nip'] = preg_replace('/\D/', '', $validated['nip']);

        if (!$this->nipChecksum($validated['nip'])) {
            return back()->withErrors(['nip' => 'Podany NIP ma nieprawidłową sumę kontrolną.'])->withInput();
        }

        $validated['name'] = Company::normalizeLegalForm($validated['name']);

        if (ClientRegistration::where('nip', $validated['nip'])->where('status', 'pending')->exists()) {
            return back()->withErrors(['nip' => 'Wniosek rejestracyjny dla tego NIP jest już w trakcie rozpatrywania.'])->withInput();
        }

        if (Company::where('nip', $validated['nip'])->exists()) {
            return back()->withErrors(['nip' => 'Firma z tym NIP jest już zarejestrowana w systemie.'])->withInput();
        }

        ClientRegistration::create($validated);

        return view('rejestracja.success', ['name' => $validated['name']]);
    }

    public function accept(int $id): RedirectResponse
    {
        $reg = ClientRegistration::where('status', 'pending')->findOrFail($id);

        Company::create([
            'name'        => $reg->name,
            'short_name'  => $reg->short_name,
            'nip'         => $reg->nip,
            'city'        => $reg->city,
            'street'      => $reg->street,
            'postal_code' => $reg->postal_code,
            'phone'       => $reg->phone,
            'email'       => $reg->email,
        ]);

        $reg->update(['status' => 'accepted']);

        return redirect()->route('dashboard')
            ->with('status', 'Firma "' . $reg->name . '" została dodana do systemu.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $reg = ClientRegistration::findOrFail($id);
        $name = $reg->name;
        $reg->update(['status' => 'rejected']);

        return redirect()->route('dashboard')
            ->with('status', 'Wniosek rejestracyjny firmy "' . $name . '" zostal odrzucony.');
    }

    private function nipChecksum(string $nip): bool
    {
        if (!preg_match('/^\d{10}$/', $nip)) {
            return false;
        }

        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum     = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $nip[$i] * $weights[$i];
        }

        $checkDigit = $sum % 11;

        return $checkDigit < 10 && $checkDigit === (int) $nip[9];
    }
}
