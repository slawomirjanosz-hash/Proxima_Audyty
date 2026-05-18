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
            <h1>Warunki korzystania z platformy ENESA</h1>
            <p class="legal-meta">Obowiązuje od: 1 stycznia 2024 r. &nbsp;|&nbsp; Wersja: 1.0</p>

            <div class="highlight-box">
                Niniejsze Warunki korzystania regulują zasady dostępu i użytkowania platformy ENESA
                przeznaczonej do zarządzania audytami energetycznymi. Przed rejestracją prosimy o uważne
                zapoznanie się z poniższymi postanowieniami.
            </div>

            <h2>§ 1. Definicje</h2>
            <ul>
                <li><strong>Platforma</strong> — system informatyczny ENESA dostępny pod adresem platformy, służący do zarządzania audytami energetycznymi,</li>
                <li><strong>Usługodawca</strong> — ENESA Energy Audit &amp; Solutions sp. z o.o. z siedzibą w Warszawie,</li>
                <li><strong>Klient</strong> — podmiot gospodarczy (firma), który zarejestrował się lub ubiega o rejestrację w systemie,</li>
                <li><strong>Użytkownik</strong> — osoba fizyczna posiadająca konto w systemie, działająca w imieniu Klienta,</li>
                <li><strong>Audyt energetyczny</strong> — usługa polegająca na analizie zużycia energii i opracowaniu rekomendacji efektywności energetycznej.</li>
            </ul>

            <h2>§ 2. Zakres usług</h2>
            <p>
                Platforma ENESA umożliwia:
            </p>
            <ul>
                <li>składanie wniosków o przeprowadzenie audytów energetycznych (energii elektrycznej, sprężonego powietrza, ciepła, oświetlenia i innych),</li>
                <li>wypełnianie i przesyłanie kwestionariuszy audytowych,</li>
                <li>dostęp do dokumentacji audytowej, raportów i wyników,</li>
                <li>komunikację z zespołem audytorów ENESA,</li>
                <li>przeglądanie ofert i akceptację warunków współpracy.</li>
            </ul>

            <h2>§ 3. Rejestracja i konto</h2>
            <ul>
                <li>Rejestracja jest bezpłatna. Aktywacja konta następuje po weryfikacji wniosku przez administratora ENESA.</li>
                <li>Dane podane w formularzu rejestracyjnym muszą być prawdziwe i aktualne.</li>
                <li>Jeden podmiot (NIP) może posiadać jedno aktywne konto. Składanie wielokrotnych wniosków z tym samym NIP jest niedozwolone.</li>
                <li>Użytkownik jest odpowiedzialny za zachowanie poufności danych logowania.</li>
                <li>Usługodawca zastrzega prawo do odmowy rejestracji bez podania przyczyny.</li>
            </ul>

            <h2>§ 4. Obowiązki Klienta</h2>
            <p>Klient zobowiązuje się do:</p>
            <ul>
                <li>podawania prawdziwych i kompletnych informacji w formularzach audytowych,</li>
                <li>umożliwienia audytorom dostępu do pomieszczeń i dokumentacji technicznej w uzgodnionych terminach,</li>
                <li>niezwłocznego informowania ENESA o zmianach danych firmy,</li>
                <li>korzystania z platformy wyłącznie zgodnie z jej przeznaczeniem i obowiązującym prawem,</li>
                <li>nieudostępniania danych logowania osobom nieuprawnionym.</li>
            </ul>

            <h2>§ 5. Prawa własności intelektualnej</h2>
            <p>
                Wszelkie materiały dostępne na platformie, w tym raporty, metodologie, szablony kwestionariuszy
                oraz oprogramowanie, stanowią własność Usługodawcy i są chronione przepisami prawa autorskiego.
                Klient otrzymuje niewyłączną, niezbywalną licencję na korzystanie z wyników audytów
                wyłącznie na potrzeby własnej działalności.
            </p>

            <h2>§ 6. Poufność</h2>
            <p>
                Obie strony zobowiązują się do zachowania w poufności wszelkich informacji pozyskanych
                w toku współpracy, oznaczonych jako poufne lub z uwagi na ich charakter uznawanych za poufne.
                Obowiązek ten nie dotyczy informacji powszechnie znanych lub wymaganych do ujawnienia przez prawo.
            </p>

            <h2>§ 7. Ograniczenie odpowiedzialności</h2>
            <p>
                Usługodawca nie ponosi odpowiedzialności za:
            </p>
            <ul>
                <li>decyzje biznesowe Klienta podjęte na podstawie wyników audytów,</li>
                <li>przerwy w dostępie do platformy spowodowane awariami infrastruktury zewnętrznej lub pracami konserwacyjnymi (planowanymi z 24-godzinnym wyprzedzeniem),</li>
                <li>szkody wynikające z podania przez Klienta nieprawdziwych lub niepełnych danych,</li>
                <li>utratę danych spowodowaną siłą wyższą.</li>
            </ul>

            <h2>§ 8. Zawieszenie i usunięcie konta</h2>
            <p>
                Usługodawca może zawiesić lub usunąć konto Klienta w przypadku:
            </p>
            <ul>
                <li>naruszenia postanowień niniejszych Warunków,</li>
                <li>podania fałszywych danych rejestracyjnych,</li>
                <li>działania na szkodę innych użytkowników lub Usługodawcy,</li>
                <li>braku aktywności konta przez okres dłuższy niż 24 miesiące.</li>
            </ul>
            <p>
                Klient może w każdej chwili złożyć wniosek o usunięcie konta kontaktując się pod adresem
                <strong>kontakt@enesa.pl</strong>.
            </p>

            <h2>§ 9. Zmiany Warunków</h2>
            <p>
                Usługodawca zastrzega prawo do zmiany niniejszych Warunków. O zmianach Klienci zostaną
                poinformowani drogą e-mailową z co najmniej 14-dniowym wyprzedzeniem. Dalsze korzystanie
                z platformy po tym terminie oznacza akceptację nowych Warunków.
            </p>

            <h2>§ 10. Prawo właściwe i rozstrzyganie sporów</h2>
            <p>
                Niniejsze Warunki podlegają prawu polskiemu. Wszelkie spory wynikające ze stosowania
                niniejszych Warunków będą rozstrzygane przez sąd właściwy dla siedziby Usługodawcy.
                Przed skierowaniem sprawy na drogę sądową strony zobowiązują się podjąć próbę polubownego
                rozwiązania sporu.
            </p>

            <h2>§ 11. Kontakt</h2>
            <p>
                W sprawach dotyczących niniejszych Warunków prosimy o kontakt:<br>
                E-mail: <strong>kontakt@enesa.pl</strong><br>
                Adres: ENESA Energy Audit &amp; Solutions sp. z o.o., ul. Energetyczna 1, 00-000 Warszawa
            </p>
        </div>
    </div>
</x-layouts.app>
