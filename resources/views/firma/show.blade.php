<x-layouts.app>
    <style>
        .firma-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom:4px; }
        .firma-meta-inline { display:flex; flex-wrap:wrap; gap:6px 16px; align-items:center; margin:6px 0 4px; }
        .firma-meta-inline .meta-chip { display:inline-flex; align-items:center; gap:4px; font-size:13px; color:#4c6373; }
        .firma-meta-inline .meta-chip strong { color:#0f2330; font-weight:600; }
        .section-box { background:#fff; border:1px solid #d5e0ea; border-radius:14px; margin-top:10px; overflow:hidden; }
        .section-box-toggle { width:100%; display:flex; justify-content:space-between; align-items:center; gap:8px; padding:14px 18px; background:#fafdff; border:none; cursor:pointer; text-align:left; }
        .section-box-toggle:hover { background:#f0f7ff; }
        .section-box-toggle h2 { margin:0; font-size:15px; font-weight:800; color:#0f2330; display:flex; align-items:center; gap:8px; }
        .section-box-toggle .toggle-right { display:flex; align-items:center; gap:8px; }
        .section-box-toggle .chevron { font-size:13px; color:#6b8aa3; transition:transform .2s; }
        .section-box.open .chevron { transform:rotate(180deg); }
        .section-box.open .section-box-toggle { background:#eef6ff; }
        .section-box-body { display:none; padding:16px 18px; border-top:1px solid #e8f1f8; }
        .section-box.open .section-box-body { display:block; }
        .section-box-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
        .section-box-header h2 { margin:0; font-size:17px; font-weight:800; color:#0f2330; }
        .audit-row { display:grid; grid-template-columns:2fr 1fr 1fr 140px auto; gap:12px; align-items:center; padding:12px 14px; border:1px solid #d5e0ea; border-radius:11px; background:#f8fbfd; margin-bottom:8px; }
        .audit-row:hover { background:#eef6ff; }
        .audit-row-title { font-weight:700; color:#0f2330; font-size:14px; }
        .audit-row-meta { font-size:12px; color:#4c6373; }
        .status-pill { display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; }
        .status-wysłany { background:#dbeafe; color:#1e40af; }
        .status-rozpoczęty { background:#d1fae5; color:#065f46; }
        .status-do_analizy { background:#fef3c7; color:#92400e; }
        .status-zwrócony_do_poprawy { background:#fee2e2; color:#991b1b; }
        .status-zaakceptowany { background:#d1fae5; color:#065f46; }
        .status-zakończony { background:#e5e7eb; color:#374151; }
        .status-zafakturowany { background:#ede9fe; color:#5b21b6; }
        .status-zapłacony { background:#d1fae5; color:#064e3b; }
        .status-new, .status-in_progress { background:#e0f2fe; color:#0369a1; }
        .status-completed { background:#e5e7eb; color:#374151; }
        .sec-badge { display:inline-block; font-size:11px; font-weight:700; padding:3px 9px; border-radius:6px; font-family:inherit; }
        .sec-badge-ok  { background:#bae6fd; color:#0369a1; }
        .sec-badge-warn { background:#fef3c7; border:1px solid #fbbf24; color:#92400e; }
        .btn-sm { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; border:none; cursor:pointer; line-height:1; white-space:nowrap; }
        .btn-primary-sm { background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; }
        .btn-secondary-sm { background:#dbe9f5; color:#1d4f73; }
        .btn-danger-sm { background:#fee2e2; color:#991b1b; }
        .inquiry-row { border:1px solid #d5e0ea; border-radius:11px; padding:12px 14px; background:#f8fbfd; margin-bottom:8px; display:flex; flex-direction:column; gap:8px; }
        .inquiry-row.status-new { border-left:4px solid #f59e0b; }
        .inquiry-row.status-accepted { border-left:4px solid #22c55e; }
        .inquiry-row.status-rejected { border-left:4px solid #ef4444; border-color:#fee2e2; background:#fff5f5; }
        .inquiry-row-top { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap; }
        .inquiry-type { font-weight:800; font-size:14px; color:#0f2330; }
        .inquiry-msg { font-size:13px; color:#4c6373; margin:2px 0; white-space:pre-line; }
        .inquiry-meta { font-size:12px; color:#8aa3b5; }
        .inquiry-actions { display:flex; gap:6px; flex-wrap:wrap; align-items:center; }
        .portfolio-picker { background:#eef6ff; border:1px solid #cce3f7; border-radius:10px; padding:12px 14px; margin-top:6px; display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end; }
        .chat-box { background:#f8fbfd; border:1px solid #d5e0ea; border-radius:10px; padding:10px 12px; max-height:340px; overflow-y:auto; margin-bottom:10px; display:flex; flex-direction:column; gap:8px; }
        .chat-bubble-wrap { display:flex; flex-direction:column; }
        .chat-bubble-wrap.admin { align-items:flex-end; }
        .chat-bubble-wrap.client { align-items:flex-start; }
        .chat-bubble { max-width:75%; padding:8px 12px; border-radius:12px; font-size:13px; line-height:1.45; white-space:pre-wrap; word-break:break-word; }
        .chat-bubble.admin { background:#0e89d8; color:#fff; border-bottom-right-radius:3px; }
        .chat-bubble.client { background:#fff; border:1px solid #d5e0ea; color:#1a2e3d; border-bottom-left-radius:3px; }
        .chat-meta-line { font-size:11px; color:#8aa3b5; margin-top:3px; }
        .offer-row-firm { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding:10px 14px; border:1px solid #d5e0ea; border-radius:10px; background:#f8fbfd; margin-bottom:6px; }
        .form-inline { display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; }
        .field-sm { display:flex; flex-direction:column; gap:3px; }
        .field-sm label { font-size:12px; font-weight:700; color:#1d3a50; }
        .field-sm input, .field-sm select { padding:8px 10px; border:1px solid #c8d8e6; border-radius:8px; font-size:13px; background:#f8fbfd; min-width:160px; }
        .credential-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:14px 16px; }
        .credential-box.has { background:#f0f9ff; border-color:#bae6fd; }
        .user-row { display:grid; grid-template-columns:auto 1fr auto auto; gap:12px; align-items:center; padding:10px 14px; border:1px solid #d5e0ea; border-radius:11px; background:#f8fbfd; margin-bottom:8px; }
        .user-row:hover { background:#eef6ff; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(130deg,#0e89d8,#1ba84a); color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800; flex-shrink:0; }
        .role-badge { display:inline-block; padding:3px 9px; border-radius:6px; font-size:11px; font-weight:700; }
        .role-client   { background:#dbeafe; color:#1e40af; }
        .role-auditor  { background:#d1fae5; color:#065f46; }
        .role-admin    { background:#fef3c7; color:#92400e; }
        .add-user-tabs { display:flex; gap:4px; margin-bottom:14px; }
        .add-user-tab { padding:6px 14px; border:1px solid #c8d8e6; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; background:#f8fbfd; color:#1d3a50; transition:.15s; }
        .add-user-tab.active { background:linear-gradient(130deg,#0e89d8,#1ba84a); color:#fff; border-color:transparent; }
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
                <a href="{{ route('dashboard') }}" style="font-size:13px; color:#0e89d8; text-decoration:none;">← Dashboard</a>
                <h1 style="margin:4px 0 0;">{{ $company->name }}</h1>
                @if($company->short_name && $company->short_name !== $company->name)
                    <p class="muted" style="margin:2px 0 0; font-size:13px;">{{ $company->short_name }}</p>
                @endif
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                @if($company->nip)
                    <span style="background:#eaf0f7; border:1px solid #d0dded; border-radius:8px; padding:5px 10px; font-size:12px; font-weight:700; color:#1d3a50; font-family:monospace;">NIP {{ $company->nip }}</span>
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
        <?php $hasAccepted = $inquiries->whereIn('status', ['new','in_review','offer_accepted'])->isNotEmpty(); ?>
        <div class="section-box" id="sec-inquiries" style="border-color:#fbbf24;">
            <button type="button" class="section-box-toggle" onclick="toggleSection('sec-inquiries')" style="border-left:4px solid #fbbf24;">
                <h2>📬 Zapytania klientów</h2>
                <div class="toggle-right">
                    @php($pendingInquiries = $inquiries->whereIn('status', ['new','in_review','offer_accepted'])->count())
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
                        @if(in_array($inquiry->status, ['new', 'in_review']))
                            <form method="POST" action="{{ route('inquiry.accept', $inquiry) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm btn-primary-sm">✅ Przyjęto</button>
                            </form>
                            <form method="POST" action="{{ route('inquiry.reject', $inquiry) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm btn-danger-sm" onclick="return confirm('Odrzucić to zapytanie?')">✗ Odrzuć</button>
                            </form>
                        @endif

                        @if($inquiry->status === 'offer_accepted')
                            <div style="padding:8px 14px; background:#d1fae5; border:1px solid #6ee7b7; border-radius:9px; color:#065f46; font-size:13px; font-weight:700;">
                                ✅ Klient zaakceptował ofertę — <a href="#storeAuditForm" style="color:#047857; text-decoration:underline;">Przydziel audyt poniżej ↓</a>
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
                        {{ $co->status === 'inprogress' ? 'W toku' : 'Portfolio' }}
                    </span>
                    <a href="{{ route('offers.edit', $co) }}" class="btn-sm btn-secondary-sm">✏ Edytuj</a>
                    <form method="POST" action="{{ route('offers.sendToClient', $co) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-sm" style="background:#1ba84a; color:#fff;" onclick="return confirm('Wysłać tę ofertę do klienta?')">📤 Wyślij do klienta</button>
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
                    <div id="chat-empty-note" style="text-align:center; color:#9ab4c5; font-size:13px; padding:20px;">Brak wiadomości.</div>
                @endforelse
            </div>

            <div style="display:flex; gap:8px; align-items:flex-end; margin-top:8px;">
                <textarea id="chat-input" rows="2" placeholder="Wpisz odpowiedź do klienta..." style="flex:1; resize:vertical; padding:8px 10px; border:1px solid #c8d8e6; border-radius:8px; font-size:13px;" required></textarea>
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
                            <div style="font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:#6b8aa3; margin-bottom:2px;">Audytor</div>
                            <div style="font-size:13px;">{{ $audit->auditor?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:#6b8aa3; margin-bottom:2px;">Data</div>
                            <div style="font-size:13px;">{{ $audit->created_at->format('d.m.Y') }}</div>
                        </div>
                        <div>
                            <span class="status-pill {{ $statusClass }}">{{ $audit->statusLabel() }}</span>
                        </div>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="{{ route('firma.audit', [$company, $audit]) }}" class="btn-sm btn-primary-sm">Wejdź →</a>
                            <form method="POST" action="{{ route('firma.destroyAudit', [$company, $audit]) }}" style="margin:0;" onsubmit="return confirm('Usunąć audyt &quot;{{ addslashes($audit->title) }}&quot;? Tej operacji nie można cofnąć.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:5px 10px;">Usuń</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="padding:20px; text-align:center; color:#8a9bac; border:1px dashed #c8d8e6; border-radius:12px; font-size:14px;">
                    Brak przydzielonych audytów. Dodaj pierwszy poniżej.
                </div>
            @endif

            <hr class="divider">

            {{-- Add new audit - collapsible --}}
            <div id="assign-audit-wrap" style="border:1px solid #d5e0ea; border-radius:12px; overflow:hidden;">
                <button type="button" id="assign-audit-toggle-btn" onclick="toggleAssignAuditPanel()"
                    style="width:100%; border:none; background:#f8fbfd; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; text-align:left; transition:background .15s;">
                    <span style="font-size:15px; font-weight:700; color:#1d3a50;">Przydziel nowy audyt</span>
                    <span id="assign-audit-icon" style="font-size:22px; font-weight:300; color:#0e89d8; line-height:1;">+</span>
                </button>
                <div id="assign-audit-panel" id="storeAuditForm" style="display:none; padding:16px 18px; border-top:1px solid #e8f1f8;">
                    <p style="margin:0 0 12px; font-size:13px; color:#4c6373;">Wybierz rodzaj audytu z listy — kliknij kartę, uzupełnij nazwę i kliknij Przydziel.</p>

                    @if ($errors->has('title') || $errors->has('audit_type_id') || $errors->has('agent_type'))
                        <div style="margin-bottom:10px; padding:8px 12px; background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; color:#991b1b; font-size:13px;">
                            @foreach ($errors->all() as $err)<div>⚠ {{ $err }}</div>@endforeach
                        </div>
                    @endif

                    @if($auditTypes->isEmpty())
                        <p style="color:#8a9bac; font-size:13px; font-style:italic; margin:0 0 12px;">Brak zdefiniowanych rodzajów audytów. <a href="{{ route('audits.types', ['tab' => 'energetyczne']) }}" style="color:#0e89d8;">Dodaj rodzaj audytu →</a></p>
                    @else
                        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:10px; margin-bottom:14px;" id="audit-type-cards">
                            @foreach($auditTypes as $type)
                            <button type="button"
                                class="audit-type-pick-card"
                                data-id="{{ $type->id }}"
                                data-name="{{ $type->name }}"
                                data-category="{{ $type->category }}"
                                data-agent-type="{{ $type->agent_type ?? '' }}"
                                onclick="selectAuditTypeCard(this)"
                                style="border:2px solid #d5e0ea; border-radius:12px; padding:12px 14px; background:#f8fbfd; cursor:pointer; text-align:left; transition:.15s;">
                                <div style="font-size:13px; font-weight:800; color:#0f2330; margin-bottom:4px;">{{ $type->name }}</div>
                                <div style="font-size:11px; color:#6b8aa3;">
                                    {{ $type->sections->count() }} {{ $type->sections->count() === 1 ? 'sekcja' : ($type->sections->count() < 5 ? 'sekcje' : 'sekcji') }}
                                </div>
                            </button>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('firma.storeAudit', $company) }}" id="assign-audit-form" style="display:none;">
                        @csrf
                        <input type="hidden" name="audit_type_id" id="selected-audit-type-id" value="{{ old('audit_type_id') }}">
                        <div style="padding:12px 14px; border:2px solid #0e89d8; border-radius:12px; background:#f0f8ff; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                            <div style="font-size:13px; font-weight:700; color:#0f2330; flex:1 0 100%; margin-bottom:4px;">
                                Wybrany typ: <span id="selected-audit-type-name" style="color:#0e89d8;"></span>
                            </div>
                            <div class="field-sm" style="flex:2;">
                                <label>Nazwa audytu *</label>
                                <input type="text" name="title" id="assign-audit-title" value="{{ old('title') }}" required placeholder="np. Audyt energetyczny 2026">
                            </div>
                            <div class="field-sm" style="flex:1; min-width:180px;">
                                <label>Agent AI *</label>
                                <select name="agent_type" id="assign-agent-type" required>
                                    <option value="">— wybierz agenta —</option>
                                    <optgroup label="Audyty energetyczne" data-category="energy">
                                        <option value="general"                 {{ old('agent_type') === 'general'                 ? 'selected' : '' }}>Ogólnie</option>
                                        <option value="compressor_room"         {{ old('agent_type') === 'compressor_room'         ? 'selected' : '' }}>Sprężarkownia</option>
                                        <option value="boiler_room"             {{ old('agent_type') === 'boiler_room'             ? 'selected' : '' }}>Kotłownia</option>
                                        <option value="drying_room"             {{ old('agent_type') === 'drying_room'             ? 'selected' : '' }}>Suszarnia</option>
                                        <option value="buildings"               {{ old('agent_type') === 'buildings'               ? 'selected' : '' }}>Budynki</option>
                                        <option value="technological_processes" {{ old('agent_type') === 'technological_processes' ? 'selected' : '' }}>Procesy technologiczne</option>
                                    </optgroup>
                                    <optgroup label="ISO 50001" data-category="iso">
                                        <option value="iso50001"                {{ old('agent_type') === 'iso50001'                ? 'selected' : '' }}>ISO 50001</option>
                                    </optgroup>
                                    <optgroup label="Białe certyfikaty" data-category="white_cert">
                                        <option value="bc_general"                 {{ old('agent_type') === 'bc_general'                 ? 'selected' : '' }}>Ogólnie</option>
                                        <option value="bc_compressor_room"         {{ old('agent_type') === 'bc_compressor_room'         ? 'selected' : '' }}>Sprężarkownia</option>
                                        <option value="bc_boiler_room"             {{ old('agent_type') === 'bc_boiler_room'             ? 'selected' : '' }}>Kotłownia</option>
                                        <option value="bc_drying_room"             {{ old('agent_type') === 'bc_drying_room'             ? 'selected' : '' }}>Suszarnia</option>
                                        <option value="bc_buildings"               {{ old('agent_type') === 'bc_buildings'               ? 'selected' : '' }}>Budynki</option>
                                        <option value="bc_technological_processes" {{ old('agent_type') === 'bc_technological_processes' ? 'selected' : '' }}>Procesy technologiczne</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="field-sm" style="flex:1;">
                                <label>Audytor</label>
                                <select name="auditor_id">
                                    <option value="">Brak</option>
                                    @foreach($auditors as $auditor)
                                        <option value="{{ $auditor->id }}" @selected(old('auditor_id') == $auditor->id)>{{ $auditor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:flex; gap:6px; align-self:flex-end;">
                                <button type="submit" class="btn-sm btn-primary-sm" style="padding:8px 16px;">Przydziel audyt</button>
                                <button type="button" class="btn-sm btn-secondary-sm" onclick="clearAuditTypeSelection()" style="padding:8px 12px;">Zmień</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
            function toggleAssignAuditPanel() {
                const panel = document.getElementById('assign-audit-panel');
                const icon  = document.getElementById('assign-audit-icon');
                const btn   = document.getElementById('assign-audit-toggle-btn');
                const isOpen = panel.style.display !== 'none';
                if (isOpen) {
                    panel.style.display = 'none';
                    icon.textContent    = '+';
                    btn.style.background = '#f8fbfd';
                } else {
                    panel.style.display = '';
                    icon.textContent    = '−';
                    btn.style.background = '#eef6ff';
                }
            }
            function selectAuditTypeCard(btn) {
                document.querySelectorAll('.audit-type-pick-card').forEach(function(c) {
                    c.style.borderColor = '#d5e0ea';
                    c.style.background  = '#f8fbfd';
                    c.style.color       = '';
                });
                btn.style.borderColor = '#0e89d8';
                btn.style.background  = '#e0f3ff';
                document.getElementById('selected-audit-type-id').value = btn.dataset.id;
                document.getElementById('selected-audit-type-name').textContent = btn.dataset.name;
                const titleInput = document.getElementById('assign-audit-title');
                if (!titleInput.value) {
                    titleInput.value = btn.dataset.name + ' ' + new Date().getFullYear();
                }

                // Filter agent options by category & auto-select when only one valid option
                const category  = btn.dataset.category  || '';
                const agentType = btn.dataset.agentType || '';
                const sel = document.getElementById('assign-agent-type');

                // Show/hide optgroups based on category
                sel.querySelectorAll('optgroup').forEach(function(grp) {
                    grp.style.display = (category && grp.dataset.category !== category) ? 'none' : '';
                });

                // Auto-select the specific agent when agent_type is set
                if (agentType) {
                    sel.value = agentType;
                } else {
                    sel.value = '';
                }

                document.getElementById('assign-audit-form').style.display = '';
            }
            function clearAuditTypeSelection() {
                document.querySelectorAll('.audit-type-pick-card').forEach(function(c) {
                    c.style.borderColor = '#d5e0ea';
                    c.style.background  = '#f8fbfd';
                });
                document.getElementById('selected-audit-type-id').value = '';
                document.getElementById('assign-audit-title').value = '';
                document.getElementById('assign-audit-form').style.display = 'none';
            }
            @if($errors->has('title') || $errors->has('audit_type_id') || $errors->has('agent_type') || old('audit_type_id'))
            (function() {
                const panel = document.getElementById('assign-audit-panel');
                const icon  = document.getElementById('assign-audit-icon');
                const btn   = document.getElementById('assign-audit-toggle-btn');
                panel.style.display  = '';
                icon.textContent     = '−';
                btn.style.background = '#eef6ff';
                @if(old('audit_type_id'))
                const card = document.querySelector('.audit-type-pick-card[data-id="{{ old('audit_type_id') }}"]');
                if (card) selectAuditTypeCard(card);
                @endif
            })();
            @endif
            </script>
            </div>
        </div>
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
    // Close modals on backdrop click
    ['modal-mail','modal-remove'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    });
    </script>
</x-layouts.app>

