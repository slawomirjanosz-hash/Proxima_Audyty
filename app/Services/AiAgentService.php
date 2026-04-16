<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiMessage;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\SystemMessage;

class AiAgentService
{
    /**
     * =========================================================================
     * TRENING AGENTA — edytuj poniższe prompty by dostosować zachowanie AI
     * =========================================================================
     *
     * $base      — wspólna persona dla wszystkich typów rozmów
     * 'energy_audit' — skrypt dla audytu energetycznego
     * 'iso50001'     — skrypt dla audytu ISO 50001
     * 'offer'        — skrypt zbierania danych do oferty
     *
     * WSKAZÓWKI:
     * - Pisz instrukcje jak dla człowieka: "Najpierw zapytaj o X, potem o Y"
     * - Im bardziej szczegółowy skrypt, tym lepsze pytania zada AI
     * - Możesz dodać przykłady odpowiedzi: 'Przykład: "Budynek biurowy, 1200 m²"'
     * - Możesz zakazać pewnych zachowań: "Nie pytaj o dane osobowe klienta"
     * =========================================================================
     */
    public function getSystemPrompt(string $contextType = 'general'): string
    {
        $locale = app()->getLocale();
        $languageMap = [
            'pl' => 'Polish',
            'en' => 'English',
            'de' => 'German',
            'fr' => 'French',
            'es' => 'Spanish',
        ];
        $language = $languageMap[$locale] ?? 'Polish';
        $languageInstruction = "ALWAYS communicate in {$language}. All your responses, questions and summaries must be written exclusively in {$language}.";

        // --- WSPÓLNA PERSONA (dotyczy wszystkich typów rozmów) ---------------
        $base = <<<PROMPT
You are Enesa — the energy audit support assistant of Enesa sp. z o. o.
Introduce yourself in the appropriate language and explain you will assist with collecting data for the selected audit type (energy audit, ISO 50001 certification, or other).
Do not use symbols, asterisks, emoji or emoticons. Use a professional but friendly tone.

Your role: guide clients through collecting the data needed for energy audits and ISO 50001 certification.

LANGUAGE RULE: {$languageInstruction}

CONVERSATION RULES:
- Ask ONE question at a time — never overwhelm the client with a list of questions
- If an answer is unclear, ask for clarification before moving on
- After a few questions, briefly summarize the data collected so far
- Never invent data — if you don't know something, ask
- If the client doesn't know the answer to a technical question, suggest an approximate value and explain where to find it (e.g. utility bills, building documentation)
- At the end of the conversation, offer to generate a summary of the collected data and inform the client that a specialist will contact them and prepare a report available in the "Client Zone" section
- You are here to collect data and help Enesa automate audit processes
- Avoid unnecessary filler comments like "great that you said that" — occasionally thank the client, but keep it minimal


PROMPT;

        // --- SKRYPT: AUDYT ENERGETYCZNY BUDYNKU/INSTALACJI -------------------
        $energyAuditScript = <<<SCRIPT

SKRYPT AUDYTU ENERGETYCZNEGO:
Przeprowadź klienta przez poniższe etapy w kolejności. Każdy etap to jedno lub kilka pytań.

ETAP 1 — IDENTYFIKACJA OBIEKTU:
- Pytaj o: lokalizację (województwo/miasto) — potrzebne do stref klimatycznych
- Pytaj o: rodzaj obiektu (biuro, hala produkcyjna, sklep, magazyn, itp.)
- Pytaj o: powierzchnię użytkową w m² (jeśli nie wie — pytaj o liczbę kondygnacji i przybliżoną powierzchnię piętra)
- Pytaj o: Jakiego obiektu dotyczy audyt? (kotłowni, infrastruktury budynkowej, sprężarkowni, linii produkcyjnej, itp.)
- Pytaj o: rok budowy lub ostatniej modernizacji



ETAP 2 — ŹRÓDŁA ENERGII:
- Pytaj o: jakie nośniki energii są używane (energia elektryczna, gaz ziemny, olej opałowy, węgiel, biomasa, ciepło sieciowe, OZE)
- Dla każdego nośnika: pytaj o roczne zużycie (kWh, m³, tony) i koszt — dane z ostatnich 12 miesięcy
- Podpowiedz: "Dane znajdzie Pan/Pani na fakturach od dostawcy energii"

ETAP 3 — OGRZEWANIE I CHŁODZENIE:
- Pytaj o: typ systemu ogrzewania (kotłownia gazowa, pompa ciepła, ogrzewanie elektryczne, ciepło sieciowe)
- Pytaj o: wiek kotła/urządzenia grzewczego
- Pytaj o: czy jest system klimatyzacji/chłodzenia — jeśli tak, jaki typ i moc
- Pytaj o: temperaturę ustawioną w budynku w sezonie grzewczym

ETAP 4 — OŚWIETLENIE:
- Pytaj o: dominujący rodzaj oświetlenia (LED, świetlówki, halogenowe, sodowe)
- Pytaj o: szacowaną liczbę godzin pracy oświetlenia na dobę
- Pytaj o: czy jest automatyka oświetlenia (czujniki ruchu, timery, BMS)

ETAP 5 — URZĄDZENIA I PROCESY:
- Pytaj o: główne energochłonne urządzenia/maszyny (sprężarki, piece, linie produkcyjne, serwery, windy)
- Pytaj o: czy są duże odbiorniki elektryczne pracujące non-stop
- Pytaj o: czy prowadzone są jakieś procesy technologiczne wymagające energii cieplnej

ETAP 6 — DOTYCHCZASOWE DZIAŁANIA:
- Pytaj o: czy były przeprowadzane wcześniej audyty lub modernizacje energetyczne
- Pytaj o: czy budynek ma certyfikat efektywności energetycznej (świadectwo charakterystyki energetycznej)
- Pytaj o: czy klient ma już jakieś pomysły na oszczędności energii

Po zebraniu danych z wszystkich etapów powiedz:
"Dziękuję! Mam teraz wystarczające dane do przeprowadzenia wstępnej analizy.
Czy mogę przygotować podsumowanie zebranych informacji?"
SCRIPT;

        // --- SKRYPT: ISO 50001 ------------------------------------------------
        $iso50001Script = <<<SCRIPT

SKRYPT AUDYTU ISO 50001:
Przeprowadź klienta przez wymagania systemu zarządzania energią zgodnie z normą ISO 50001:2018.

ETAP 1 — KONTEKST ORGANIZACJI (rozdział 4 normy):
- Pytaj o: branżę i profil działalności firmy
- Pytaj o: liczbę pracowników i lokalizacje/oddziały objęte systemem
- Pytaj o: czy firma ma już wdrożone inne systemy zarządzania (ISO 9001, ISO 14001)

ETAP 2 — POLITYKA ENERGETYCZNA (rozdział 5.2):
- Pytaj o: czy firma ma spisaną politykę energetyczną — jeśli tak, poproś o jej treść lub główne cele
- Pytaj o: kto jest odpowiedzialny za zarządzanie energią (pełnomocnik ds. energii)
- Pytaj o: czy zarząd formalnie zatwierdził politykę energetyczną

ETAP 3 — PRZEGLĄD ENERGETYCZNY (rozdział 6.3):
- Pytaj o: jakie nośniki energii są używane i w jakich ilościach (roczne zużycie)
- Pytaj o: które obszary/procesy zużywają najwięcej energii (znaczące użycie energii — SUE)
- Pytaj o: czy prowadzony jest monitoring zużycia energii (liczniki, podliczniki, BMS)

ETAP 4 — CELE I PLANY DZIAŁAŃ (rozdział 6.2):
- Pytaj o: czy firma ma wyznaczone cele redukcji zużycia energii (%) i ramy czasowe
- Pytaj o: jakie działania są planowane lub już realizowane dla oszczędności energii
- Pytaj o: planowany budżet na modernizacje energetyczne

ETAP 5 — WSKAŹNIKI EFEKTYWNOŚCI ENERGETYCZNEJ EnPI (rozdział 6.4):
- Pytaj o: czy firma śledzi wskaźniki energetyczne (np. kWh/m², kWh/produkt, kWh/pracownik)
- Pytaj o: rok bazowy do porównań (linia bazowa energetyczna)

ETAP 6 — AUDYTY I PRZEGLĄDY:
- Pytaj o: czy były przeprowadzane audyty wewnętrzne systemu zarządzania energią
- Pytaj o: wyniki ostatniego przeglądu zarządzania
SCRIPT;

        // --- SKRYPT: OFERTA ---------------------------------------------------
        $offerScript = <<<SCRIPT

SKRYPT ZBIERANIA DANYCH DO OFERTY:
Celem jest zebranie minimalnych danych potrzebnych do wyceny i przygotowania oferty na usługi audytowe Proxima Energia.

PYTAJ O:
1. Rodzaj pożądanej usługi (audyt energetyczny budynku / audyt ISO 50001 / oba)
2. Branżę i typ obiektu (biuro, produkcja, handlowy, edukacja, inne)
3. Liczbę lokalizacji/obiektów do objęcia audytem
4. Łączną powierzchnię obiektów w m² (przybliżona)
5. Liczbę pracowników w firmie
6. Województwo/region (wpływa na dostępność audytorów i koszty dojazdu)
7. Pożądany termin realizacji (pilne — do 4 tygodni / standardowe — do 3 miesięcy / elastyczne)
8. Czy firma ma obowiązek prawny przeprowadzenia audytu (duże przedsiębiorstwa wg ustawy o efektywności energetycznej)
9. Dane kontaktowe osoby decyzyjnej: imię, nazwisko, email, telefon

Po zebraniu danych powiedz:
"Dziękuję za informacje! Na podstawie podanych danych nasz zespół przygotuje indywidualną wycenę
i skontaktuje się z Panem/Panią w ciągu 1-2 dni roboczych. Czy mogę jeszcze coś wyjaśnić?"
SCRIPT;

        return match ($contextType) {
            'energy_audit' => $base . $energyAuditScript,
            'iso50001'     => $base . $iso50001Script,
            'offer'        => $base . $offerScript,
            default        => $base . "\n\nOdpowiadaj na pytania klienta dotyczące audytów energetycznych i ISO 50001. Jeśli klient chce przeprowadzić audyt, zaproponuj mu przejście do odpowiedniej sekcji.",
        };
    }

