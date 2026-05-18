<x-layouts.app>
    <style>
        .oferta-hero {
            background: linear-gradient(130deg, var(--green-deep) 0%, #2d7a50 100%);
            border-radius: 16px;
            padding: 48px 52px 44px;
            color: var(--paper);
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
        }
        .oferta-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 260px; height: 260px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .oferta-hero h1 {
            font-family: var(--serif);
            font-size: 34px;
            font-weight: 700;
            color: var(--paper);
            margin: 0 0 10px;
        }
        .oferta-hero p {
            font-size: 16px;
            color: rgba(245,239,224,0.82);
            max-width: 640px;
            line-height: 1.7;
            margin: 0 0 28px;
        }
        .oferta-hero .hero-cta {
            display: inline-block;
            background: var(--gold);
            color: #1a1a0a;
            font-weight: 800;
            font-size: 15px;
            padding: 13px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s, transform 0.1s;
        }
        .oferta-hero .hero-cta:hover {
            background: var(--gold-light);
            transform: translateY(-1px);
        }
        .oferta-hero .hero-sub {
            display: inline-block;
            margin-left: 16px;
            font-size: 13px;
            color: rgba(245,239,224,0.65);
        }

        /* Sekcja kart usług */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }
        .service-card {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 14px;
            padding: 28px 26px 24px;
            box-shadow: 0 3px 12px rgba(26,77,58,0.06);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: box-shadow 0.15s, transform 0.15s;
        }
        .service-card:hover {
            box-shadow: 0 6px 24px rgba(26,77,58,0.12);
            transform: translateY(-2px);
        }
        .service-card .sc-icon {
            font-size: 32px;
            line-height: 1;
        }
        .service-card h3 {
            margin: 0;
            font-family: var(--serif);
            font-size: 18px;
            color: var(--green-deep);
        }
        .service-card p {
            margin: 0;
            font-size: 13.5px;
            color: var(--ink-mute);
            line-height: 1.65;
            flex: 1;
        }
        .service-card .sc-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            background: var(--green-bg);
            color: var(--green-primary);
            border: 1px solid var(--green-light);
            border-radius: 20px;
            padding: 3px 10px;
            align-self: flex-start;
        }

        /* Sekcja korzyści */
        .benefits-section {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 16px;
            padding: 36px 40px;
            margin-bottom: 36px;
        }
        .benefits-section h2 {
            font-family: var(--serif);
            font-size: 22px;
            color: var(--green-deep);
            margin: 0 0 24px;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 18px;
        }
        .benefit-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .benefit-item .bi-icon {
            font-size: 22px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .benefit-item .bi-text strong {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 3px;
        }
        .benefit-item .bi-text span {
            font-size: 12.5px;
            color: var(--ink-mute);
            line-height: 1.55;
        }

        /* Sekcja procesu */
        .process-section {
            margin-bottom: 36px;
        }
        .process-section h2 {
            font-family: var(--serif);
            font-size: 22px;
            color: var(--green-deep);
            margin: 0 0 24px;
        }
        .process-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .process-step {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding-bottom: 24px;
            position: relative;
        }
        .process-step::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 42px;
            bottom: 0;
            width: 2px;
            background: var(--green-light);
        }
        .process-step:last-child::before { display: none; }
        .ps-num {
            flex-shrink: 0;
            width: 40px; height: 40px;
            background: var(--green-primary);
            color: var(--paper);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 15px;
            font-family: var(--mono);
        }
        .ps-body strong {
            display: block;
            font-size: 14.5px;
            color: var(--ink);
            margin-bottom: 4px;
            margin-top: 8px;
        }
        .ps-body p {
            margin: 0;
            font-size: 13px;
            color: var(--ink-mute);
            line-height: 1.6;
        }

        /* Sekcja CTA dolna */
        .cta-bottom {
            background: linear-gradient(130deg, #f0f7f4, #e8f3ee);
            border: 1px solid var(--green-light);
            border-radius: 16px;
            padding: 36px 40px;
            text-align: center;
        }
        .cta-bottom h2 {
            font-family: var(--serif);
            font-size: 24px;
            color: var(--green-deep);
            margin: 0 0 10px;
        }
        .cta-bottom p {
            font-size: 14px;
            color: var(--ink-mute);
            margin: 0 0 24px;
        }
        .cta-bottom a.btn-primary {
            display: inline-block;
            background: var(--green-primary);
            color: var(--paper);
            font-weight: 800;
            font-size: 15px;
            padding: 13px 32px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s;
            margin: 0 8px;
        }
        .cta-bottom a.btn-primary:hover { background: var(--green-deep); }
        .cta-bottom a.btn-outline {
            display: inline-block;
            border: 2px solid var(--green-primary);
            color: var(--green-primary);
            font-weight: 700;
            font-size: 14px;
            padding: 11px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s;
            margin: 0 8px;
        }
        .cta-bottom a.btn-outline:hover { background: var(--green-primary); color: var(--paper); }

        @media (max-width: 700px) {
            .oferta-hero { padding: 28px 20px 28px; }
            .oferta-hero h1 { font-size: 24px; }
            .benefits-section { padding: 24px 18px; }
            .cta-bottom { padding: 24px 18px; }
        }
    </style>

    <div style="max-width:1060px; margin:0 auto; padding: 24px 16px 48px;">

        {{-- Hero --}}
        <div class="oferta-hero">
            <h1>Audyty energetyczne dla Twojej firmy</h1>
            <p>
                Pomagamy przedsiębiorstwom obniżyć koszty energii, spełnić wymogi prawne i uzyskać
                białe certyfikaty. Nasze audyty energetyczne to profesjonalna analiza przeprowadzona
                przez certyfikowanych audytorów ENESA — szybko, rzetelnie i z pełnym wsparciem.
            </p>
            <a href="{{ route('register.form') }}" class="hero-cta">Zarejestruj firmę i zacznij →</a>
            <span class="hero-sub">Rejestracja bezpłatna · Bez zobowiązań</span>
        </div>

        {{-- Usługi --}}
        <h2 style="font-family:var(--serif); font-size:22px; color:var(--green-deep); margin:0 0 20px;">Nasze usługi audytowe</h2>
        <div class="services-grid">

            <div class="service-card">
                <div class="sc-icon">⚡</div>
                <h3>Audyt energetyczny budynku / zakładu</h3>
                <p>
                    Kompleksowa analiza zużycia energii elektrycznej, cieplnej i paliw w obiektach przemysłowych,
                    biurowych i produkcyjnych. Identyfikujemy obszary strat energii i przygotowujemy plan
                    modernizacji z kalkulacją zwrotu inwestycji.
                </p>
                <span class="sc-tag">Obowiązkowy dla dużych firm</span>
            </div>

            <div class="service-card">
                <div class="sc-icon">🔧</div>
                <h3>Audyt sprężonego powietrza</h3>
                <p>
                    Szczegółowa analiza układów sprężarkowych — od poboru mocy, przez straty na wyciekach,
                    po optymalizację ciśnienia roboczego. Typowe oszczędności po wdrożeniu zaleceń
                    wynoszą 15–30% kosztów sprężarkowni.
                </p>
                <span class="sc-tag">Przemysł & produkcja</span>
            </div>

            <div class="service-card">
                <div class="sc-icon">🔥</div>
                <h3>Audyt kotłowni i ciepłowni</h3>
                <p>
                    Ocena efektywności układów grzewczych, kotłów, wymienników i sieci ciepłowniczych.
                    Weryfikacja sprawności instalacji, jakości spalania oraz możliwości automatyzacji
                    i modernizacji źródeł ciepła.
                </p>
                <span class="sc-tag">Ogrzewanie & technologia</span>
            </div>

            <div class="service-card">
                <div class="sc-icon">💡</div>
                <h3>Audyt oświetlenia</h3>
                <p>
                    Inwentaryzacja i analiza opraw oświetleniowych, sterowania i harmonogramów pracy.
                    Projekt wymiany na technologię LED z doborem systemu DALI/PIR i wykazem
                    oszczędności energetycznych i finansowych.
                </p>
                <span class="sc-tag">LED & automatyka</span>
            </div>

            <div class="service-card">
                <div class="sc-icon">❄️</div>
                <h3>Audyt układów chłodzenia</h3>
                <p>
                    Analiza instalacji chłodniczych, klimatyzacji precyzyjnej i central klimatyzacyjnych.
                    Ocena COP/EER, szczelności układów czynnika chłodniczego, pracy w trybach
                    ekonomicznych i możliwości odzysku ciepła.
                </p>
                <span class="sc-tag">Chłodnictwo & HVAC</span>
            </div>

            <div class="service-card">
                <div class="sc-icon">🌿</div>
                <h3>ISO 50001 i białe certyfikaty</h3>
                <p>
                    Wsparcie we wdrożeniu systemu zarządzania energią zgodnego z normą ISO 50001.
                    Przygotowanie wniosków o białe certyfikaty (świadectwa efektywności energetycznej)
                    i nadzór nad procesem uzyskiwania oszczędności.
                </p>
                <span class="sc-tag">Certyfikacja & regulacje</span>
            </div>

        </div>

        {{-- Korzyści --}}
        <div class="benefits-section">
            <h2>Dlaczego warto wybrać ENESA?</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="bi-icon">🏅</div>
                    <div class="bi-text">
                        <strong>Certyfikowani audytorzy</strong>
                        <span>Nasi specjaliści posiadają uprawnienia auditora energetycznego wpisanego do rejestru UDT.</span>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="bi-icon">📊</div>
                    <div class="bi-text">
                        <strong>Raport z konkretnymi wnioskami</strong>
                        <span>Otrzymujesz czytelny dokument z listą priorytetowych działań i kalkulacją oszczędności.</span>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="bi-icon">⏱️</div>
                    <div class="bi-text">
                        <strong>Realizacja w terminie</strong>
                        <span>Dotrzymujemy umówionych harmonogramów — audyt standardowy w 4–6 tygodnie od wizyty.</span>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="bi-icon">🔒</div>
                    <div class="bi-text">
                        <strong>Pełna poufność danych</strong>
                        <span>Wszelkie dane techniczne i finansowe Twojej firmy są chronione umową NDA.</span>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="bi-icon">💬</div>
                    <div class="bi-text">
                        <strong>Wsparcie po audycie</strong>
                        <span>Służymy pomocą przy interpretacji wyników i wyborze firm wykonawczych do modernizacji.</span>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="bi-icon">📋</div>
                    <div class="bi-text">
                        <strong>Zgodność z przepisami</strong>
                        <span>Audyt spełnia wymogi ustawy o efektywności energetycznej i jest akceptowany przez UDT/URE.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jak to działa --}}
        <div class="process-section">
            <h2>Jak wygląda współpraca?</h2>
            <div class="process-steps">
                <div class="process-step">
                    <div class="ps-num">1</div>
                    <div class="ps-body">
                        <strong>Rejestracja i wstępna konsultacja</strong>
                        <p>Zakładasz konto firmy w systemie ENESA. Nasz zespół kontaktuje się z Tobą w ciągu 1 dnia roboczego, aby omówić zakres i specyfikę audytu.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="ps-num">2</div>
                    <div class="ps-body">
                        <strong>Wypełnienie kwestionariusza</strong>
                        <p>Przez platformę ENESA wypełniasz szczegółowy kwestionariusz dotyczący instalacji i zużycia energii. System prowadzi Cię krok po kroku.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="ps-num">3</div>
                    <div class="ps-body">
                        <strong>Wizyta audytora w obiekcie</strong>
                        <p>Certyfikowany audytor ENESA przyjeżdża do Twojego zakładu, przeprowadza pomiary i inwentaryzację techniczną.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="ps-num">4</div>
                    <div class="ps-body">
                        <strong>Opracowanie raportu</strong>
                        <p>Na podstawie pomiarów i danych z kwestionariusza przygotowujemy szczegółowy raport z analizą i rekomendacjami modernizacyjnymi.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="ps-num">5</div>
                    <div class="ps-body">
                        <strong>Prezentacja wyników i wsparcie</strong>
                        <p>Omawiamy wyniki audytu z kluczowymi osobami w Twojej firmie. Pomagamy w przygotowaniu wniosków o białe certyfikaty i finansowanie modernizacji.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA dolne --}}
        <div class="cta-bottom">
            <h2>Gotowy na pierwszą rozmowę?</h2>
            <p>Zarejestruj firmę bezpłatnie i dowiedz się, ile możesz zaoszczędzić na kosztach energii.</p>
            <a href="{{ route('register.form') }}" class="btn-primary">Zarejestruj firmę →</a>
            <a href="mailto:kontakt@enesa.pl" class="btn-outline">Napisz do nas</a>
        </div>

    </div>
</x-layouts.app>
