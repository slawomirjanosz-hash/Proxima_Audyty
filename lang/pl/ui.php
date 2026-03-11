<?php

return [
    'company' => 'ENESA sp. z o. o.',
    'menu' => [
        'home' => 'Strona główna',
        'dashboard' => 'Dashboard',
        'offer' => 'Oferta',
        'audits' => 'Audyty',
        'client_zone' => 'Strefa klienta',
        'settings' => 'Ustawienia',
    ],
    'actions' => [
        'login' => 'Logowanie',
        'logout' => 'Wyloguj',
        'close' => 'Zamknij',
        'sign_in' => 'Zaloguj',
        'remember_me' => 'Zapamietaj mnie',
    ],
    'messages' => [
        'invalid_credentials' => 'Nieprawidłowy e-mail lub hasło.',
        'logged_out' => 'Zostałeś wylogowany.',
        'user_permissions_updated' => 'Uprawnienia użytkownika zostały zaktualizowane.',
        'user_role_updated' => 'Rola użytkownika została zaktualizowana.',
        'company_assignments_updated' => 'Przypisania firmy zostały zaktualizowane.',
    ],
    'auth' => [
        'title' => 'Logowanie',
        'access_panel' => 'Panel dostępu',
        'password' => 'Hasło',
        'test_accounts' => 'Testowe konta: admin@enesa.pl, auditor@enesa.pl, client@enesa.pl (hasło: password)',
    ],
    'dashboard' => [
        'title' => 'Dashboard - aktywne audyty',
        'subtitle' => 'Lista wszystkich aktualnie prowadzonych audytów.',
        'count' => 'Aktywne audyty',
        'empty' => 'Brak aktywnych audytów.',
        'columns' => [
            'title' => 'Audyt',
            'company' => 'Firma',
            'auditor' => 'Audytor',
            'status' => 'Status',
            'updated' => 'Aktualizacja',
        ],
    ],
    'client' => [
        'tag' => 'Strefa klienta',
        'welcome' => 'Witamy ponownie',
        'description' => 'Twoja dedykowana przestrzeń ENESA — śledź przypisane firmy, aktywne oferty i trwające audyty energetyczne w czasie rzeczywistym.',
        'meta' => [
            'account' => 'Konto',
            'access_level' => 'Poziom dostępu',
            'session_date' => 'Data sesji',
        ],
        'preview_title' => 'Tryb podglądu:',
        'preview_text' => 'Przeglądasz dane Strefy klienta jako konto uprzywilejowane.',
        'stats' => [
            'companies' => 'Firmy',
            'companies_sub' => 'przypisane do Twojego konta',
            'active_offers' => 'Aktywne oferty',
            'active_offers_sub' => 'w trakcie lub oczekujące',
            'audits' => 'Audyty energetyczne',
            'audits_sub' => 'zakończone lub trwające',
        ],
        'tables' => [
            'companies' => [
                'title' => 'Twoje firmy',
                'columns' => [
                    'company_name' => 'Nazwa firmy',
                    'city' => 'Miasto',
                    'assigned_auditor' => 'Przypisany audytor',
                ],
                'empty' => 'Brak przypisanych firm.',
            ],
            'offers' => [
                'title' => 'Twoje oferty',
                'columns' => [
                    'title' => 'Tytuł',
                    'status' => 'Status',
                    'company' => 'Firma',
                ],
                'empty' => 'Brak przypisanych ofert.',
            ],
            'audits' => [
                'title' => 'Audyty energetyczne',
                'columns' => [
                    'title' => 'Tytuł',
                    'status' => 'Status',
                    'company' => 'Firma',
                    'auditor' => 'Audytor',
                ],
                'empty' => 'Brak przypisanych audytów.',
            ],
        ],
    ],
    'settings' => [
        'header' => [
            'title' => 'Ustawienia systemu',
            'subtitle' => 'Zarządzaj użytkownikami, klientami i cennikiem ofert. Edycja wymaga uprawnień administratora.',
            'read_only' => '⚠ Tryb podglądu — brak uprawnień do edycji.',
        ],
        'users' => [
            'title' => 'Użytkownicy i uprawnienia',
            'subtitle' => 'Zarządzaj kontami użytkowników i przypisuj role systemowe',
            'first_name' => 'Imię',
            'last_name' => 'Nazwisko',
            'phone' => 'Telefon',
            'password' => 'Hasło',
            'password_placeholder' => 'Pozostaw puste, aby nie zmieniać',
            'columns' => [
                'name' => 'Imię i nazwisko',
                'email' => 'E-mail',
                'role' => 'Rola',
                'action' => 'Akcja',
            ],
            'role_label' => 'Rola',
        ],
        'clients' => [
            'title' => 'Klienci i przypisania firm',
            'subtitle' => 'Przypisuj klientów i audytorów do firm',
            'columns' => [
                'company' => 'Firma',
                'current_client' => 'Aktualny klient',
                'current_auditor' => 'Aktualny audytor',
                'action' => 'Akcja',
            ],
            'no_client' => 'Brak klienta',
            'no_auditor' => 'Brak audytora',
        ],
        'pricing' => [
            'title' => 'Ceny ofert',
            'subtitle' => 'Referencyjny cennik dla każdego poziomu usług',
            'note' => 'Cennik ma charakter poglądowy. Skontaktuj się z ProximaLumine, aby skonfigurować dynamiczną integrację cen.',
            'custom_quote' => 'wycena indywidualna',
        ],
        'actions' => [
            'edit' => 'Edytuj',
            'save' => 'Zapisz',
            'save_permissions' => 'Zapisz uprawnienia',
            'cancel' => 'Anuluj',
        ],
    ],
];