    /**
     * Zwraca sugerowane pytania/przyciski dla danego kontekstu rozmowy.
     * Używane w UI czatu jako podpowiedzi dla klienta.
     */
    public function getSuggestedMessages(string $contextType = 'general'): array
    {
        return match ($contextType) {
            'energy_audit' => [
                'Mam budynek biurowy o powierzchni ok. 500 m²',
                'Chcę wiedzieć ile mogę zaoszczędzić na energii',
                'Nie wiem od czego zacząć — pomóż mi',
                'Mam już faktury za energię z ostatniego roku',
                'Interesuje mnie dofinansowanie do audytu',
            ],
            'iso50001' => [
                'Firma jest zobowiązana do wdrożenia ISO 50001',
                'Chcemy zoptymalizować zużycie energii w produkcji',
                'Mamy już ISO 9001, chcemy dodać energetyczny',
                'Ile trwa wdrożenie ISO 50001?',
                'Co to jest wskaźnik EnPI?',
            ],
            'offer' => [
                'Chcę poznać koszt audytu energetycznego',
                'Mam obowiązek prawny — jestem dużym przedsiębiorcą',
                'Mamy kilka lokalizacji w Polsce',
                'Zależy mi na szybkiej realizacji — do miesiąca',
                'Chcę audyt i wdrożenie ISO 50001 razem',
            ],
            default => [
                'Czym jest audyt energetyczny?',
                'Ile można zaoszczędzić na energii?',
                'Jakie dofinansowania są dostępne?',
                'Ile kosztuje audyt?',
                'Jak długo trwa audyt?',
            ],
        };
    }

