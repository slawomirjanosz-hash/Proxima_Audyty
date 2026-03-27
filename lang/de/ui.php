<?php

return [
    'company' => 'ENESA sp. z o. o.',
    'menu' => [
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        'crm' => 'CRM',
        'offer' => 'Offer',
        'audits' => 'Audits',
          'iso50001' => 'ISO50001',
           'info' => 'Informationen',
        'client_zone' => 'Client Zone',
        'settings' => 'Settings',
    ],
    'actions' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'close' => 'Close',
        'sign_in' => 'Sign in',
        'remember_me' => 'Remember me',
    ],
    'auth' => [
        'title' => 'Login',
        'access_panel' => 'Access panel',
        'password' => 'Password',
        'test_accounts' => 'Test accounts: admin@enesa.pl, auditor@enesa.pl, client@enesa.pl (password: password)',
    ],
    'dashboard' => [
        'title' => 'Dashboard - active audits',
        'subtitle' => 'List of all currently active audits.',
        'count' => 'Active audits',
        'empty' => 'No active audits.',
        'columns' => [
            'title' => 'Audit',
            'company' => 'Company',
            'auditor' => 'Auditor',
            'status' => 'Status',
            'updated' => 'Updated',
        ],
    ],
    'client' => [
        'tag' => 'Client Portal',
        'welcome' => 'Welcome back',
        'description' => 'Your dedicated ENESA space — track your assigned companies, active offers and ongoing energy audits in real time.',
        'meta' => [
            'account' => 'Account',
            'access_level' => 'Access level',
            'session_date' => 'Session date',
        ],
        'preview_title' => 'Preview mode:',
        'preview_text' => 'You are viewing Client Zone data as a privileged account.',
        'stats' => [
            'companies' => 'Companies',
            'companies_sub' => 'assigned to your account',
            'active_offers' => 'Active offers',
            'active_offers_sub' => 'in progress or pending',
            'audits' => 'Energy audits',
            'audits_sub' => 'completed or ongoing',
        ],
        'tables' => [
            'companies' => [
                'title' => 'Your companies',
                'columns' => [
                    'company_name' => 'Company name',
                    'city' => 'City',
                    'assigned_auditor' => 'Assigned auditor',
                ],
                'empty' => 'No companies assigned yet.',
            ],
            'offers' => [
                'title' => 'Your offers',
                'columns' => [
                    'title' => 'Title',
                    'status' => 'Status',
                    'company' => 'Company',
                ],
                'empty' => 'No offers assigned yet.',
            ],
            'audits' => [
                'title' => 'Energy audits',
                'columns' => [
                    'title' => 'Title',
                    'status' => 'Status',
                    'company' => 'Company',
                    'auditor' => 'Auditor',
                ],
                'empty' => 'No audits assigned yet.',
            ],
        ],
    ],
];
