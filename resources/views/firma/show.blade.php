<x-layouts.app>
    <style>
        .firma-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom:4px; }
        .firma-meta-inline { display:flex; flex-wrap:wrap; gap:6px 16px; align-items:center; margin:6px 0 4px; }
        .firma-meta-inline .meta-chip { display:inline-flex; align-items:center; gap:4px; font-size:13px; color:var(--ink-mute); }
        .firma-meta-inline .meta-chip strong { color:var(--ink); font-weight:600; }
        .section-box { background:var(--paper-soft); border:1px solid var(--paper-deep); border-radius:8px; margin-top:10px; overflow:hidden; }
        .section-box-toggle { width:100%; display:flex; justify-content:space-between; align-items:center; gap:8px; padding:14px 18px; background:var(--green-deep); border:none; cursor:pointer; text-align:left; font-family:var(--sans); color:var(--paper); }
        .section-box-toggle:hover { background:var(--green-primary); }
        .section-box-toggle h2 { margin:0; font-size:15px; font-weight:700; color:var(--paper); font-family:var(--serif); display:flex; align-items:center; gap:8px; }
        .section-box-toggle .toggle-right { display:flex; align-items:center; gap:8px; }
        .section-box-toggle .chevron { font-size:13px; color:var(--green-light); transition:transform .2s; }
        .section-box.open .chevron { transform:rotate(180deg); }
        .section-box.open .section-box-toggle { background:var(--green-primary); }
        .section-box-body { display:none; padding:16px 18px; border-top:1px solid var(--paper-deep); }
        .section-box.open .section-box-body { display:block; }
        .section-box-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
        .section-box-header h2 { margin:0; font-size:17px; font-weight:700; color:var(--green-deep); font-family:var(--serif); }
        .audit-row { display:grid; grid-template-columns:2fr 1fr 1fr 140px auto; gap:12px; align-items:start; padding:12px 14px; border:1px solid var(--paper-deep); border-radius:6px; background:var(--paper-soft); margin-bottom:8px; }
        .audit-row > .audit-edit-panel { grid-column:1/-1; }
        .audit-row:hover { background:var(--green-bg); }
        .audit-row-title { font-weight:700; color:var(--ink); font-size:14px; }
        .audit-row-meta { font-size:12px; color:var(--ink-mute); }
        .status-inprogress { background:#e0f2fe; color:#0369a1; }
        .status-sent     { background:#fef3c7; color:#92400e; }
        .status-accepted { background:var(--green-bg); color:var(--green-deep); }
        .status-portfolio { background:#f3f4f6; color:#374151; }
        .status-pill { display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:4px; font-family:var(--mono); }
        .status-wysłany { background:#dbeafe; color:#1e40af; }
        .status-rozpoczęty { background:var(--green-bg); color:var(--green-deep); }
        .status-do_analizy { background:#fef3c7; color:#92400e; }
        .status-zwrócony_do_poprawy { background:#fee2e2; color:#991b1b; }
        .status-zaakceptowany { background:var(--green-bg); color:var(--green-deep); }
        .status-zakończony { background:var(--paper-deep); color:var(--ink-soft); }
        .status-zafakturowany { background:#ede9fe; color:#5b21b6; }
        .status-zapłacony { background:var(--green-bg); color:var(--green-deep); }
        .status-new, .status-in_progress { background:#e0f2fe; color:#0369a1; }
        .status-completed { background:var(--paper-deep); color:var(--ink-soft); }
        .sec-badge { display:inline-block; font-size:11px; font-weight:700; padding:3px 9px; border-radius:4px; font-family:var(--mono); }
        .sec-badge-ok  { background:var(--green-bg); color:var(--green-deep); }
        .sec-badge-warn { background:#fef3c7; border:1px solid #fbbf24; color:#92400e; }
        .btn-sm { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:5px; font-size:12px; font-weight:700; text-decoration:none; border:none; cursor:pointer; line-height:1; white-space:nowrap; font-family:var(--sans); }
        .btn-primary-sm { background:var(--green-primary); color:var(--paper); }
        .btn-primary-sm:hover { background:var(--green-deep); }
        .btn-secondary-sm { background:var(--green-bg); color:var(--green-deep); }
        .btn-danger-sm { background:#fee2e2; color:#991b1b; }
        .inquiry-row { border:1px solid var(--paper-deep); border-radius:6px; padding:12px 14px; background:var(--paper-soft); margin-bottom:8px; display:flex; flex-direction:column; gap:8px; }
        .inquiry-row.status-new { border-left:4px solid var(--gold); }
        .inquiry-row.status-accepted { border-left:4px solid var(--green-primary); }
        .inquiry-row.status-rejected { border-left:4px solid var(--rose); border-color:#fee2e2; background:#fff5f5; }
        .inquiry-row-top { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap; }
        .inquiry-type { font-weight:700; font-size:14px; color:var(--green-deep); font-family:var(--serif); }
        .inquiry-msg { font-size:13px; color:var(--ink-soft); margin:2px 0; white-space:pre-line; }
        .inquiry-meta { font-size:12px; color:var(--ink-mute); font-family:var(--mono); }
        .inquiry-actions { display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
        .portfolio-picker { background:var(--green-bg); border:1px solid var(--green-light); border-radius:6px; padding:12px 14px; margin-top:6px; display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end; }
        .chat-box { background:var(--paper-soft); border:1px solid var(--paper-deep); border-radius:6px; padding:10px 12px; max-height:340px; overflow-y:auto; margin-bottom:10px; display:flex; flex-direction:column; gap:8px; }
        .chat-bubble-wrap { display:flex; flex-direction:column; }
        .chat-bubble-wrap.admin { align-items:flex-end; }
        .chat-bubble-wrap.client { align-items:flex-start; }
        .chat-bubble { max-width:75%; padding:8px 12px; border-radius:8px; font-size:13px; line-height:1.45; white-space:pre-wrap; word-break:break-word; }
        .chat-bubble.admin { background:var(--green-deep); color:var(--paper); border-bottom-right-radius:2px; }
        .chat-bubble.client { background:white; border:1px solid var(--paper-deep); color:var(--ink); border-bottom-left-radius:2px; }
        .chat-meta-line { font-size:11px; color:var(--ink-mute); margin-top:3px; font-family:var(--mono); }
        .offer-row-firm { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding:10px 14px; border:1px solid var(--paper-deep); border-radius:6px; background:var(--paper-soft); margin-bottom:6px; }
        .form-inline { display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; }
        .field-sm { display:flex; flex-direction:column; gap:3px; }
        .field-sm label { font-size:12px; font-weight:700; color:var(--green-deep); font-family:var(--mono); letter-spacing:0.5px; }
        .field-sm input, .field-sm select { padding:8px 10px; border:1px solid var(--paper-deep); border-radius:5px; font-size:13px; background:white; min-width:160px; font-family:var(--sans); }
        .credential-box { background:var(--green-bg); border:1px solid var(--green-light); border-radius:6px; padding:14px 16px; }
        .credential-box.has { background:#f0f9ff; border-color:#bae6fd; }
        .user-row { display:grid; grid-template-columns:auto 1fr auto auto; gap:12px; align-items:center; padding:10px 14px; border:1px solid var(--paper-deep); border-radius:6px; background:var(--paper-soft); margin-bottom:8px; }
        .user-row:hover { background:var(--green-bg); }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:var(--green-deep); color:var(--paper); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; flex-shrink:0; font-family:var(--serif); }
        .role-badge { display:inline-block; padding:3px 9px; border-radius:4px; font-size:11px; font-weight:700; font-family:var(--mono); }
        .role-client   { background:#dbeafe; color:#1e40af; }
        .role-auditor  { background:var(--green-bg); color:var(--green-deep); }
        .role-admin    { background:#fef3c7; color:#92400e; }
        .add-user-tabs { display:flex; gap:4px; margin-bottom:14px; }
        .add-user-tab { padding:6px 14px; border:1px solid var(--paper-deep); border-radius:5px; font-size:12px; font-weight:700; cursor:pointer; background:var(--paper-soft); color:var(--green-deep); transition:.15s; font-family:var(--sans); }
        .add-user-tab.active { background:var(--green-primary); color:var(--paper); border-color:transparent; }
        @media (max-width: 900px) {
            .audit-row { grid-template-columns: 1fr; }
        }
        .firma-cols { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
        .firma-col { min-width:0; display:flex; flex-direction:column; gap:16px; }
        .firma-col .section-box { margin-bottom:0; }
        @media (max-width: 1100px) {
            .firma-cols { grid-template-columns:1fr; }
        }
    </style>

    <section class="panel">
        @if (session('status'))
            <div style="margin-bottom:12px; padding:10px 14px; background:#f0fdf4; border:1px solid #86efac; border-radius:10px; color:#166534; font-weight:600;">
                ✅ {{ session('status') }}
            </div>
        @endif

        <div class="firma-header">
            <div>
                <a href="{{ route('dashboard') }}" style="font-size:13px; color:var(--green-primary); text-decoration:none; font-family:var(--sans);">← Dashboard</a>
                <h1 style="margin:4px 0 0; font-family:var(--serif); font-size:24px; font-weight:600; color:var(--green-deep);">{{ $company->name }}</h1>
                @if($company->short_name && $company->short_name !== $company->name)
                    <p class="muted" style="margin:2px 0 0; font-size:13px;">{{ $company->short_name }}</p>
                @endif
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                @if($company->nip)
                    <span style="background:var(--green-bg); border:1px solid var(--green-light); border-radius:4px; padding:4px 10px; font-size:12px; font-weight:700; color:var(--green-deep); font-family:var(--mono);">NIP {{ $company->nip }}</span>
                @endif
                @if(auth()->user()->canManageEverything())
                    <button type="button" onclick="openDeleteCompanyModal()"
                        style="padding:5px 14px; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer;">
                        🗑 Usuń firmę
                    </button>
                @endif
            </div>
        </div>

        {{-- Company metadata inline --}}
        <div class="firma-meta-inline">
            @if($company->city)
                @php
                    $addrParts = array_filter([$company->postal_code, $company->street]);
                    $addrSuffix = $addrParts ? ', ' . implode(' ', $addrParts) : '';
                @endphp
                <span class="meta-chip">📍 <strong>{{ $company->city }}{{ $addrSuffix }}</strong></span>
            @endif
            @if($company->phone)<span class="meta-chip">📞 <strong>{{ $company->phone }}</strong></span>@endif
            @if($company->email)<span class="meta-chip">✉️ <strong>{{ $company->email }}</strong></span>@endif
            @if($company->auditor)<span class="meta-chip">👤 Opiekun: <strong>{{ $company->auditor->name }}</strong></span>@endif
        </div>

        <div class="firma-cols">
        <div class="firma-col">{{-- left column --}}

        {{-- ── Client credentials ── --}}
        <div class="section-box" id="sec-credentials">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-credentials')">
                <h2>🔐 Dostęp klienta do systemu</h2>
                <div class="toggle-right">
                    @if($company->client)<span class="sec-badge sec-badge-ok">✅ Konto istnieje</span>@else<span class="sec-badge sec-badge-warn">Brak konta</span>@endif
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            @if($company->client)
                <div class="credential-box has" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <span style="background:#bae6fd; color:#0369a1; font-size:13px; font-weight:700; padding:6px 14px; border-radius:8px;">✅ Konto istnieje</span>
                    <span style="font-size:13px; color:#4c6373;">Klient loguje się e-mailem. Dane w <a href="{{ route('settings.index', ['tab' => 'users']) }}" style="color:#0e89d8;">Ustawieniach → Użytkownicy</a>.</span>
                </div>
            @else
                <div class="credential-box" style="margin-bottom:16px;">
                    <p style="margin:0 0 10px; font-size:13px; color:#4c6373;">Firma nie ma jeszcze konta w systemie. Utwórz dane do logowania dla klienta.</p>

                    @if ($errors->has('email') || $errors->has('first_name') || $errors->has('last_name') || $errors->has('password'))
                        <div style="margin-bottom:10px; padding:8px 12px; background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; color:#991b1b; font-size:13px;">
                            @foreach ($errors->all() as $err)<div>⚠ {{ $err }}</div>@endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('firma.storeClient', $company) }}">
                        @csrf
                        <div class="form-inline">
                            <div class="field-sm">
                                <label>Imię *</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Jan">
                            </div>
                            <div class="field-sm">
                                <label>Nazwisko *</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Kowalski">
                            </div>
                            <div class="field-sm">
                                <label>E-mail *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="jan@firma.pl">
                            </div>
                            <div class="field-sm">
                                <label>Hasło *</label>
                                <input type="password" name="password" required placeholder="Min. 8 znaków">
                            </div>
                            <button type="submit" class="btn-sm btn-primary-sm" style="align-self:flex-end; padding:8px 16px;">Utwórz konto</button>
                        </div>
                    </form>
                </div>
            @endif
            </div>
        </div>

        {{-- ── Users with access ── --}}
        <div class="section-box" id="sec-users">
            <?php $usersCount = $company->assignedUsers->count(); ?>
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-users')">
                <h2>👥 Użytkownicy firmy</h2>
                <div class="toggle-right">
                    <span class="sec-badge sec-badge-ok">{{ $usersCount }} {{ $usersCount === 1 ? 'użytkownik' : ($usersCount < 5 ? 'użytkownicy' : 'użytkowników') }}</span>
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            @if($company->assignedUsers->isNotEmpty())
                @foreach($company->assignedUsers as $u)
                    <?php                         $initials = mb_strtoupper(mb_substr($u->first_name ?? $u->name, 0, 1) . mb_substr($u->last_name ?? '', 0, 1));
                        $roleVal  = $u->role instanceof \App\Enums\UserRole ? $u->role->value : (string) $u->role;
                        $roleLabels = ['client' => 'Klient', 'auditor' => 'Audytor', 'admin' => 'Admin', 'super_admin' => 'SuperAdmin'];
                        $roleLabel = $roleLabels[$roleVal] ?? ucfirst($roleVal);
                     ?>
                    <div class="user-row">
                        <div class="user-avatar">{{ $initials }}</div>
                        <div>
                            <div style="font-weight:700; font-size:14px; color:#0f2330;">{{ $u->name }}</div>
                            <div style="font-size:12px; color:#4c6373; margin-top:2px;">
                                📧 {{ $u->email }}
                                @if($u->phone) &nbsp;·&nbsp; 📞 {{ $u->phone }} @endif
                            </div>
                        </div>
                        <div>
                            <span class="role-badge role-{{ $roleVal }}">{{ $roleLabel }}</span>
                        </div>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <button type="button"
                                class="btn-sm"
                                style="background:#fff8e1; color:#92400e; border:1px solid #fde68a;"
                                onclick="openMailModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')"
                            ><span>✉</span> Wyślij mail</button>
                            <button type="button"
                                class="btn-sm btn-danger-sm"
                                style="border:1px solid #fca5a5;"
                                onclick="openRemoveModal({{ $u->id }}, '{{ addslashes($u->name) }}')"
                            >✕ Usuń dostęp</button>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="padding:14px; text-align:center; color:#8a9bac; border:1px dashed #c8d8e6; border-radius:12px; font-size:13px; margin-bottom:14px;">
                    Brak przypisanych użytkowników.
                </div>
            @endif

            <hr class="divider">

            <button type="button"
                id="add-user-toggle"
                onclick="toggleAddUserPanel()"
                style="display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; margin-bottom:12px;">
                ➕ Dodaj użytkownika
            </button>

            {{-- Add user panel --}}
            <div id="add-user-panel" style="display:none;">
            <h3 style="margin:0 0 12px; font-size:15px; font-weight:700; color:#1d3a50;">➕ Dodaj użytkownika</h3>
            <div class="add-user-tabs">
                <button type="button" class="add-user-tab active" onclick="switchUserTab('new', this)">Nowy użytkownik</button>
                <button type="button" class="add-user-tab" onclick="switchUserTab('existing', this)">Istniejący użytkownik</button>
            </div>

            {{-- NEW user form --}}
            <div id="tab-new">
                @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('password') || $errors->has('role'))
                    <div style="margin-bottom:10px; padding:8px 12px; background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; color:#991b1b; font-size:13px;">
                        @foreach ($errors->all() as $err)<div>⚠ {{ $err }}</div>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('firma.addUser', $company) }}">
                    @csrf
                    <div class="form-inline">
                        <div class="field-sm">
                            <label>Imię *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="Jan">
                        </div>
                        <div class="field-sm">
                            <label>Nazwisko *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Kowalski">
                        </div>
                        <div class="field-sm">
                            <label>E-mail *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="jan@firma.pl">
                        </div>
                        <div class="field-sm">
                            <label>Hasło *</label>
                            <input type="password" name="password" required placeholder="Min. 8 znaków">
                        </div>
                        <div class="field-sm">
                            <label>Rola *</label>
                            <select name="role" required>
                                <option value="client" @selected(old('role','client')==='client')>Klient</option>
                                <option value="auditor" @selected(old('role')==='auditor')>Audytor</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-sm btn-primary-sm" style="align-self:flex-end; padding:8px 16px;">Utwórz i przydziel</button>
                    </div>
                    <p style="margin:8px 0 0; font-size:11px; color:#6b8aa3;">Po utworzeniu konta zostanie wysłany e-mail powitalny z danymi do logowania.</p>
                </form>
            </div>

            {{-- EXISTING user form --}}
            <div id="tab-existing" style="display:none;">
                @if($availableUsers->isNotEmpty())
                    <form method="POST" action="{{ route('firma.addUser', $company) }}">
                        @csrf
                        <div class="form-inline">
                            <div class="field-sm" style="flex:2;">
                                <label>Wybierz użytkownika *</label>
                                <select name="user_id" required style="min-width:280px;">
                                    <option value="">Wybierz...</option>
                                    @foreach($availableUsers as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-sm btn-primary-sm" style="align-self:flex-end; padding:8px 16px;">Przydziel dostęp</button>
                        </div>
                    </form>
                @else
                    <p style="color:#8a9bac; font-size:13px; font-style:italic; margin:0;">Brak dostępnych użytkowników do przypisania.</p>
                @endif
            </div>
            </div>{{-- /add-user-panel --}}
            </div>{{-- /section-box-body --}}
        </div>{{-- /sec-users --}}

        {{-- ── Inquiries ── --}}
        @if($inquiries->isNotEmpty())
        <?php $hasAccepted = $inquiries->whereIn('status', ['new','offer_accepted'])->isNotEmpty(); ?>
        <div class="section-box" id="sec-inquiries" style="border-color:#fbbf24;">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-inquiries')" style="border-left:4px solid #fbbf24;">
                <h2>📬 Zapytania klientów</h2>
                <div class="toggle-right">
                    @php($pendingInquiries = $inquiries->whereIn('status', ['new','offer_accepted'])->count())
                    @if($pendingInquiries > 0)
                        <span class="sec-badge sec-badge-warn">{{ $pendingInquiries }} {{ $pendingInquiries === 1 ? 'do obsługi' : 'do obsługi' }}</span>
                    @else
                        <span class="sec-badge sec-badge-ok">{{ $inquiries->count() }} {{ $inquiries->count() === 1 ? 'zapytanie' : ($inquiries->count() < 5 ? 'zapytania' : 'zapytań') }}</span>
                    @endif
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            @foreach($inquiries as $inquiry)
                <div class="inquiry-row status-{{ $inquiry->status }}">
                    <div class="inquiry-row-top">
                        <div style="flex:1;">
                            <div class="inquiry-type">{{ $inquiry->audit_type_name ?? '—' }}</div>
                            @if($inquiry->message)
                                <div class="inquiry-msg">{{ $inquiry->message }}</div>
                            @endif
                            <div class="inquiry-meta">
                                👤 {{ $inquiry->user?->name ?? '—' }} &nbsp;·&nbsp; 🕐 {{ $inquiry->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <div>
                            <span class="inquiry-badge" style="padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; background:{{ $inquiry->statusBg() }}; color:{{ $inquiry->statusColor() }};">
                                {{ $inquiry->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="inquiry-actions">
                        @if($inquiry->status === 'new')
                            <form method="POST" action="{{ route('inquiry.accept', $inquiry) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm btn-primary-sm">✅ Przyjęto</button>
                            </form>
                            <form method="POST" action="{{ route('inquiry.reject', $inquiry) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm btn-danger-sm" onclick="return confirm('Odrzucić to zapytanie?')">✗ Odrzuć</button>
                            </form>
                        @endif

                        @if($inquiry->status === 'in_review')
                            <div style="padding:7px 12px; background:#fef3c7; border:1px solid #fcd34d; border-radius:8px; color:#92400e; font-size:13px;">
                                📤 Oferta wysłana — oczekiwanie na odpowiedź klienta
                            </div>
                        @endif

                        @if($inquiry->status === 'offer_accepted')
                            <div style="padding:8px 14px; background:#d1fae5; border:1px solid #6ee7b7; border-radius:9px; color:#065f46; font-size:13px; font-weight:700;">
                                ✅ Klient zaakceptował ofertę — <a href="#sec-assign-audit" style="color:#047857; text-decoration:underline;">Przydziel audyt poniżej ↓</a>
                            </div>
                        @endif

                        @if($inquiry->status === 'accepted')
                            @if($inquiry->offer)
                                <a href="{{ route('offers.edit', $inquiry->offer) }}" class="btn-sm" style="background:#e0f2fe; color:#0369a1;">📄 Edytuj ofertę #{{ $inquiry->offer->offer_number }}</a>
                                <form method="POST" action="{{ route('offers.sendToClient', $inquiry->offer) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-sm" style="background:#1ba84a; color:#fff;" onclick="return confirm('Wysłać ofertę do klienta?')">📤 Wyślij ofertę</button>
                                </form>
                            @else
                                <a href="{{ route('offers.create', ['from_company' => $company->id, 'inquiry_id' => $inquiry->id]) }}" class="btn-sm btn-primary-sm">📄 Zrób ofertę</a>
                                <button type="button" class="btn-sm btn-secondary-sm" onclick="togglePortfolio({{ $inquiry->id }})">📁 Wybierz z portfolio</button>
                            @endif
                        @endif
                    </div>

                    {{-- Portfolio picker --}}
                    @if($inquiry->status === 'accepted' && !$inquiry->offer)
                    <div id="portfolio-{{ $inquiry->id }}" style="display:none;">
                        @if($portfolioOffers->isEmpty())
                            <p style="font-size:13px; color:#8aa3b5; margin:6px 0 0; font-style:italic;">Brak ofert w portfolio.</p>
                        @else
                        <form method="POST" action="{{ route('offers.copyForCompany', $company) }}" class="portfolio-picker">
                            @csrf
                            <input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">
                            <div class="field-sm" style="flex:1; min-width:220px;">
                                <label style="font-size:12px; font-weight:700; color:#1d3a50; margin-bottom:3px; display:block;">Wybierz ofertę z portfolio</label>
                                <select name="offer_id" required style="min-width:260px;">
                                    <option value="">— Wybierz ofertę —</option>
                                    @foreach($portfolioOffers as $po)
                                        <option value="{{ $po->id }}">{{ $po->offer_number ? $po->offer_number.' – ' : '' }}{{ $po->offer_title }} ({{ number_format((float)$po->total_price, 2, ',', ' ') }} zł)</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-sm btn-primary-sm" style="align-self:flex-end;">Kopiuj i przenieś do W toku</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            @endforeach
            </div>
        </div>
        @endif

        </div>{{-- end left column --}}
        <div class="firma-col">{{-- right column --}}

        {{-- ── Offers for this company ── --}}
        @if($companyOffers->isNotEmpty())
        <div class="section-box" id="sec-offers">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-offers')">
                <h2>💼 Oferty dla tej firmy</h2>
                <div class="toggle-right">
                    @php($pendingOffers = $companyOffers->whereIn('status', ['inprogress'])->count())
                    @if($pendingOffers > 0)
                        <span class="sec-badge sec-badge-warn">{{ $pendingOffers }} {{ $pendingOffers === 1 ? 'w toku' : 'w toku' }}</span>
                    @else
                        <span class="sec-badge sec-badge-ok">{{ $companyOffers->count() }}</span>
                    @endif
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">
            @foreach($companyOffers as $co)
            <div class="offer-row-firm">
                <div>
                    <div style="font-weight:700; font-size:14px; color:#0f2330;">{{ $co->offer_title }}</div>
                    <div style="font-size:12px; color:#4c6373; margin-top:2px;">
                        {{ $co->offer_number ? '#'.$co->offer_number.' · ' : '' }}
                        {{ $co->offer_date?->format('d.m.Y') ?? '' }}
                        @if($co->total_price) &nbsp;·&nbsp; <strong>{{ number_format((float)$co->total_price, 2, ',', ' ') }} zł</strong> @endif
                    </div>
                </div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                    <span class="status-pill {{ 'status-'.$co->status }}" style="font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px;">
                        {{ $co->status === 'inprogress' ? 'W toku' : ($co->status === 'sent' ? 'Wysłana' : ($co->status === 'accepted' ? 'Zaakceptowana' : 'Portfolio')) }}
                    </span>
                    <a href="{{ route('offers.edit', $co) }}" class="btn-sm btn-secondary-sm">✏ Edytuj</a>
                    <form method="POST" action="{{ route('offers.sendToClient', $co) }}" style="display:inline">
                        @csrf
                        @if(in_array($co->status, ['sent', 'accepted']))
                            <button type="submit" class="btn-sm" style="background:#c8d8e6; color:#4c6373; border:1px solid #b0c4d6;" onclick="return confirm('Oferta już wysłana. Wysłać ponownie?')">📤 Wyślij ponownie</button>
                        @else
                            <button type="submit" class="btn-sm" style="background:#1ba84a; color:#fff;" onclick="return confirm('Wysłać tę ofertę do klienta?')">📤 Wyślij do klienta</button>
                        @endif
                    </form>
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif

        {{-- ── Chat z klientem ── --}}
        @php($unread = $chatMessages->where('is_from_admin', false)->whereNull('read_at')->count())
        <div class="section-box" id="sec-chat">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-chat')">
                <h2>💬 Chat z klientem</h2>
                <div class="toggle-right">
                    @if($unread > 0)
                        <span id="chat-unread-badge" class="sec-badge sec-badge-warn">{{ $unread }} nieprzeczytane</span>
                    @else
                        <span id="chat-unread-badge" class="sec-badge sec-badge-ok" style="display:none;"></span>
                    @endif
                    <span class="sec-badge sec-badge-ok" id="chat-count-label">{{ $chatMessages->count() }} wiad.</span>
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            <div class="chat-box" id="admin-chat-box">
                @forelse($chatMessages as $msg)
                    <div class="chat-bubble-wrap {{ $msg->is_from_admin ? 'admin' : 'client' }}" data-msg-id="{{ $msg->id }}">
                        <div class="chat-bubble {{ $msg->is_from_admin ? 'admin' : 'client' }}">{{ $msg->message }}</div>
                        <div class="chat-meta-line">{{ $msg->user?->name ?? '—' }} · {{ $msg->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                @empty
                    <div id="chat-empty-note" style="text-align:center; color:var(--ink-mute); font-size:13px; padding:20px;">Brak wiadomości.</div>
                @endforelse
            </div>

            <div style="display:flex; gap:8px; align-items:flex-end; margin-top:8px;">
                <textarea id="chat-input" rows="2" placeholder="Wpisz odpowiedź do klienta..." style="flex:1; resize:vertical; padding:8px 10px; border:1px solid var(--paper-deep); border-radius:8px; font-size:13px;" required></textarea>
                <button type="button" id="chat-send-btn" class="btn-sm btn-primary-sm" style="padding:8px 16px;" onclick="adminChatSend()">Wyślij</button>
            </div>
            </div>
        </div>

        {{-- ── Assigned audits ── --}}
        <?php $auditsCount = $company->energyAudits->count(); ?>
        <div class="section-box" id="sec-audits">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-audits')">
                <h2>📋 Audyty firmy</h2>
                <div class="toggle-right">
                    @php($activeAudits = $company->energyAudits->whereNotIn('status', ['zakończony','zafakturowany','zapłacony'])->count())
                    @if($activeAudits > 0)
                        <span class="sec-badge sec-badge-warn">{{ $activeAudits }} {{ $activeAudits === 1 ? 'w toku' : 'w toku' }}</span>
                    @else
                        <span class="sec-badge sec-badge-ok">{{ $auditsCount }} {{ $auditsCount === 1 ? 'audyt' : ($auditsCount < 5 ? 'audyty' : 'audytów') }}</span>
                    @endif
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            @if($company->energyAudits->isNotEmpty())
                <?php                     $statusOrder = array_keys(\App\Models\EnergyAudit::STATUSES);
                    $sorted = $company->energyAudits->sortBy(function ($a) use ($statusOrder) {
                        return array_search($a->status, $statusOrder);
                    })->values();
                 ?>

                @foreach($sorted as $audit)
                    <?php                         $statusKey = str_replace(' ', '_', $audit->status);
                        $statusClass = 'status-' . $statusKey;
                     ?>
                    <div class="audit-row">
                        <div>
                            <div class="audit-row-title">{{ $audit->title }}</div>
                            <div class="audit-row-meta">{{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-mute); margin-bottom:2px;">Audytor</div>
                            <div style="font-size:13px;">{{ $audit->auditor?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-mute); margin-bottom:2px;">Data</div>
                            <div style="font-size:13px;">{{ $audit->created_at->format('d.m.Y') }}</div>
                        </div>
                        <div>
                            <span class="status-pill {{ $statusClass }}">{{ $audit->statusLabel() }}</span>
                        </div>
                        <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                            <a href="{{ route('firma.audit', [$company, $audit]) }}" class="btn-sm btn-primary-sm">Wejdź →</a>
                            @if($audit->agent_type === 'general')
                                <a href="{{ route('client.audit.master') }}?company_id={{ $company->id }}"
                                   class="btn-sm"
                                   style="background:#f0faf4; color:#0f6e2e; border:1px solid #a7d9b7; padding:5px 10px;"
                                   title="Otwórz ankietę Master audytu energetycznego dla tej firmy">📋 Ankieta Master</a>
                            @elseif($audit->agent_type === 'compressor_room')
                                <a href="{{ route('client.audit.compressor.questionnaire', $audit) }}"
                                   class="btn-sm"
                                   style="background:#f0faf4; color:#0f6e2e; border:1px solid #a7d9b7; padding:5px 10px;"
                                   title="Wejdź do ankiety Kompresory jako audytor">📋 Ankieta</a>
                                <a href="{{ route('client.audit.ai', $audit) }}"
                                   class="btn-sm"
                                   style="background:var(--green-bg); color:var(--green-deep); border:1px solid var(--green-light); padding:5px 10px;"
                                   title="Uruchom asystenta AI dla tego audytu">🤖 AI</a>
                            @endif
                            <button type="button" class="btn-sm"
                                style="background:#fffbeb; color:#92400e; border:1px solid #fcd34d; padding:5px 10px;"
                                onclick="toggleAuditEdit({{ $audit->id }})"
                                title="Edytuj audyt (audytor, tytuł)">✏️ Edytuj</button>
                            <form method="POST" action="{{ route('firma.destroyAudit', [$company, $audit]) }}" style="margin:0;" onsubmit="return confirm('Usunąć audyt &quot;{{ addslashes($audit->title) }}&quot;? Tej operacji nie można cofnąć.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:5px 10px;">Usuń</button>
                            </form>
                        </div>
                        {{-- Inline edit form (hidden by default) --}}
                        <div class="audit-edit-panel">
                        <div id="audit-edit-{{ $audit->id }}" style="display:none; margin-top:10px; background:#fffbeb; border:1px solid #fcd34d; border-radius:8px; padding:14px 16px;">
                            <form method="POST" action="{{ route('firma.updateAudit', [$company, $audit]) }}" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                                @csrf
                                @method('PATCH')
                                <div style="flex:2; min-width:200px;">
                                    <label style="font-size:11px; font-weight:600; text-transform:uppercase; color:var(--ink-mute); display:block; margin-bottom:4px;">Tytuł audytu</label>
                                    <input type="text" name="title" value="{{ old('title', $audit->title) }}"
                                           style="width:100%; padding:6px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; box-sizing:border-box;">
                                </div>
                                <div style="flex:2; min-width:180px;">
                                    <label style="font-size:11px; font-weight:600; text-transform:uppercase; color:var(--ink-mute); display:block; margin-bottom:4px;">Audytor</label>
                                    <select name="auditor_id" style="width:100%; padding:6px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; box-sizing:border-box;">
                                        <option value="">— brak —</option>
                                        @foreach($auditors as $aud)
                                            <option value="{{ $aud->id }}" @selected($audit->auditor_id == $aud->id)>{{ $aud->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display:flex; gap:6px;">
                                    <button type="submit" class="btn-sm btn-primary-sm" style="padding:7px 14px;">Zapisz</button>
                                    <button type="button" class="btn-sm" style="padding:7px 12px;" onclick="toggleAuditEdit({{ $audit->id }})">Anuluj</button>
                                </div>
                            </form>
                        </div>
                        </div>{{-- /audit-edit-panel --}}
                    </div>
                @endforeach
            @else
                <div style="padding:20px; text-align:center; color:var(--ink-mute); border:1px dashed var(--paper-deep); border-radius:12px; font-size:14px;">
                    Brak przydzielonych audytów.
                </div>
            @endif

            </div>{{-- /section-box-body sec-audits --}}
        </div>{{-- /section-box sec-audits --}}

        {{-- ── Przydziel nowy audyt ── --}}
        <div class="section-box {{ $pendingAudits->isNotEmpty() ? 'open' : '' }}" id="sec-assign-audit">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-assign-audit')">
                <h2>➕ Przydziel nowy audyt</h2>
                <div class="toggle-right">
                    @if($pendingAudits->isNotEmpty())
                        <span class="sec-badge sec-badge-warn">{{ $pendingAudits->count() }} oczekujących</span>
                    @endif
                    <span class="chevron">▼</span>
                </div>
            </button>
            <div class="section-box-body">

            @if($pendingAudits->isNotEmpty())
            <div style="margin-bottom:16px;">
                <div style="font-size:13px; font-weight:800; color:#065f46; margin-bottom:10px;">⚡ Oczekujące audyty — zaakceptowano ofertę</div>
                @foreach($pendingAudits as $pa)
                <div style="background:#f0fff4; border:1px solid #86efac; border-radius:12px; padding:14px 16px; margin-bottom:10px;">
                    <div style="font-size:13px; font-weight:700; color:#065f46; margin-bottom:10px;">
                        {{ $pa->auditType?->name ?? $pa->audit_type ?? '—' }}
                        <span style="font-size:11px; font-weight:400; color:#16a34a; margin-left:8px;">🕐 Oczekuje na zatwierdzenie</span>
                    </div>
                    <form method="POST" action="{{ route('firma.approveAudit', [$company, $pa]) }}">
                        @csrf @method('PATCH')
                        <div style="display:grid; gap:8px;">
                            <div>
                                <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Nazwa audytu *</label>
                                <input type="text" name="title" value="{{ $pa->title }}" required
                                    style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:13px; background:#fff; box-sizing:border-box;">
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Agent AI *</label>
                                    <select name="agent_type" required style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                        <optgroup label="Audyty energetyczne">
                                            <option value="general"                 {{ $pa->agent_type==='general'                 ? 'selected' : '' }}>Audyt Energetyczny zakładu (Master)</option>
                                            <option value="compressor_room"         {{ $pa->agent_type==='compressor_room'         ? 'selected' : '' }}>Kompresory</option>
                                            <option value="boiler_room"             {{ $pa->agent_type==='boiler_room'             ? 'selected' : '' }}>Kotłownia</option>
                                            <option value="drying_room"             {{ $pa->agent_type==='drying_room'             ? 'selected' : '' }}>Suszarnia</option>
                                            <option value="buildings"               {{ $pa->agent_type==='buildings'               ? 'selected' : '' }}>Budynki</option>
                                            <option value="technological_processes" {{ $pa->agent_type==='technological_processes' ? 'selected' : '' }}>Procesy technologiczne</option>
                                        </optgroup>
                                        <optgroup label="ISO 50001">
                                            <option value="iso50001" {{ $pa->agent_type==='iso50001' ? 'selected' : '' }}>ISO 50001</option>
                                        </optgroup>
                                        <optgroup label="Białe certyfikaty">
                                            <option value="bc_general"                 {{ $pa->agent_type==='bc_general'                 ? 'selected' : '' }}>BC Ogólnie</option>
                                            <option value="bc_compressor_room"         {{ $pa->agent_type==='bc_compressor_room'         ? 'selected' : '' }}>BC Kompresory</option>
                                            <option value="bc_boiler_room"             {{ $pa->agent_type==='bc_boiler_room'             ? 'selected' : '' }}>BC Kotłownia</option>
                                            <option value="bc_drying_room"             {{ $pa->agent_type==='bc_drying_room'             ? 'selected' : '' }}>BC Suszarnia</option>
                                            <option value="bc_buildings"               {{ $pa->agent_type==='bc_buildings'               ? 'selected' : '' }}>BC Budynki</option>
                                            <option value="bc_technological_processes" {{ $pa->agent_type==='bc_technological_processes' ? 'selected' : '' }}>BC Procesy</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Audytor</label>
                                    <select name="auditor_id" style="width:100%; border:1px solid #d1fae5; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                        <option value="">Brak przydziału</option>
                                        @foreach($auditors as $auditor)
                                            <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div style="display:flex; gap:8px;">
                                <button type="submit" style="flex:1; padding:8px 14px; background:linear-gradient(130deg,#22c55e,#16a34a); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">✓ Zatwierdź i uruchom audyt</button>
                                <button type="button" onclick="rejectAudit({{ $pa->id }}, '{{ addslashes($pa->title) }}')" style="padding:8px 14px; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;">Odrzuć</button>
                            </div>
                        </div>
                    </form>
                    <form id="reject-form-{{ $pa->id }}" method="POST" action="{{ route('firma.destroyAudit', [$company, $pa]) }}" style="display:none;">@csrf @method('DELETE')</form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Manual new audit — 2-step selector --}}
            <div>
                <p style="font-size:13px; color:#4c6373; margin:0 0 14px;">Wybierz kategorię, następnie typ audytu, uzupełnij nazwę i kliknij Przydziel.</p>

                {{-- Step 1: Category --}}
                <div style="margin-bottom:14px;">
                    <div style="font-size:11px; font-weight:700; color:#6b8aa3; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Krok 1 — Kategoria</div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" class="assign-cat-btn" data-cat="energy"
                            onclick="selectAuditCat('energy')"
                            style="padding:10px 18px; border-radius:10px; border:2px solid #d5e0ea; background:#f8fbfd; font-size:13px; font-weight:700; color:#1d4f73; cursor:pointer; transition:.15s; display:flex; align-items:center; gap:8px;">
                            ⚡ Audyty energetyczne
                        </button>
                        <button type="button" class="assign-cat-btn" data-cat="bc"
                            onclick="selectAuditCat('bc')"
                            style="padding:10px 18px; border-radius:10px; border:2px solid #d5e0ea; background:#f8fbfd; font-size:13px; font-weight:700; color:#1d4f73; cursor:pointer; transition:.15s; display:flex; align-items:center; gap:8px;">
                            📜 Białe certyfikaty
                        </button>
                        <button type="button" class="assign-cat-btn" data-cat="iso"
                            onclick="selectAuditCat('iso')"
                            style="padding:10px 18px; border-radius:10px; border:2px solid #d5e0ea; background:#f8fbfd; font-size:13px; font-weight:700; color:#1d4f73; cursor:pointer; transition:.15s; display:flex; align-items:center; gap:8px;">
                            🏭 ISO 50001
                        </button>
                    </div>
                </div>

                {{-- Step 2: Subtype (shown after category) --}}
                <div id="assign-subtype-wrap" style="display:none; margin-bottom:14px;">
                    <div style="font-size:11px; font-weight:700; color:#6b8aa3; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Krok 2 — Typ audytu</div>
                    <div id="assign-subtype-btns" style="display:flex; gap:8px; flex-wrap:wrap;"></div>
                </div>

                {{-- Step 3: Form (shown after subtype) --}}
                <form method="POST" action="{{ route('firma.storeAudit', $company) }}" id="assign-audit-form" style="display:none;">
                    @csrf
                    <input type="hidden" name="agent_type" id="assign-agent-type">
                    <div style="padding:14px; border:2px solid #0e89d8; border-radius:12px; background:#f0f8ff; display:grid; gap:10px;">
                        <div style="font-size:13px; font-weight:700; color:#0f2330;">
                            Wybrany: <span id="assign-type-label" style="color:#0e89d8;"></span>
                        </div>
                        <div style="display:grid; grid-template-columns:2fr 1fr; gap:10px;">
                            <div>
                                <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Nazwa audytu *</label>
                                <input type="text" name="title" id="assign-title" required placeholder="np. Audyt energetyczny 2026"
                                    style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:13px; background:#fff; box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="font-size:11px; font-weight:700; color:#374151; display:block; margin-bottom:4px;">Audytor</label>
                                <select name="auditor_id" style="width:100%; border:1px solid #c8d8e6; border-radius:8px; padding:7px 10px; font-size:12px; background:#fff; box-sizing:border-box;">
                                    <option value="">Brak</option>
                                    @foreach($auditors as $auditor)
                                        <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" style="padding:9px 20px; background:linear-gradient(130deg,#0e89d8,#0772b5); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">Przydziel audyt</button>
                            <button type="button" onclick="resetAuditAssign()" style="padding:9px 14px; background:#f8fbfd; border:1px solid #d5e0ea; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; color:#4c6373;">← Zmień</button>
                        </div>
                    </div>
                </form>
            </div>{{-- /manual new audit --}}

            </div>{{-- /section-box-body sec-assign-audit --}}
        </div>{{-- /section-box sec-assign-audit --}}

        </div>{{-- end right column --}}
        </div>{{-- end firma-cols --}}
    </section>

    {{-- Mail confirmation modal --}}
    <div id="modal-mail" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:28px 32px; max-width:440px; width:90%; box-shadow:0 8px 40px rgba(0,0,0,.18);">
            <h3 style="margin:0 0 8px; font-size:17px; color:#0f2330;">📧 Wyślij mail z danymi logowania</h3>
            <p style="margin:0 0 6px; font-size:14px; color:#3b5567;">Użytkownik: <strong id="modal-mail-name"></strong></p>
            <p style="margin:0 0 16px; font-size:13px; color:#4c6373;">Mail zostanie wysłany na: <span id="modal-mail-email" style="font-weight:700; color:#0e89d8;"></span></p>
            <p style="margin:0 0 18px; font-size:12px; color:#92400e; background:#fff8e1; border:1px solid #fde68a; border-radius:8px; padding:8px 12px;">
                ⚠ Zostanie wygenerowane nowe hasło tymczasowe, które zastąpi obecne.
            </p>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeMailModal()" style="padding:8px 18px; border:1px solid #c8d8e6; border-radius:8px; background:#f8fbfd; font-weight:700; cursor:pointer; color:#1d3a50;">Anuluj</button>
                <form id="modal-mail-form" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="padding:8px 18px; background:linear-gradient(130deg,#f59e0b,#d97706); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Wyślij mail i zresetuj hasło</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Remove confirmation modal --}}
    <div id="modal-remove" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:28px 32px; max-width:400px; width:90%; box-shadow:0 8px 40px rgba(0,0,0,.18);">
            <h3 style="margin:0 0 10px; font-size:17px; color:#991b1b;">🗑 Usuń dostęp</h3>
            <p style="margin:0 0 18px; font-size:14px; color:#3b5567;">Czy na pewno chcesz usunąć dostęp użytkownikowi <strong id="modal-remove-name"></strong> do tej firmy?</p>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeRemoveModal()" style="padding:8px 18px; border:1px solid #c8d8e6; border-radius:8px; background:#f8fbfd; font-weight:700; cursor:pointer; color:#1d3a50;">Anuluj</button>
                <form id="modal-remove-form" method="POST" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:8px 18px; background:#dc2626; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Tak, usuń dostęp</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function switchUserTab(tab, btn) {
        document.getElementById('tab-new').style.display      = tab === 'new'      ? '' : 'none';
        document.getElementById('tab-existing').style.display = tab === 'existing' ? '' : 'none';
        document.querySelectorAll('.add-user-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    function toggleSection(id) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('open');
    }
    function toggleAuditEdit(auditId) {
        const el = document.getElementById('audit-edit-' + auditId);
        if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
    function toggleAddUserPanel() {
        const panel  = document.getElementById('add-user-panel');
        const btn    = document.getElementById('add-user-toggle');
        const isOpen = panel.style.display !== 'none';
        panel.style.display = isOpen ? 'none' : '';
        btn.style.background = isOpen ? '#e0f2fe' : '#bae6fd';
        btn.textContent = isOpen ? '➕ Dodaj użytkownika' : '✖ Zamknij';
        if (!isOpen) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function togglePortfolio(inquiryId) {
        const el = document.getElementById('portfolio-' + inquiryId);
        if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
    }

    // ── Real-time chat ────────────────────────────────────────────────────
    const chatBox    = document.getElementById('admin-chat-box');
    const chatInput  = document.getElementById('chat-input');
    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content
                       || '{{ csrf_token() }}';
    let lastMsgId    = {{ $chatMessages->last()?->id ?? 0 }};
    let chatTotal    = {{ $chatMessages->count() }};

    function appendChatBubble(msg) {
        const emptyNote = document.getElementById('chat-empty-note');
        if (emptyNote) emptyNote.remove();

        const wrap = document.createElement('div');
        wrap.className = 'chat-bubble-wrap ' + (msg.is_from_admin ? 'admin' : 'client');
        wrap.dataset.msgId = msg.id;
        wrap.innerHTML =
            `<div class="chat-bubble ${msg.is_from_admin ? 'admin' : 'client'}">${escHtml(msg.message)}</div>` +
            `<div class="chat-meta-line">${escHtml(msg.user_name)} · ${escHtml(msg.created_at)}</div>`;
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
        chatTotal++;
        const lbl = document.getElementById('chat-count-label');
        if (lbl) lbl.textContent = chatTotal + ' wiad.';
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function adminChatSend() {
        const msg = chatInput.value.trim();
        if (!msg) return;
        const btn = document.getElementById('chat-send-btn');
        btn.disabled = true;
        fetch('{{ route('chat.admin.send.ajax', $company) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ message: msg })
        })
        .then(r => r.json())
        .then(data => {
            if (data.id) {
                appendChatBubble(data);
                lastMsgId = Math.max(lastMsgId, data.id);
                chatInput.value = '';
            }
        })
        .catch(() => {})
        .finally(() => { btn.disabled = false; chatInput.focus(); });
    }

    chatInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); adminChatSend(); }
    });

    function pollChatMessages() {
        fetch('{{ route('chat.admin.poll', $company) }}?after=' + lastMsgId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.messages || !data.messages.length) return;
            data.messages.forEach(msg => {
                if (msg.id > lastMsgId) {
                    appendChatBubble(msg);
                    lastMsgId = msg.id;
                    if (!msg.is_from_admin) {
                        // Show unread badge in chat header
                        const badge = document.getElementById('chat-unread-badge');
                        if (badge) { badge.style.display = ''; badge.textContent = '🔔 Nowa wiadomość'; }
                        // Open chat section if closed
                        const sec = document.getElementById('sec-chat');
                        if (sec && !sec.classList.contains('open')) sec.classList.add('open');
                    }
                }
            });
        })
        .catch(() => {});
    }

    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
        setInterval(pollChatMessages, 5000);
    }
    // Auto-open add-user panel when there are validation errors
    @if($errors->hasAny(['first_name','last_name','email','password','role','user_id']))
    (function() {
        const panel = document.getElementById('add-user-panel');
        const btn   = document.getElementById('add-user-toggle');
        if (panel) { panel.style.display = ''; }
        if (btn)   { btn.style.background = '#bae6fd'; btn.textContent = '✖ Zamknij'; }
    })();
    @endif
    function openMailModal(userId, name, email) {
        document.getElementById('modal-mail-name').textContent  = name;
        document.getElementById('modal-mail-email').textContent = email;
        document.getElementById('modal-mail-form').action = '/firmy/{{ $company->id }}/uzytkownik/' + userId + '/mail';
        document.getElementById('modal-mail').style.display = 'flex';
    }
    function closeMailModal() {
        document.getElementById('modal-mail').style.display = 'none';
    }
    function openRemoveModal(userId, name) {
        document.getElementById('modal-remove-name').textContent = name;
        document.getElementById('modal-remove-form').action = '/firmy/{{ $company->id }}/uzytkownik/' + userId;
        document.getElementById('modal-remove').style.display = 'flex';
    }
    function closeRemoveModal() {
        document.getElementById('modal-remove').style.display = 'none';
    }

    // ── Assign audit: 2-step category → subtype ────────────────
    var AUDIT_SUBTYPES = {
        energy: [
            { value: 'general',                 label: '⚡ Audyt energetyczny zakładu' },
            { value: 'compressor_room',          label: '🔧 Kompresory' },
            { value: 'boiler_room',              label: '🔥 Kotłownia' },
            { value: 'drying_room',              label: '🌡️ Suszarnia' },
            { value: 'buildings',                label: '🏢 Budynki' },
            { value: 'technological_processes',  label: '⚙️ Procesy technologiczne' },
        ],
        bc: [
            { value: 'bc_compressor_room',         label: '🔧 Kompresory' },
            { value: 'bc_boiler_room',             label: '🔥 Kotłownia' },
            { value: 'bc_drying_room',             label: '🌡️ Suszarnia' },
            { value: 'bc_buildings',               label: '🏢 Budynki' },
            { value: 'bc_technological_processes', label: '⚙️ Procesy technologiczne' },
        ],
        iso: [],
    };

    var CAT_LABELS = {
        energy: '⚡ Audyty energetyczne',
        bc: '📜 Białe certyfikaty',
        iso: '🏭 ISO 50001',
    };

    var selectedCat = null;

    function selectAuditCat(cat) {
        selectedCat = cat;
        // Highlight selected category button
        document.querySelectorAll('.assign-cat-btn').forEach(function(b) {
            var active = b.dataset.cat === cat;
            b.style.borderColor = active ? '#0e89d8' : '#d5e0ea';
            b.style.background  = active ? '#e0f3ff' : '#f8fbfd';
            b.style.color       = active ? '#0e89d8' : '#1d4f73';
        });

        // Reset form
        document.getElementById('assign-audit-form').style.display = 'none';
        document.getElementById('assign-agent-type').value = '';
        document.getElementById('assign-title').value = '';

        var subtypes = AUDIT_SUBTYPES[cat] || [];

        if (subtypes.length === 0) {
            // ISO: no subtype needed, go straight to form
            document.getElementById('assign-subtype-wrap').style.display = 'none';
            document.getElementById('assign-type-label').textContent = CAT_LABELS[cat];
            document.getElementById('assign-agent-type').value = 'iso50001';
            document.getElementById('assign-title').value = 'ISO 50001 ' + new Date().getFullYear();
            document.getElementById('assign-audit-form').style.display = '';
        } else {
            // Show subtype buttons
            var wrap = document.getElementById('assign-subtype-btns');
            wrap.innerHTML = '';
            subtypes.forEach(function(sub) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'assign-sub-btn';
                btn.dataset.value = sub.value;
                btn.dataset.label = sub.label;
                btn.textContent = sub.label;
                btn.style.cssText = 'padding:9px 16px; border-radius:10px; border:2px solid #d5e0ea; background:#f8fbfd; font-size:13px; font-weight:700; color:#1d4f73; cursor:pointer; transition:.15s;';
                btn.onclick = function() { selectAuditSubtype(this); };
                wrap.appendChild(btn);
            });
            document.getElementById('assign-subtype-wrap').style.display = '';
        }
    }

    function selectAuditSubtype(btn) {
        document.querySelectorAll('.assign-sub-btn').forEach(function(b) {
            b.style.borderColor = '#d5e0ea';
            b.style.background  = '#f8fbfd';
            b.style.color       = '#1d4f73';
        });
        btn.style.borderColor = '#0e89d8';
        btn.style.background  = '#e0f3ff';
        btn.style.color       = '#0e89d8';

        document.getElementById('assign-agent-type').value     = btn.dataset.value;
        document.getElementById('assign-type-label').textContent = btn.dataset.label;
        var title = document.getElementById('assign-title');
        if (!title.value) {
            title.value = btn.dataset.label.replace(/^[\S\s]{2}\s/, '') + ' ' + new Date().getFullYear();
        }
        document.getElementById('assign-audit-form').style.display = '';
        title.focus();
    }

    function resetAuditAssign() {
        selectedCat = null;
        document.querySelectorAll('.assign-cat-btn').forEach(function(b) {
            b.style.borderColor = '#d5e0ea';
            b.style.background  = '#f8fbfd';
            b.style.color       = '#1d4f73';
        });
        document.getElementById('assign-subtype-wrap').style.display = 'none';
        document.getElementById('assign-audit-form').style.display = 'none';
        document.getElementById('assign-agent-type').value = '';
        document.getElementById('assign-title').value = '';
    }

    function rejectAudit(id, title) {
        if (confirm('Odrzucić i usunąć audyt „' + title + '"? Tej operacji nie można cofnąć.')) {
            document.getElementById('reject-form-' + id).submit();
        }
    }

    @if($pendingAudits->isNotEmpty())
    // Auto-open and scroll to assign section on page load
    document.addEventListener('DOMContentLoaded', function() {
        var sec = document.getElementById('sec-assign-audit');
        if (sec) {
            sec.classList.add('open');
            setTimeout(function() { sec.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 200);
        }
    });
    @endif

    // Close modals on backdrop click
    ['modal-mail','modal-remove','modal-delete-company'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });
    </script>

    {{-- Delete company confirmation modal --}}
    @if(auth()->user()->canManageEverything())
    <div id="modal-delete-company" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:28px 32px; max-width:480px; width:92%; box-shadow:0 8px 40px rgba(0,0,0,.22);">
            <h3 style="margin:0 0 10px; font-size:18px; color:#991b1b;">⚠️ Trwałe usunięcie firmy</h3>
            <p style="margin:0 0 8px; font-size:14px; color:#3b5567;">Zostanie usunięte <strong>wszystko</strong>:</p>
            <ul style="margin:0 0 14px; padding-left:20px; font-size:13px; color:#4c6373; line-height:1.8;">
                <li>Firma i jej dane</li>
                <li>Wszyscy użytkownicy przypisani do firmy</li>
                <li>Wszystkie audyty i konwersacje AI</li>
                <li>Wiadomości czatu, zapytania, oferty</li>
            </ul>
            <p style="margin:0 0 6px; font-size:13px; font-weight:700; color:#991b1b;">Tej operacji <u>nie można cofnąć</u>.</p>
            <p style="margin:0 0 12px; font-size:13px; color:#4c6373;">Aby potwierdzić, wpisz nazwę firmy:</p>
            <p style="margin:0 0 8px; font-size:13px; font-family:monospace; background:#fef2f2; border:1px solid #fca5a5; border-radius:6px; padding:6px 10px; color:#991b1b; word-break:break-all;">
                {{ $company->name }}
            </p>
            <input type="text" id="delete-company-confirm-input" placeholder="Wpisz nazwę firmy..."
                style="width:100%; border:1px solid #fca5a5; border-radius:8px; padding:9px 12px; font-size:13px; background:#fff; box-sizing:border-box; margin-bottom:16px; outline:none;"
                oninput="checkDeleteCompanyInput()">
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeDeleteCompanyModal()"
                    style="padding:9px 20px; border:1px solid #c8d8e6; border-radius:8px; background:#f8fbfd; font-weight:700; cursor:pointer; color:#1d3a50;">
                    Anuluj
                </button>
                <form id="delete-company-form" method="POST" action="{{ route('firma.destroy', $company) }}" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="delete-company-btn" disabled
                        style="padding:9px 20px; background:#dc2626; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; opacity:.4; transition:opacity .15s;">
                        Usuń firmę na zawsze
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    const EXPECTED_COMPANY_NAME = @json($company->name);
    function openDeleteCompanyModal() {
        document.getElementById('delete-company-confirm-input').value = '';
        document.getElementById('delete-company-btn').disabled = true;
        document.getElementById('delete-company-btn').style.opacity = '.4';
        document.getElementById('modal-delete-company').style.display = 'flex';
        setTimeout(() => document.getElementById('delete-company-confirm-input').focus(), 100);
    }
    function closeDeleteCompanyModal() {
        document.getElementById('modal-delete-company').style.display = 'none';
    }
    function checkDeleteCompanyInput() {
        const val = document.getElementById('delete-company-confirm-input').value;
        const btn = document.getElementById('delete-company-btn');
        const match = val.trim() === EXPECTED_COMPANY_NAME.trim();
        btn.disabled = !match;
        btn.style.opacity = match ? '1' : '.4';
        btn.style.cursor = match ? 'pointer' : 'default';
    }
    </script>
    @endif

    <x-admin-chat-float :chatMessages="$chatMessages" :company="$company" />
</x-layouts.app>