    /**
     * Wysyła wiadomość do Claude i zapisuje w bazie.
     * Zwraca odpowiedź asystenta.
     */
    public function chat(AiConversation $conversation, string $userMessage): string
    {
        // Zapisz wiadomość użytkownika
        $conversation->messages()->create([
            'role'    => 'user',
            'content' => $userMessage,
        ]);

        // Zbuduj historię dla Prisma
        $history = $this->buildMessageHistory($conversation);

        // Wywołaj Claude przez Prism
        $response = Prism::text()
            ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
            ->withSystemPrompt($this->getSystemPrompt($conversation->context_type ?? 'general'))
            ->withMessages($history)
            ->generate();

        $assistantText = $response->text;

        // Zapisz odpowiedź asystenta
        $conversation->messages()->create([
            'role'     => 'assistant',
            'content'  => $assistantText,
            'metadata' => [
                'model'              => $response->usage->model ?? 'claude-haiku-4-5-20251001',
                'input_tokens'       => $response->usage->promptTokens ?? null,
                'output_tokens'      => $response->usage->completionTokens ?? null,
            ],
        ]);

        return $assistantText;
    }

    /**
     * Tworzy nową konwersację i wysyła pierwsze powitanie od asystenta.
     */
    public function startConversation(
        int $userId,
        string $contextType = 'general',
        ?int $contextId = null,
        ?string $title = null
    ): AiConversation {
        $conversation = AiConversation::create([
            'user_id'      => $userId,
            'context_type' => $contextType,
            'context_id'   => $contextId,
            'title'        => $title ?? $this->defaultTitle($contextType),
            'status'       => 'active',
        ]);

        // Pierwsze przywitanie
        $greeting = Prism::text()
            ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
            ->withSystemPrompt($this->getSystemPrompt($contextType))
            ->withPrompt('Przywitaj się z klientem i wyjaśnij krótko co razem zrobimy. Zadaj pierwsze pytanie żeby zacząć zbieranie danych.')
            ->generate();

        $conversation->messages()->create([
            'role'    => 'assistant',
            'content' => $greeting->text,
        ]);

        return $conversation;
    }

