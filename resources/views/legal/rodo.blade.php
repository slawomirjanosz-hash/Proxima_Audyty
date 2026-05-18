<x-layouts.app>
    <style>
        .legal-wrap {
            max-width: 860px;
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 16px;
            padding: 40px 48px 52px;
            box-shadow: 0 4px 18px rgba(26,77,58,.07);
        }
        .legal-wrap h1 {
            font-family: var(--serif);
            font-size: 28px;
            color: var(--green-deep);
            margin: 0 0 6px;
        }
        .legal-wrap .legal-meta {
            font-size: 12px;
            color: var(--ink-mute);
            margin: 0 0 32px;
        }
        .legal-wrap h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            margin: 28px 0 8px;
            border-left: 3px solid var(--green-primary);
            padding-left: 10px;
        }
        .legal-wrap p, .legal-wrap li {
            font-size: 14px;
            color: var(--ink-soft);
            line-height: 1.75;
        }
        .legal-wrap ul { padding-left: 22px; margin: 8px 0; }
        .legal-wrap li { margin-bottom: 4px; }
        .legal-wrap .highlight-box {
            background: #f0f7f4;
            border: 1px solid var(--green-light);
            border-radius: 10px;
            padding: 14px 18px;
            margin: 16px 0;
            font-size: 14px;
            color: var(--green-deep);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--green-primary);
            text-decoration: none;
            margin-bottom: 20px;
        }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 700px) { .legal-wrap { padding: 24px 18px 32px; } }
    </style>

    <div style="max-width:860px; margin:0 auto; padding: 24px 16px;">
        <a href="{{ route('register.form') }}" class="back-link">← Wróć do rejestracji</a>

        <div class="legal-wrap">
            <h1>Polityka prywatności i klauzula RODO</h1>
            <p class="legal-meta">Obowiązuje od: 1 stycznia 2024 r. &nbsp;|&nbsp; Wersja: 1.0</p>

            <div class="highlight-box">
                Administratorem Twoich danych osobowych jest <strong>ENESA Energy Audit &amp; Solutions sp. z o.o.</strong>
                z siedzibą w Warszawie. Dane przetwarzamy wyłącznie w celach związanych ze świadczeniem usług
                audytu energetycznego i obsługą klientów platformy ENESA.
            </div>

            <h2>§ 1. Administrator danych</h2>
            <p>
                Administratorem danych osobowych jest <strong>ENESA Energy Audit &amp; Solutions sp. z o.o.</strong>,
                ul. Energetyczna 1, 00-000 Warszawa, NIP: 0000000000, e-mail: <strong>rodo@enesa.pl</strong>.
            </p>

            <h2>§ 2. Zakres zbieranych danych</h2>
            <p>W procesie rejestracji firmy oraz korzystania z platformy przetwarzamy:</p>
            <ul>
                <li>dane identyfikacyjne firmy: nazwa, NIP, REGON, adres siedziby,</li>
                <li>dane osoby kontaktowej: imię, nazwisko, adres e-mail, numer telefonu,</li>
                <li>dane logowania: adres e-mail, zaszyfrowane hasło,</li>
                <li>dane techniczne: adres IP, typ przeglądarki, czas dostępu (logi systemowe),</li>
                <li>dane zawarte w formularzach audytu energetycznego przekazanych dobrowolnie przez klienta.</li>
            </ul>

            <h2>§ 3. Cele i podstawy prawne przetwarzania</h2>
            <ul>
                <li><strong>Realizacja umowy</strong> (art. 6 ust. 1 lit. b RODO) — obsługa rejestracji, prowadzenie audytów, kontakt z klientem, wystawianie ofert i dokumentacji,</li>
                <li><strong>Obowiązek prawny</strong> (art. 6 ust. 1 lit. c RODO) — przechowywanie dokumentacji na potrzeby przepisów podatkowych i rachunkowych,</li>
                <li><strong>Uzasadniony interes administratora</strong> (art. 6 ust. 1 lit. f RODO) — bezpieczeństwo systemu, zapobieganie nadużyciom, statystyki użytkowania,</li>
                <li><strong>Zgoda</strong> (art. 6 ust. 1 lit. a RODO) — wysyłka informacji handlowych i marketingowych (jeżeli wyrażono odrębną zgodę).</li>
            </ul>

            <h2>§ 4. Okres przechowywania danych</h2>
            <p>Dane przechowujemy przez okres:</p>
            <ul>
                <li>trwania umowy/relacji handlowej, a następnie przez wymagany przepisami prawa okres przedawnienia roszczeń (do 6 lat),</li>
                <li>dane rachunkowe — przez 5 lat od końca roku podatkowego, w którym powstał obowiązek podatkowy,</li>
                <li>logi systemowe — do 12 miesięcy,</li>
                <li>dane przekazane na podstawie zgody — do czasu jej cofnięcia.</li>
            </ul>

            <h2>§ 5. Odbiorcy danych</h2>
            <p>Dane mogą być przekazywane:</p>
            <ul>
                <li>dostawcom usług IT (hosting, poczta e-mail, usługi chmurowe) na podstawie umów powierzenia przetwarzania,</li>
                <li>biuru rachunkowemu i kancelarii prawnej w zakresie niezbędnym do realizacji obowiązków,</li>
                <li>organom publicznym (urzędy skarbowe, sądy) — wyłącznie na podstawie przepisów prawa.</li>
            </ul>
            <p>Dane nie są przekazywane poza Europejski Obszar Gospodarczy.</p>

            <h2>§ 6. Prawa osoby, której dane dotyczą</h2>
            <p>Przysługują Ci następujące prawa:</p>
            <ul>
                <li><strong>prawo dostępu</strong> do swoich danych (art. 15 RODO),</li>
                <li><strong>prawo do sprostowania</strong> danych (art. 16 RODO),</li>
                <li><strong>prawo do usunięcia</strong> danych („prawo do bycia zapomnianym") — art. 17 RODO,</li>
                <li><strong>prawo do ograniczenia przetwarzania</strong> (art. 18 RODO),</li>
                <li><strong>prawo do przenoszenia danych</strong> (art. 20 RODO),</li>
                <li><strong>prawo do sprzeciwu</strong> wobec przetwarzania (art. 21 RODO),</li>
                <li><strong>prawo do cofnięcia zgody</strong> w dowolnym momencie — bez wpływu na zgodność z prawem przetwarzania przed jej cofnięciem.</li>
            </ul>
            <p>
                Aby skorzystać z powyższych praw, prosimy o kontakt pod adresem: <strong>rodo@enesa.pl</strong>.
                Masz również prawo wniesienia skargi do organu nadzorczego — <strong>Prezesa Urzędu Ochrony Danych Osobowych</strong> (ul. Stawki 2, 00-193 Warszawa).
            </p>

            <h2>§ 7. Pliki cookies</h2>
            <p>
                Serwis może używać plików cookies w celu zapewnienia prawidłowego działania (cookies techniczne, niezbędne).
                Nie stosujemy cookies śledzących ani profilujących bez Twojej zgody.
            </p>

            <h2>§ 8. Bezpieczeństwo danych</h2>
            <p>
                Stosujemy odpowiednie środki techniczne i organizacyjne zapewniające bezpieczeństwo danych osobowych,
                w tym szyfrowanie transmisji (HTTPS/TLS), kontrolę dostępu oraz regularne audyty bezpieczeństwa.
            </p>

            <h2>§ 9. Zmiany polityki prywatności</h2>
            <p>
                Zastrzegamy sobie prawo do aktualizacji niniejszej polityki. O wszelkich istotnych zmianach
                użytkownicy zostaną poinformowani drogą e-mailową lub poprzez komunikat w systemie.
                Data ostatniej modyfikacji widnieje na początku dokumentu.
            </p>

            <h2>§ 10. Kontakt</h2>
            <p>
                W sprawach dotyczących ochrony danych osobowych prosimy o kontakt:<br>
                E-mail: <strong>rodo@enesa.pl</strong><br>
                Adres: ENESA Energy Audit &amp; Solutions sp. z o.o., ul. Energetyczna 1, 00-000 Warszawa
            </p>
        </div>
    </div>
</x-layouts.app>
