<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\AuditAssignedMail;
use App\Mail\OfferSentMail;
use App\Mail\RegistrationAcceptedMail;
use App\Mail\RegistrationReceivedMail;
use App\Mail\RegistrationRejectedMail;
use App\Mail\WelcomeClientMail;
use App\Models\ClientRegistration;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
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
            'nip'             => ['required', 'string', 'max:20'],
            'name'            => ['required', 'string', 'max:255'],
            'short_name'      => ['nullable', 'string', 'max:100'],
            'city'            => ['nullable', 'string', 'max:100'],
            'street'          => ['nullable', 'string', 'max:255'],
            'postal_code'     => ['nullable', 'string', 'max:10'],
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'phone'           => ['required', 'string', 'max:30'],
            'email'           => ['required', 'email:rfc', 'max:200'],
            'accepted_terms'  => ['accepted'],
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

        unset($validated['accepted_terms']);

        ClientRegistration::create($validated);

        try {
            Mail::to($validated['email'])->send(new RegistrationReceivedMail(
                ClientRegistration::where('nip', $validated['nip'])->latest()->first()
            ));
        } catch (\Throwable $e) {
            report($e);
        }

        return view('rejestracja.success', ['name' => $validated['name']]);
    }

    public function accept(int $id): RedirectResponse
    {
        $reg = ClientRegistration::where('status', 'pending')->findOrFail($id);

        $company = Company::create([
            'name'        => $reg->name,
            'short_name'  => $reg->short_name,
            'nip'         => $reg->nip,
            'city'        => $reg->city,
            'street'      => $reg->street,
            'postal_code' => $reg->postal_code,
            'phone'       => $reg->phone,
            'email'       => $reg->email,
        ]);

        // Create a client user account from the registration contact data
        $plainPassword = $this->generatePassword();
        $firstName     = $reg->first_name ?? '';
        $lastName      = $reg->last_name ?? '';
        $fullName      = trim($firstName . ' ' . $lastName) ?: $reg->name;
        $shortName     = mb_substr($firstName, 0, 3) . mb_substr($lastName, 0, 3);

        $user = User::where('email', $reg->email)->first();

        if (! $user) {
            $user = User::create([
                'name'       => $fullName,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'short_name' => $shortName ?: mb_substr($fullName, 0, 6),
                'email'      => $reg->email,
                'phone'      => $reg->phone,
                'password'   => $plainPassword,
                'role'       => UserRole::Client->value,
                'company_id' => $company->id,
            ]);
        } else {
            $user->update(['company_id' => $company->id, 'role' => UserRole::Client->value]);
            $plainPassword = null; // don't reset existing user's password
        }

        // Link user as primary client of the company
        $company->update(['client_id' => $user->id]);
        $company->assignedUsers()->syncWithoutDetaching([$user->id]);

        $reg->update(['status' => 'accepted']);

        // Send welcome e-mail with login credentials (only for new users)
        $mailError = null;
        if ($plainPassword) {
            try {
                Mail::to($user->email)->send(new WelcomeClientMail($user, $company, $plainPassword));
            } catch (\Throwable $e) {
                report($e);
                $mailError = $e->getMessage();
            }
        }

        $status = 'Firma "' . $reg->name . '" została dodana do systemu. Konto klienta: ' . $user->email . '.';
        if ($mailError) {
            $status .= ' ⚠ Nie udało się wysłać e-maila: ' . $mailError;
        }

        return redirect()->route('dashboard')->with('status', $status);
    }

    public function destroy(int $id): RedirectResponse
    {
        $reg = ClientRegistration::findOrFail($id);
        $name = $reg->name;
        $reg->update(['status' => 'rejected']);

        try {
            Mail::to($reg->email)->send(new RegistrationRejectedMail($reg));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Wniosek rejestracyjny firmy "' . $name . '" został odrzucony.');
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#';
        return substr(str_shuffle(str_repeat($chars, 4)), 0, 12);
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
