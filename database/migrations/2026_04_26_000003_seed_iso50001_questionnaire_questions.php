<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Iso50001QuestionnaireQuestion;

return new class extends Migration
{
    public function up(): void
    {
        $questions = [
            // BLOK A
            ['block_key' => 'A', 'question_code' => 'A1', 'question_text' => 'Pełna nazwa firmy i forma prawna (Sp. z o.o., S.A. itp.)', 'answer_hint' => null, 'sort_order' => 10],
            ['block_key' => 'A', 'question_code' => 'A2', 'question_text' => 'NIP / KRS', 'answer_hint' => null, 'sort_order' => 20],
            ['block_key' => 'A', 'question_code' => 'A3', 'question_text' => 'Adres siedziby oraz adres(-y) zakładu(-ów) produkcyjnych', 'answer_hint' => null, 'sort_order' => 30],
            ['block_key' => 'A', 'question_code' => 'A4', 'question_text' => 'Osoba kontaktowa: imię, nazwisko, stanowisko, e-mail, telefon', 'answer_hint' => null, 'sort_order' => 40],
            ['block_key' => 'A', 'question_code' => 'A5', 'question_text' => 'Przybliżona liczba zatrudnionych (etaty)', 'answer_hint' => null, 'sort_order' => 50],
            ['block_key' => 'A', 'question_code' => 'A6', 'question_text' => 'Przybliżone przychody roczne (przedział: do 10 mln / 10–50 mln / 50–200 mln / >200 mln PLN)', 'answer_hint' => 'do 10 mln / 10–50 mln / 50–200 mln / >200 mln PLN', 'sort_order' => 60],
            ['block_key' => 'A', 'question_code' => 'A7', 'question_text' => 'Branża / główna działalność produkcyjna', 'answer_hint' => null, 'sort_order' => 70],
            // BLOK B
            ['block_key' => 'B', 'question_code' => 'B1', 'question_text' => 'Ile wynosi roczne zużycie energii elektrycznej? (GWh/rok lub kWh/rok)', 'answer_hint' => 'GWh/rok lub kWh/rok', 'sort_order' => 80],
            ['block_key' => 'B', 'question_code' => 'B2', 'question_text' => 'Ile wynosi roczne zużycie gazu ziemnego / LNG / LPG? (GWh lub tys. m³)', 'answer_hint' => 'GWh lub tys. m³', 'sort_order' => 90],
            ['block_key' => 'B', 'question_code' => 'B3', 'question_text' => 'Czy zakład zużywa parę technologiczną, ciepło sieciowe lub inne nośniki? Jakie?', 'answer_hint' => null, 'sort_order' => 100],
            ['block_key' => 'B', 'question_code' => 'B4', 'question_text' => 'Jaki jest łączny roczny rachunek za energię (wszystkie nośniki)? (PLN/rok, szacunkowo)', 'answer_hint' => 'PLN/rok, szacunkowo', 'sort_order' => 110],
            ['block_key' => 'B', 'question_code' => 'B5', 'question_text' => 'Czy energia stanowi ponad 5% kosztów operacyjnych?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 120],
            ['block_key' => 'B', 'question_code' => 'B6', 'question_text' => 'Czy zakład posiada własne źródła energii (PV, CHP, kogeneracja)?', 'answer_hint' => 'TAK / NIE / W PLANACH', 'sort_order' => 130],
            // BLOK C
            ['block_key' => 'C', 'question_code' => 'C1', 'question_text' => 'Ile lokalizacji / zakładów ma objąć system ISO 50001?', 'answer_hint' => null, 'sort_order' => 140],
            ['block_key' => 'C', 'question_code' => 'C2', 'question_text' => 'Podaj adresy wszystkich lokalizacji (lub główna + liczba pozostałych)', 'answer_hint' => null, 'sort_order' => 150],
            ['block_key' => 'C', 'question_code' => 'C3', 'question_text' => 'Ile budynków / hal produkcyjnych / obiektów łącznie?', 'answer_hint' => null, 'sort_order' => 160],
            ['block_key' => 'C', 'question_code' => 'C4', 'question_text' => 'Odległość od Gliwic do głównej lokalizacji (km lub miasto)?', 'answer_hint' => 'km lub nazwa miasta', 'sort_order' => 170],
            // BLOK D
            ['block_key' => 'D', 'question_code' => 'D1', 'question_text' => 'Czy firma posiada certyfikat ISO 9001?', 'answer_hint' => 'TAK / NIE / W TRAKCIE', 'sort_order' => 180],
            ['block_key' => 'D', 'question_code' => 'D2', 'question_text' => 'Czy firma posiada certyfikat ISO 14001?', 'answer_hint' => 'TAK / NIE / W TRAKCIE', 'sort_order' => 190],
            ['block_key' => 'D', 'question_code' => 'D3', 'question_text' => 'Czy firma posiada certyfikat ISO 45001 lub inny system zarządzania?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 200],
            ['block_key' => 'D', 'question_code' => 'D4', 'question_text' => 'Czy w ciągu ostatnich 3 lat był wykonany audyt energetyczny wg PN-EN 16247?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 210],
            ['block_key' => 'D', 'question_code' => 'D5', 'question_text' => 'Czy zakład posiada podliczniki energii (elektrycznej, gazowej)?', 'answer_hint' => 'TAK / NIE / CZĘŚCIOWO', 'sort_order' => 220],
            ['block_key' => 'D', 'question_code' => 'D6', 'question_text' => 'Czy istnieje dokumentacja techniczna głównych odbiorników energii?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 230],
            ['block_key' => 'D', 'question_code' => 'D7', 'question_text' => 'Czy prowadzone są jakiekolwiek rejestry zużycia energii (Excel, SCADA, inny system)?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 240],
            // BLOK E
            ['block_key' => 'E', 'question_code' => 'E1', 'question_text' => 'Czy firma wyznaczy pełnomocnika ds. EnMS dedykowanego do projektu?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 250],
            ['block_key' => 'E', 'question_code' => 'E2', 'question_text' => 'Ile godzin tygodniowo pełnomocnik będzie mógł poświęcić projektowi?', 'answer_hint' => null, 'sort_order' => 260],
            ['block_key' => 'E', 'question_code' => 'E3', 'question_text' => 'Czy firma posiada własny dział utrzymania ruchu lub energy managera?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 270],
            ['block_key' => 'E', 'question_code' => 'E4', 'question_text' => 'Jaka jest preferowana formuła? FULL (ENESA robi wszystko) / ASSISTED (klient angażuje własny zespół)', 'answer_hint' => 'FULL / ASSISTED', 'sort_order' => 280],
            ['block_key' => 'E', 'question_code' => 'E5', 'question_text' => 'Do kiedy firma planuje uzyskać certyfikat? (miesiąc/rok)', 'answer_hint' => 'np. 03/2026', 'sort_order' => 290],
            ['block_key' => 'E', 'question_code' => 'E6', 'question_text' => 'Czy jest wyznaczony budżet na projekt? (orientacyjnie, opcjonalnie)', 'answer_hint' => 'PLN, opcjonalnie', 'sort_order' => 300],
            // BLOK F
            ['block_key' => 'F', 'question_code' => 'F1', 'question_text' => 'Czy zakład korzysta z energii elektrycznej jako głównego nośnika?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 310],
            ['block_key' => 'F', 'question_code' => 'F2', 'question_text' => 'Czy zakład korzysta z gazu ziemnego / LNG / LPG do procesów?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 320],
            ['block_key' => 'F', 'question_code' => 'F3', 'question_text' => 'Czy jest para technologiczna lub sieć ciepłownicza?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 330],
            ['block_key' => 'F', 'question_code' => 'F4', 'question_text' => 'Czy jest instalacja sprężonego powietrza?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 340],
            ['block_key' => 'F', 'question_code' => 'F5', 'question_text' => 'Czy jest chłód technologiczny (chillery, agregaty)?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 350],
            ['block_key' => 'F', 'question_code' => 'F6', 'question_text' => 'Czy zakład posiada własną kogenerację CHP?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 360],
            ['block_key' => 'F', 'question_code' => 'F7', 'question_text' => 'Czy są procesy wymagające intensywnego pomiaru energii (piece, suszarnie, lakiernie)?', 'answer_hint' => 'TAK / NIE', 'sort_order' => 370],
            ['block_key' => 'F', 'question_code' => 'F8', 'question_text' => 'Proszę wymienić 3 największe odbiorniki energii w zakładzie (np. kompresor 250 kW, piec 500 kW)', 'answer_hint' => 'np. kompresor 250 kW, piec 500 kW', 'sort_order' => 380],
        ];

        foreach ($questions as $q) {
            Iso50001QuestionnaireQuestion::create(array_merge($q, ['is_active' => true]));
        }
    }

    public function down(): void
    {
        \App\Models\Iso50001QuestionnaireQuestion::query()->delete();
    }
};