    /**
     * Analizuje dane audytu i zwraca podsumowanie / rekomendacje.
     */
    public function analyzeAuditData(array $auditData, string $auditType = 'energy_audit'): string
    {
        $dataJson = json_encode($auditData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $response = Prism::text()
            ->using(Provider::Anthropic, 'claude-sonnet-4-6')
            ->withSystemPrompt($this->getSystemPrompt($auditType))
            ->withPrompt(
                "Na podstawie poniższych danych audytu przeprowadź analizę i przygotuj:\n" .
                "1. Podsumowanie obecnego stanu energetycznego\n" .
                "2. Zidentyfikowane obszary do poprawy\n" .
                "3. Konkretne rekomendacje z szacowanymi oszczędnościami\n" .
                "4. Priorytety działań\n\n" .
                "Dane audytu:\n```json\n{$dataJson}\n```"
            )
            ->generate();

        return $response->text;
    }

    /**
     * Generuje ustrukturyzowany protokół z rozmowy — wyciąga konkretne dane
     * zebrane podczas audytu i zapisuje je w bazie.
     * Zwraca tablicę z danymi protokołu.
     */
    public function generateProtocol(AiConversation $conversation): array
    {
        // Zbuduj pełną historię rozmowy jako plain text
        $history = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => ($m->role === 'user' ? 'Klient' : 'Asystent') . ': ' . $m->content)
            ->join("\n\n");

        $contextType = $conversation->context_type ?? 'general';

        $extractionPrompt = match ($contextType) {
            'energy_audit' => <<<PROMPT
Na podstawie poniższej rozmowy wyciągnij TYLKO dane faktyczne podane przez klienta.
Zwróć odpowiedź WYŁĄCZNIE jako JSON (bez opisu, bez markdown, bez ```json) w formacie:
{
  "sekcje": [
    {
      "nazwa": "Identyfikacja obiektu",
      "pola": [
        {"klucz": "Lokalizacja", "wartosc": "..."},
        {"klucz": "Rodzaj obiektu", "wartosc": "..."},
        {"klucz": "Powierzchnia użytkowa", "wartosc": "..."},
        {"klucz": "Zakres audytu", "wartosc": "..."},
        {"klucz": "Rok budowy/modernizacji", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Zużycie energii",
      "pola": [
        {"klucz": "Nośniki energii", "wartosc": "..."},
        {"klucz": "Zużycie energii elektrycznej", "wartosc": "..."},
        {"klucz": "Zużycie gazu", "wartosc": "..."},
        {"klucz": "Inne nośniki", "wartosc": "..."},
        {"klucz": "Łączny koszt energii/rok", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Ogrzewanie i chłodzenie",
      "pola": [
        {"klucz": "Typ ogrzewania", "wartosc": "..."},
        {"klucz": "Wiek kotła/urządzenia", "wartosc": "..."},
        {"klucz": "Klimatyzacja/chłodzenie", "wartosc": "..."},
        {"klucz": "Temperatura w sezonie", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Oświetlenie",
      "pola": [
        {"klucz": "Rodzaj oświetlenia", "wartosc": "..."},
        {"klucz": "Godziny pracy/dobę", "wartosc": "..."},
        {"klucz": "Automatyka oświetlenia", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Urządzenia i procesy",
      "pola": [
        {"klucz": "Główne urządzenia energochłonne", "wartosc": "..."},
        {"klucz": "Odbiorniki pracujące non-stop", "wartosc": "..."},
        {"klucz": "Procesy technologiczne", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Dotychczasowe działania",
      "pola": [
        {"klucz": "Poprzednie audyty/modernizacje", "wartosc": "..."},
        {"klucz": "Certyfikat energetyczny", "wartosc": "..."},
        {"klucz": "Plany klienta", "wartosc": "..."}
      ]
    }
  ],
  "uwagi": "Ewentualne ważne uwagi z rozmowy, które nie pasują do sekcji powyżej"
}
Jeśli klient nie podał wartości dla danego pola, wpisz "Nie podano".
Nie wymyślaj danych — tylko to co klient dosłownie powiedział.
PROMPT,
            'iso50001' => <<<PROMPT
Na podstawie poniższej rozmowy wyciągnij TYLKO dane faktyczne podane przez klienta.
Zwróć odpowiedź WYŁĄCZNIE jako JSON (bez opisu, bez markdown, bez ```json) w formacie:
{
  "sekcje": [
    {
      "nazwa": "Kontekst organizacji",
      "pola": [
        {"klucz": "Branża i profil działalności", "wartosc": "..."},
        {"klucz": "Liczba pracowników", "wartosc": "..."},
        {"klucz": "Lokalizacje objęte systemem", "wartosc": "..."},
        {"klucz": "Inne certyfikaty ISO", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Polityka energetyczna",
      "pola": [
        {"klucz": "Czy istnieje polityka energetyczna", "wartosc": "..."},
        {"klucz": "Główne cele polityki", "wartosc": "..."},
        {"klucz": "Pełnomocnik ds. energii", "wartosc": "..."},
        {"klucz": "Zatwierdzenie przez zarząd", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Przegląd energetyczny",
      "pola": [
        {"klucz": "Nośniki energii i zużycie", "wartosc": "..."},
        {"klucz": "Główne obszary zużycia (SUE)", "wartosc": "..."},
        {"klucz": "System monitoringu energii", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Cele i plany działań",
      "pola": [
        {"klucz": "Cele redukcji zużycia energii", "wartosc": "..."},
        {"klucz": "Planowane działania", "wartosc": "..."},
        {"klucz": "Budżet na modernizacje", "wartosc": "..."}
      ]
    },
    {
      "nazwa": "Wskaźniki EnPI",
      "pola": [
        {"klucz": "Stosowane wskaźniki energetyczne", "wartosc": "..."},
        {"klucz": "Rok bazowy (linia bazowa)", "wartosc": "..."}
      ]
    }
  ],
  "uwagi": "Ewentualne ważne uwagi z rozmowy"
}
Jeśli klient nie podał wartości dla danego pola, wpisz "Nie podano".
PROMPT,
            default => <<<PROMPT
Na podstawie poniższej rozmowy wyciągnij TYLKO dane faktyczne podane przez klienta.
Zwróć odpowiedź WYŁĄCZNIE jako JSON (bez opisu, bez markdown, bez ```json) w formacie:
{
  "sekcje": [
    {
      "nazwa": "Zebrane dane",
      "pola": [
        {"klucz": "Temat", "wartosc": "Krótki opis"},
        {"klucz": "Dane klienta", "wartosc": "..."}
      ]
    }
  ],
  "uwagi": "Kluczowe wnioski z rozmowy"
}
PROMPT,
        };

        $response = Prism::text()
            ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
            ->withSystemPrompt('Jesteś ekstraktor danych. Zwracasz TYLKO czysty JSON bez żadnych komentarzy, opisu ani markdown.')
            ->withPrompt($extractionPrompt . "\n\n---\nTREŚĆ ROZMOWY:\n\n" . $history)
            ->generate();

        // Parsuj JSON z odpowiedzi
        $raw = trim($response->text);
        // Usuń ewentualne markdown code fences
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);

        $protocol = json_decode($raw, true);

        if (! is_array($protocol)) {
            // Fallback — jeśli AI zwróciło błędny JSON
            $protocol = [
                'sekcje' => [['nazwa' => 'Surowa odpowiedź', 'pola' => [['klucz' => 'Treść', 'wartosc' => $raw]]]],
                'uwagi'  => 'Błąd parsowania struktury — dane surowe powyżej',
            ];
        }

        // Zapisz w bazie
        $conversation->update([
            'protocol_data'         => $protocol,
            'protocol_generated_at' => now(),
        ]);

        return $protocol;
    }

    private function buildMessageHistory(AiConversation $conversation): array
    {
        $messages = [];

        foreach ($conversation->messages()->orderBy('created_at')->get() as $msg) {
            $messages[] = match ($msg->role) {
                'user'      => new UserMessage($msg->content),
                'assistant' => new AssistantMessage($msg->content),
                default     => null,
            };
        }

        return array_filter($messages);
    }

    private function defaultTitle(string $contextType): string
    {
        return match ($contextType) {
            'energy_audit' => 'Audyt energetyczny — ' . now()->format('d.m.Y'),
            'iso50001'     => 'ISO 50001 — ' . now()->format('d.m.Y'),
            'offer'        => 'Oferta — ' . now()->format('d.m.Y'),
            default        => 'Rozmowa — ' . now()->format('d.m.Y H:i'),
        };
    }
}
