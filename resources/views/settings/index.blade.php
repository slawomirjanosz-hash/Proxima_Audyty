<x-layouts.app>

    {{-- Page header --}}
    <section class="panel" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 4px; font-size:20px; font-weight:800; color:#0f2330;">{{ __('ui.settings.header.title') }}</h2>
            <p class="muted" style="margin:0; font-size:13px;">{{ __('ui.settings.header.subtitle') }}</p>
        </div>
        @if(!$canManage)
            <div style="background:#eaf2ff; color:#154f93; border:1px solid #cfe0ff; border-radius:10px; padding:8px 14px; font-size:13px; font-weight:600;">
                {{ __('ui.settings.header.read_only') }}
            </div>
        @endif
    </section>

    @if ($errors->any())
        <section class="panel" style="margin-top:14px; background:#fff3f3; border-color:#ffc9c9; color:#9f1f1f;">
            {{ $errors->first() }}
        </section>
    @endif

    <style>
        .accordion { margin-top: 14px; display: flex; flex-direction: column; gap: 10px; }
        .acc-item { background: #fff; border: 1px solid #d5e0ea; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(14,55,85,.05); }
        .acc-header {
            display: flex; align-items: center; gap: 14px;
            padding: 16px 20px; cursor: pointer; user-select: none;
            background: #fff;
            border: none; width: 100%; text-align: left;
            font-family: inherit;
        }
        .acc-header:hover { background: #f5faff; }
        .acc-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: grid; place-items: center;
            font-size: 16px; flex-shrink: 0;
        }
        .acc-icon-blue  { background: rgba(14,137,216,.12); color: #0e89d8; }
        .acc-icon-green { background: rgba(27,168,74,.12);  color: #1ba84a; }
        .acc-icon-amber { background: rgba(217,119,6,.12);  color: #d97706; }
        .acc-title { flex: 1; }
        .acc-title strong { display: block; font-size: 15px; font-weight: 700; color: #0f2330; }
        .acc-title span   { font-size: 12px; color: #4c6373; }
        .acc-chevron { font-size: 18px; color: #9ab4c5; transition: transform .2s; }
        .acc-item.open .acc-chevron { transform: rotate(180deg); }
        .acc-body { display: none; padding: 0 20px 20px; border-top: 1px solid #e8f0f7; }
        .acc-item.open .acc-body { display: block; }
        .acc-body table { margin-top: 14px; }
        .acc-body th { font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: #4c6373; background: #f7fafc; padding: 9px 10px; }
        .acc-body td { padding: 10px; font-size: 13px; vertical-align: middle; border-bottom: 1px solid #edf2f7; }
        .acc-body tr:last-child td { border-bottom: none; }
        .acc-note { margin-top: 14px; padding: 10px 14px; background: #f0f7ff; border-radius: 10px; border-left: 3px solid #0e89d8; font-size: 13px; color: #2a5a8a; }
        .price-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 0; border-bottom: 1px solid #edf2f7; flex-wrap: wrap; }
        .price-row:last-child { border-bottom: none; }
        .price-badge { display: inline-flex; align-items: center; gap: 8px; }
        .price-badge .tag { font-size: 10px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase; padding: 3px 8px; border-radius: 5px; }
        .tag-pro     { background: #1ba84a; color: #fff; }
        .tag-express { background: #1585d0; color: #fff; }
        .tag-iso     { background: #d97706; color: #fff; }
        .tag-micro   { background: #6d28d9; color: #fff; }
        .tag-impl    { background: #ea580c; color: #fff; }
        .price-val { font-weight: 700; font-size: 14px; color: #0c5f28; }
        .edit-user-btn { padding: 7px 10px; font-size: 12px; }
        .permissions-row td { background: #fbfdff; border-bottom: 1px solid #dfeaf3; }
        .permissions-panel { padding: 10px 2px 4px; }
        .permissions-grid { display: grid; grid-template-columns: repeat(3, minmax(160px, 1fr)); gap: 8px 14px; margin: 10px 0 14px; }
        .perm-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #0f2330; }
        .perm-item input { margin: 0; }
        .permissions-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn-secondary { background: #dbe9f5; color: #1d4f73; }
        .audit-type-card { border: 1px solid #dfeaf3; border-radius: 10px; padding: 10px; margin-top: 10px; background:#fbfdff; }
        .audit-type-section { margin-top: 8px; padding: 8px; border:1px solid #edf2f7; border-radius:8px; background:#fff; }
        .audit-builder { display:none; margin-top:14px; padding:12px; border:1px solid #dfeaf3; border-radius:10px; background:#f9fcff; }
        @media (max-width: 960px) {
            .permissions-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>

    <div class="accordion">

        {{-- 1. UŻYTKOWNICY --}}
        <div class="acc-item open" id="acc-users">
            <button class="acc-header" onclick="toggleAcc('acc-users')">
                <div class="acc-icon acc-icon-blue">👤</div>
                <div class="acc-title">
                    <strong>{{ __('ui.settings.users.title') }}</strong>
                    <span>{{ __('ui.settings.users.subtitle') }}</span>
                </div>
                <span class="acc-chevron">&#9660;</span>
            </button>
            <div class="acc-body">
                @if($canManage)
                    <button type="button" class="edit-user-btn" onclick="toggleAddUserForm()">{{ __('ui.settings.users.add_button') }}</button>
                    <form id="add-user-form" method="POST" action="{{ route('settings.user-store') }}" style="display:none; margin-top:14px; padding:12px; border:1px solid #dfeaf3; border-radius:10px; background:#f9fcff; flex-wrap:wrap; gap:10px; align-items:end;">
                        @csrf
                        <input type="hidden" name="open_add_user" value="1">

                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.first_name') }}</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.last_name') }}</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.columns.email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.password') }}</label>
                            <input type="password" name="password" required>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.role_label') }}</label>
                            <select name="role" required>
                                @foreach ([\App\Enums\UserRole::Admin, \App\Enums\UserRole::Auditor, \App\Enums\UserRole::Client] as $role)
                                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:flex; flex-direction:column; min-width:220px;">
                            <label style="font-size:12px; font-weight:700; color:#4c6373;">Uprawnienia zakładek</label>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                @foreach($tabLabels as $tabKey => $tabLabel)
                                    <label style="font-size:12px;">
                                        <input type="checkbox" name="tabs[{{ $tabKey }}]" value="1" @checked(old('tabs.'.$tabKey))>
                                        {{ $tabLabel }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit">{{ __('ui.settings.users.add_button') }}</button>
                    </form>
                @endif

                <table>
                    <thead>
                        <tr>
                            <th>{{ __('ui.settings.users.columns.name') }}</th>
                            <th>{{ __('ui.settings.users.columns.email') }}</th>
                            <th>{{ __('ui.settings.users.columns.role') }}</th>
                            <th>{{ __('ui.settings.users.columns.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php($currentTabs = $user->resolvedTabPermissions())
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td style="color:#4c6373;">{{ $user->email }}</td>
                                <td>
                                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:6px;background:#eaf2ff;color:#154f93;letter-spacing:.5px;text-transform:uppercase;">
                                        {{ $user->role->label() }}
                                    </span>
                                    @if($user->role === \App\Enums\UserRole::Admin && $user->also_auditor)
                                        <span style="font-size:11px;font-weight:700;padding:3px 7px;border-radius:6px;background:#d1fae5;color:#065f46;letter-spacing:.5px;text-transform:uppercase;margin-left:4px;">+ Audytor</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    @if($canManage)
                                        <div id="user-normal-actions-{{ $user->id }}" style="display:flex; gap:4px; align-items:center;">
                                            <button type="button" class="edit-user-btn" onclick="toggleUserEditor({{ $user->id }})">{{ __('ui.settings.actions.edit') }}</button>
                                            <form method="POST" action="{{ route('settings.user-destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Czy na pewno usunąć użytkownika?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="edit-user-btn" style="color:#b91c1c;">Usuń</button>
                                            </form>
                                        </div>
                                        <div id="user-edit-actions-{{ $user->id }}" style="display:none; gap:4px; align-items:center;">
                                            <button type="submit" form="user-access-form-{{ $user->id }}" class="edit-user-btn">Zapisz</button>
                                            <button type="button" class="btn-secondary" onclick="toggleUserEditor({{ $user->id }})">{{ __('ui.settings.actions.cancel') }}</button>
                                        </div>
                                    @else
                                        <span style="color:#9ab4c5;font-size:13px;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if($canManage)
                                <tr id="user-editor-{{ $user->id }}" class="permissions-row" style="display:none;">
                                    <td colspan="4" style="background:#d9e4ed; padding:12px 16px;">
                                        <div class="permissions-panel">
                                            <form id="user-access-form-{{ $user->id }}" method="POST" action="{{ route('settings.user-access', $user) }}">
                                                @csrf
                                                @method('PATCH')

                                                {{-- All fields in one row --}}
                                                <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:12px;">
                                                    <div style="flex:1; min-width:90px;">
                                                        <label for="first-name-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.first_name') }}</label>
                                                        <input id="first-name-{{ $user->id }}" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" style="width:100%;">
                                                    </div>
                                                    <div style="flex:1.2; min-width:100px;">
                                                        <label for="last-name-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.last_name') }}</label>
                                                        <input id="last-name-{{ $user->id }}" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" style="width:100%;">
                                                    </div>
                                                    <div style="flex:0.8; min-width:70px;">
                                                        <label for="short-name-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.short_name') }}</label>
                                                        <input id="short-name-{{ $user->id }}" type="text" name="short_name" maxlength="32" value="{{ old('short_name', $user->short_name ?? (\Illuminate\Support\Str::substr($user->first_name,0,3).\Illuminate\Support\Str::substr($user->last_name,0,3))) }}" style="width:100%;">
                                                    </div>
                                                    <div style="flex:1.8; min-width:140px;">
                                                        <label for="email-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.columns.email') }}</label>
                                                        <input id="email-{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%;">
                                                    </div>
                                                    <div style="flex:1; min-width:90px;">
                                                        <label for="phone-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.phone') }}</label>
                                                        <input id="phone-{{ $user->id }}" type="text" name="phone" value="{{ old('phone', $user->phone) }}" style="width:100%;">
                                                    </div>
                                                    <div style="flex:1.2; min-width:100px;">
                                                        <label for="password-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.password') }}</label>
                                                        <input id="password-{{ $user->id }}" type="password" name="password" placeholder="{{ __('ui.settings.users.password_placeholder') }}" style="width:100%;">
                                                    </div>
                                                    <div style="flex:1; min-width:90px;">
                                                        <label for="role-{{ $user->id }}" style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.users.role_label') }}</label>
                                                        <select id="role-{{ $user->id }}" name="role" style="width:100%;" onchange="toggleAlsoAuditor({{ $user->id }}, this.value)">
                                                            @foreach ([\App\Enums\UserRole::Admin, \App\Enums\UserRole::Auditor, \App\Enums\UserRole::Client] as $role)
                                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div id="also-auditor-wrap-{{ $user->id }}" style="flex:0 0 auto; align-self:flex-end; padding-bottom:6px; display:{{ $user->role === \App\Enums\UserRole::Admin ? 'flex' : 'none' }}; align-items:center; gap:5px;">
                                                        <input type="checkbox" id="also-auditor-{{ $user->id }}" name="also_auditor" value="1" @checked(old('also_auditor', $user->also_auditor))>
                                                        <label for="also-auditor-{{ $user->id }}" style="font-size:11px; font-weight:700; color:#065f46; cursor:pointer; white-space:nowrap;">Pełni też rolę audytora</label>
                                                    </div>
                                                </div>

                                                <div class="permissions-grid">
                                                    @foreach($tabLabels as $tabKey => $tabLabel)
                                                        <label class="perm-item">
                                                            <input type="checkbox" name="tabs[{{ $tabKey }}]" value="1" @checked($currentTabs[$tabKey] ?? false)>
                                                            <span>{{ $tabLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. KLIENT --}}
        <div class="acc-item" id="acc-clients">
            <button class="acc-header" onclick="toggleAcc('acc-clients')">
                <div class="acc-icon acc-icon-green">🏢</div>
                <div class="acc-title">
                    <strong>{{ __('ui.settings.clients.title') }}</strong>
                    <span>{{ __('ui.settings.clients.subtitle') }}</span>
                </div>
                <span class="acc-chevron">&#9660;</span>
            </button>
            <div class="acc-body">
                @if($canManage)
                    <div style="display:flex; justify-content:flex-end; margin-bottom:4px;">
                        <button type="button" class="edit-user-btn" onclick="toggleAddCompanyForm()">{{ __('ui.settings.clients.add_button') }}</button>
                    </div>
                    <form id="add-company-form" method="POST" action="{{ route('settings.company-store') }}" enctype="multipart/form-data" style="display:{{ old('open_add_company') ? 'block' : 'none' }}; margin-top:14px; padding:16px; border:1px solid #dfeaf3; border-radius:10px; background:#f9fcff;">
                        @csrf
                        <input type="hidden" name="open_add_company" value="1">

                        {{-- Rząd 1: Wyszukiwanie po NIP --}}
                        <div style="display:flex; align-items:flex-end; gap:8px; flex-wrap:wrap; padding-bottom:14px; margin-bottom:14px; border-bottom:1px solid #dfeaf3;">
                            <div style="flex:0 0 auto;">
                                <label for="new-company-nip" style="display:block; font-size:12px; font-weight:700; color:#4c6373;">NIP — znajdź firmę w GUS</label>
                                <input id="new-company-nip" type="text" name="nip" value="{{ old('nip') }}" placeholder="np. 525-244-57-67" style="width:200px;">
                            </div>
                            <button type="button" class="btn-secondary" onclick="lookupCompanyByNip()" style="flex:0 0 auto; height:38px;">{{ __('ui.settings.clients.lookup.button') }}</button>
                            <span id="nip-lookup-message" style="font-size:12px; color:#4c6373; align-self:center;"></span>
                        </div>

                        {{-- Rząd 1: Nazwa + Skrót --}}
                        <div style="display:flex; gap:12px; margin-bottom:10px;">
                            <div style="flex:3; min-width:150px;">
                                <label for="new-company-name" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">{{ __('ui.settings.clients.columns.company') }} *</label>
                                <input id="new-company-name" type="text" name="name" value="{{ old('name') }}" required style="width:100%;">
                            </div>
                            <div style="flex:1; min-width:80px;">
                                <label for="new-company-short-name" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Skrót</label>
                                <input id="new-company-short-name" type="text" name="short_name" value="{{ old('short_name') }}" maxlength="32" placeholder="auto" style="width:100%;">
                            </div>
                        </div>
                        {{-- Rząd 2: Adres + Kontakt + Opiekun --}}
                        <div style="display:flex; gap:12px; margin-bottom:10px; flex-wrap:wrap;">
                            <div style="flex:2; min-width:130px;">
                                <label for="new-company-street" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Ulica</label>
                                <input id="new-company-street" type="text" name="street" value="{{ old('street') }}" style="width:100%;">
                            </div>
                            <div style="flex:1; min-width:90px;">
                                <label for="new-company-postal" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Kod pocztowy</label>
                                <input id="new-company-postal" type="text" name="postal_code" value="{{ old('postal_code') }}" style="width:100%;">
                            </div>
                            <div style="flex:1.5; min-width:110px;">
                                <label for="new-company-city" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Miejscowość</label>
                                <input id="new-company-city" type="text" name="city" value="{{ old('city') }}" style="width:100%;">
                            </div>
                            <div style="flex:1; min-width:100px;">
                                <label for="new-company-phone" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Telefon</label>
                                <input id="new-company-phone" type="text" name="phone" value="{{ old('phone') }}" style="width:100%;">
                            </div>
                            <div style="flex:1.5; min-width:130px;">
                                <label for="new-company-email" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Email</label>
                                <input id="new-company-email" type="email" name="email" value="{{ old('email') }}" style="width:100%;">
                            </div>
                            <div style="flex:1.5; min-width:130px;">
                                <label for="new-company-auditor" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Opiekun</label>
                                <select id="new-company-auditor" name="auditor_id" style="width:100%;">
                                    <option value="">— brak —</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" @selected((string) old('auditor_id') === (string) $admin->id)>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Rząd 3: Logo + Opis + Zapisz --}}
                        <div style="display:flex; gap:12px; align-items:flex-end;">
                            <div style="flex:0 0 auto;">
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Logo firmy</label>
                                <input type="file" name="logo" accept="image/*" style="font-size:13px; display:block;">
                            </div>
                            <div style="flex:1; min-width:150px;">
                                <label for="new-company-description" style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Opis</label>
                                <input id="new-company-description" type="text" name="description" value="{{ old('description') }}" style="width:100%;">
                            </div>
                            <div style="flex:0 0 auto;">
                                <button type="submit">{{ __('ui.settings.clients.add_button') }}</button>
                            </div>
                        </div>
                    </form>
                @endif

                <div style="overflow-x:auto; margin-top:14px;">
                    <table id="companies-table" style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead id="companies-thead">
                            <tr style="background:#eaf2f8; color:#4c6373;">
                                <th style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; width:52px;">Logo</th>
                                <th onclick="sortCompaniesTable(1)" style="padding:8px 10px; text-align:left; font-weight:700; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">Nazwa firmy <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th onclick="sortCompaniesTable(2)" style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">Skrót <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th onclick="sortCompaniesTable(3)" style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">NIP <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th onclick="sortCompaniesTable(4)" style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">Miasto <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th onclick="sortCompaniesTable(5)" style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">Telefon <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th onclick="sortCompaniesTable(6)" style="padding:8px 10px; text-align:left; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3; cursor:pointer; user-select:none;">Email <span class="sort-ind" style="font-size:10px; color:#9ab4c5;">↕</span></th>
                                <th style="padding:8px 10px; text-align:center; font-weight:700; white-space:nowrap; border-bottom:2px solid #c9d7e3;">Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="companies-tbody">
                            @foreach($companies as $company)
                                <tr data-company-id="{{ $company->id }}"
                                    data-col1="{{ strtolower($company->name) }}"
                                    data-col2="{{ strtolower($company->short_name) }}"
                                    data-col3="{{ $company->nip }}"
                                    data-col4="{{ strtolower($company->city) }}"
                                    data-col5="{{ $company->phone }}"
                                    data-col6="{{ strtolower($company->email) }}"
                                    style="border-bottom:1px solid #e8f0f7;">
                                    <td style="padding:8px 10px;">
                                        @if($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" alt="logo" style="width:40px; height:40px; object-fit:contain; border-radius:6px; border:1px solid #dfeaf3;">
                                        @else
                                            <div style="width:40px; height:40px; background:#eaf2f8; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:18px; border:1px solid #dfeaf3;">🏢</div>
                                        @endif
                                    </td>
                                    <td style="padding:8px 10px; font-weight:600; color:#0f2330;">{{ $company->name }}</td>
                                    <td style="padding:8px 10px; color:#4c6373;">{{ $company->short_name ?: '—' }}</td>
                                    <td style="padding:8px 10px; color:#4c6373; white-space:nowrap;">{{ $company->nip ?: '—' }}</td>
                                    <td style="padding:8px 10px; color:#4c6373;">{{ $company->city ?: '—' }}</td>
                                    <td style="padding:8px 10px; color:#4c6373; white-space:nowrap;">{{ $company->phone ?: '—' }}</td>
                                    <td style="padding:8px 10px; color:#4c6373;">{{ $company->email ?: '—' }}</td>
                                    <td style="padding:8px 10px; white-space:nowrap;">
                                        <div style="display:flex; gap:4px; align-items:center; justify-content:center; flex-wrap:nowrap;">
                                            @if($canManage)
                                                <button type="button" class="edit-user-btn" onclick="toggleCompanyEditor({{ $company->id }})">Edytuj</button>
                                                <button type="button" class="btn-secondary" onclick="toggleCompanyUsers({{ $company->id }})" title="Użytkownicy">
                                                    👥 {{ $company->assignedUsers->count() > 0 ? $company->assignedUsers->count() : '' }}
                                                </button>
                                                <form method="POST" action="{{ route('settings.company-destroy', $company) }}" style="display:inline;" onsubmit="return confirm('Czy na pewno usunąć firmę?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-secondary" style="color:#b91c1c;">Usuń</button>
                                                </form>
                                            @else
                                                <button type="button" class="btn-secondary" onclick="toggleCompanyUsers({{ $company->id }})" title="Użytkownicy">
                                                    👥 {{ $company->assignedUsers->count() > 0 ? $company->assignedUsers->count() : '' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                {{-- Users dropdown row --}}
                                <tr id="company-users-{{ $company->id }}" style="display:none; background:#f5faff;">
                                    <td colspan="8" style="padding:0 10px 0 52px;">
                                        <div style="padding:8px 0; border-top:1px solid #dfeaf3;">
                                            @if($company->assignedUsers->isNotEmpty())
                                                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                                                    <thead>
                                                        <tr style="color:#9ab4c5; font-size:11px; font-weight:700; text-transform:uppercase;">
                                                            <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Imię i nazwisko</th>
                                                            <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Skrót</th>
                                                            <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Stanowisko</th>
                                                            <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Email</th>
                                                            <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Telefon</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($company->assignedUsers as $u)
                                                            <tr style="border-bottom:1px solid #edf4fb;">
                                                                <td style="padding:5px 8px; color:#0f2330; font-weight:600; white-space:nowrap;">{{ trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->name }}</td>
                                                                <td style="padding:5px 8px; color:#4c6373; white-space:nowrap;">{{ $u->short_name ?: '—' }}</td>
                                                                <td style="padding:5px 8px; color:#4c6373;">{{ $u->position ?: '—' }}</td>
                                                                <td style="padding:5px 8px; color:#4c6373;">{{ $u->email }}</td>
                                                                <td style="padding:5px 8px; color:#4c6373; white-space:nowrap;">{{ $u->phone ?: '—' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span style="font-size:12px; color:#9ab4c5; padding:4px 8px; display:block;">Brak przypisanych użytkowników.</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @if($canManage)
                                    <tr id="company-editor-{{ $company->id }}" class="permissions-row" style="display:{{ old('open_company_editor_id') == $company->id ? 'table-row' : 'none' }};">
                                        <td colspan="8" style="padding:0;">
                                            <div class="permissions-panel">

                                                {{-- Company edit form --}}
                                                <form method="POST" action="{{ route('settings.company-update', $company) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="open_company_editor_id" value="{{ $company->id }}">
                                                    {{-- Rząd 1: Nazwa + Skrót + NIP --}}
                                                    <div style="display:flex; gap:12px; margin-bottom:10px;">
                                                        <div style="flex:3; min-width:150px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Nazwa *</label>
                                                            <input type="text" name="name" value="{{ old('open_company_editor_id') == $company->id ? old('name', $company->name) : $company->name }}" required style="width:100%;">
                                                        </div>
                                                        <div style="flex:1; min-width:80px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Skrót</label>
                                                            <input type="text" name="short_name" maxlength="32" value="{{ old('open_company_editor_id') == $company->id ? old('short_name', $company->short_name) : $company->short_name }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1.2; min-width:90px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">NIP</label>
                                                            <input type="text" name="nip" value="{{ old('open_company_editor_id') == $company->id ? old('nip', $company->nip) : $company->nip }}" style="width:100%;">
                                                        </div>
                                                    </div>
                                                    {{-- Rząd 2: Adres + Kontakt + Opiekun --}}
                                                    <div style="display:flex; gap:12px; margin-bottom:10px; flex-wrap:wrap;">
                                                        <div style="flex:2; min-width:130px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Ulica</label>
                                                            <input type="text" name="street" value="{{ old('open_company_editor_id') == $company->id ? old('street', $company->street) : $company->street }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1; min-width:90px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Kod pocztowy</label>
                                                            <input type="text" name="postal_code" value="{{ old('open_company_editor_id') == $company->id ? old('postal_code', $company->postal_code) : $company->postal_code }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1.5; min-width:110px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Miejscowość</label>
                                                            <input type="text" name="city" value="{{ old('open_company_editor_id') == $company->id ? old('city', $company->city) : $company->city }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1; min-width:100px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Telefon</label>
                                                            <input type="text" name="phone" value="{{ old('open_company_editor_id') == $company->id ? old('phone', $company->phone) : $company->phone }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1.5; min-width:130px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Email</label>
                                                            <input type="email" name="email" value="{{ old('open_company_editor_id') == $company->id ? old('email', $company->email) : $company->email }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:1.5; min-width:130px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Opiekun</label>
                                                            <select name="auditor_id" style="width:100%;">
                                                                <option value="">— brak —</option>
                                                                @foreach($admins as $admin)
                                                                    <option value="{{ $admin->id }}" @selected($company->auditor_id === $admin->id)>{{ $admin->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    {{-- Rząd 3: Logo + Opis + Zapisz --}}
                                                    <div style="display:flex; gap:12px; align-items:flex-end;">
                                                        <div style="flex:0 0 auto;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Logo firmy</label>
                                                            @if($company->logo)
                                                                <img src="{{ asset('storage/' . $company->logo) }}" alt="logo" style="width:28px; height:28px; object-fit:contain; border-radius:4px; border:1px solid #dfeaf3; display:block; margin-bottom:3px;">
                                                            @endif
                                                            <input type="file" name="logo" accept="image/*" style="font-size:13px; display:block;">
                                                        </div>
                                                        <div style="flex:1; min-width:150px;">
                                                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:3px;">Opis</label>
                                                            <input type="text" name="description" value="{{ old('open_company_editor_id') == $company->id ? old('description', $company->description) : $company->description }}" style="width:100%;">
                                                        </div>
                                                        <div style="flex:0 0 auto;">
                                                            <button type="submit">{{ __('ui.settings.actions.save') }}</button>
                                                        </div>
                                                    </div>
                                                </form>

                                                {{-- Users section --}}
                                                <div style="margin-top:16px; border-top:1px solid #dfeaf3; padding-top:14px;">
                                                    <strong style="font-size:13px; color:#0f2330; display:block; margin-bottom:10px;">Użytkownicy klienta</strong>

                                                    {{-- Add new client user form --}}
                                                    <form id="company-contacts-form-{{ $company->id }}" method="POST" action="{{ route('settings.company-client-store', $company) }}" style="display:{{ old('open_company_editor_id') == $company->id && old('open_company_contacts') ? 'block' : 'none' }}; margin-bottom:12px; padding:12px; background:#f9fcff; border:1px solid #dfeaf3; border-radius:8px;">
                                                        @csrf
                                                        <input type="hidden" name="open_company_editor_id" value="{{ $company->id }}">
                                                        <input type="hidden" name="open_company_contacts" value="1">
                                                        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:8px;">
                                                            <div style="flex:1; min-width:90px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Imię</label>
                                                                <input type="text" name="first_name" placeholder="Imię" value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('first_name') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:1.2; min-width:100px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Nazwisko</label>
                                                                <input type="text" name="last_name" placeholder="Nazwisko" value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('last_name') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:0.8; min-width:70px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Skrót</label>
                                                                <input type="text" name="short_name" placeholder="auto" maxlength="32" value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('short_name') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:1.2; min-width:100px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Stanowisko</label>
                                                                <input type="text" name="position" placeholder="np. Dyrektor" value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('position') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:1.8; min-width:140px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Email *</label>
                                                                <input type="email" name="email" placeholder="Email" required value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('email') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:1; min-width:90px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Telefon</label>
                                                                <input type="text" name="phone" placeholder="Telefon" value="{{ old('open_company_contacts') && old('open_assigned_user_id') === null ? old('phone') : '' }}" style="width:100%;">
                                                            </div>
                                                            <div style="flex:1.2; min-width:100px;">
                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Hasło</label>
                                                                <input type="password" name="password" placeholder="Hasło (dla nowej osoby)" style="width:100%;">
                                                            </div>
                                                            <div style="flex:0 0 auto;">
                                                                <button type="submit">Dodaj</button>
                                                            </div>
                                                        </div>
                                                        @if($errors->has('password') || $errors->has('email'))
                                                            <div style="margin-top:6px; font-size:12px; color:#b42318;">
                                                                {{ $errors->first('password') ?: $errors->first('email') }}
                                                            </div>
                                                        @endif
                                                    </form>

                                                    {{-- Existing assigned users --}}
                                                    @if($company->assignedUsers->isNotEmpty())
                                                        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:4px;">
                                                            <thead>
                                                                <tr style="color:#9ab4c5; font-size:11px; font-weight:700; text-transform:uppercase;">
                                                                    <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Imię i nazwisko</th>
                                                                    <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Skrót</th>
                                                                    <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Stanowisko</th>
                                                                    <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Email</th>
                                                                    <th style="padding:4px 8px; text-align:left; font-weight:700; border-bottom:1px solid #e3edf5;">Telefon</th>
                                                                    <th style="padding:4px 8px; border-bottom:1px solid #e3edf5;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($company->assignedUsers as $assignedUser)
                                                                <tr style="border-bottom:1px solid #edf4fb;">
                                                                    <td style="padding:5px 8px; color:#0f2330; font-weight:600; white-space:nowrap;">{{ trim(($assignedUser->first_name ?? '') . ' ' . ($assignedUser->last_name ?? '')) ?: $assignedUser->name }}</td>
                                                                    <td style="padding:5px 8px; color:#4c6373; white-space:nowrap;">{{ $assignedUser->short_name ?: '—' }}</td>
                                                                    <td style="padding:5px 8px; color:#4c6373;">{{ $assignedUser->position ?: '—' }}</td>
                                                                    <td style="padding:5px 8px; color:#4c6373;">{{ $assignedUser->email }}</td>
                                                                    <td style="padding:5px 8px; color:#4c6373; white-space:nowrap;">{{ $assignedUser->phone ?: '—' }}</td>
                                                                    <td style="padding:5px 8px; text-align:right; white-space:nowrap;">
                                                                        <button type="button" class="btn-secondary" onclick="toggleAssignedUserEditor({{ $company->id }}, {{ $assignedUser->id }})">Edytuj</button>
                                                                    </td>
                                                                </tr>
                                                                <tr id="assigned-user-editor-row-{{ $company->id }}-{{ $assignedUser->id }}" style="display:{{ old('open_assigned_user_id') == $assignedUser->id ? 'table-row' : 'none' }}; background:#f5faff;">
                                                                    <td colspan="6" style="padding:10px 8px;">
                                                                        <form id="assigned-user-editor-{{ $company->id }}-{{ $assignedUser->id }}" method="POST" action="{{ route('settings.company-client-update', [$company, $assignedUser]) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="open_company_editor_id" value="{{ $company->id }}">
                                                                        <input type="hidden" name="open_company_contacts" value="1">
                                                                        <input type="hidden" name="open_assigned_user_id" value="{{ $assignedUser->id }}">
                                                                        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:8px;">
                                                                            <div style="flex:1; min-width:90px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Imię</label>
                                                                                <input type="text" name="first_name" placeholder="Imię" value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('first_name', $assignedUser->first_name) : $assignedUser->first_name }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:1.2; min-width:100px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Nazwisko</label>
                                                                                <input type="text" name="last_name" placeholder="Nazwisko" value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('last_name', $assignedUser->last_name) : $assignedUser->last_name }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:0.8; min-width:70px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Skrót</label>
                                                                                <input type="text" name="short_name" placeholder="Skrót" maxlength="32" value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('short_name', $assignedUser->short_name) : $assignedUser->short_name }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:1.2; min-width:100px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Stanowisko</label>
                                                                                <input type="text" name="position" placeholder="Stanowisko" value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('position', $assignedUser->position) : $assignedUser->position }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:1.8; min-width:140px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Email *</label>
                                                                                <input type="email" name="email" placeholder="Email" required value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('email', $assignedUser->email) : $assignedUser->email }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:1; min-width:90px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Telefon</label>
                                                                                <input type="text" name="phone" placeholder="Telefon" value="{{ old('open_assigned_user_id') == $assignedUser->id ? old('phone', $assignedUser->phone) : $assignedUser->phone }}" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:1.2; min-width:100px;">
                                                                                <label style="display:block; font-size:11px; font-weight:700; color:#4c6373; margin-bottom:3px;">Nowe hasło</label>
                                                                                <input type="password" name="password" placeholder="Opcjonalnie" style="width:100%;">
                                                                            </div>
                                                                            <div style="flex:0 0 auto;">
                                                                                <button type="submit">Zapisz</button>
                                                                            </div>
                                                                        </div>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <p style="font-size:13px; color:#9ab4c5; margin:0;">Brak przypisanych użytkowników.</p>
                                                    @endif

                                                    {{-- Add user button below the list --}}
                                                    <div style="display:flex; justify-content:flex-end; margin-top:12px;">
                                                        <button type="button" class="edit-user-btn" onclick="toggleCompanyContactsForm({{ $company->id }})">+ Dodaj użytkownika</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 3. CENY OFERT --}}
        <div class="acc-item" id="acc-pricing">
            <button class="acc-header" onclick="toggleAcc('acc-pricing')">
                <div class="acc-icon acc-icon-amber">💰</div>
                <div class="acc-title">
                    <strong>{{ __('ui.settings.pricing.title') }}</strong>
                    <span>{{ __('ui.settings.pricing.subtitle') }}</span>
                </div>
                <span class="acc-chevron">&#9660;</span>
            </button>
            <div class="acc-body">
                <div class="acc-note">{{ __('ui.settings.pricing.note') }}</div>
                <div style="margin-top:16px;">
                    <div class="price-row">
                        <div class="price-badge">
                            <span class="tag tag-pro">PRO</span>
                            <span style="font-weight:600;">Audyt Pełny</span>
                        </div>
                        <span class="price-val">od 55 000 PLN</span>
                    </div>
                    <div class="price-row">
                        <div class="price-badge">
                            <span class="tag tag-express">EXPRESS</span>
                            <span style="font-weight:600;">Audyt Compliance+</span>
                        </div>
                        <span class="price-val">od 12 000 PLN</span>
                    </div>
                    <div class="price-row">
                        <div class="price-badge">
                            <span class="tag tag-iso">ISO 50001</span>
                            <span style="font-weight:600;">Zarządzanie Energią</span>
                        </div>
                        <span class="price-val">od 30 000 PLN</span>
                    </div>
                    <div class="price-row">
                        <div class="price-badge">
                            <span class="tag tag-micro">MICROGRID</span>
                            <span style="font-weight:600;">Microgrid Przemysłowy</span>
                        </div>
                        <span class="price-val" style="color:#7c3aed;">{{ __('ui.settings.pricing.custom_quote') }}</span>
                    </div>
                    <div class="price-row">
                        <div class="price-badge">
                            <span class="tag tag-impl">IMPLEMENT</span>
                            <span style="font-weight:600;">Inżynier Kontraktu</span>
                        </div>
                        <span class="price-val" style="color:#ea580c;">3–8% wart. kontraktu</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. MOJA FIRMA --}}
        <div class="acc-item" id="acc-my-company">
            <button class="acc-header" onclick="toggleAcc('acc-my-company')">
                <div class="acc-icon acc-icon-green">🏢</div>
                <div class="acc-title">
                    <strong>Moja firma</strong>
                    <span>Dane kontaktowe firmy — adres e-mail widoczny dla klientów w Strefie klienta</span>
                </div>
                <span class="acc-chevron">&#9660;</span>
            </button>
            <div class="acc-body">
                @if($canManage)
                    <form method="POST" action="{{ route('settings.my-company') }}" style="margin-top:14px; display:flex; flex-direction:column; gap:10px; max-width:480px;">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px;">
                                E-mail kontaktowy dla klientów
                            </label>
                            <input type="email" name="company_contact_email"
                                value="{{ old('company_contact_email', $companyContactEmail ?? '') }}"
                                placeholder="np. biuro@enesa.pl"
                                style="width:100%;">
                            <div style="font-size:11px; color:#6b8294; margin-top:4px;">
                                Ten adres będzie wyświetlany klientom w Strefie klienta jako przycisk „Napisz do nas".
                            </div>
                        </div>
                        <div>
                            <button type="submit">💾 Zapisz dane firmy</button>
                        </div>
                        @if (session('my_company_status'))
                            <div class="status" style="margin-top:0;">{{ session('my_company_status') }}</div>
                        @endif
                    </form>
                @else
                    <div class="acc-note" style="margin-top:14px;">
                        E-mail kontaktowy: <strong>{{ $companyContactEmail ?? '—' }}</strong>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        function toggleAcc(id) {
            document.getElementById(id).classList.toggle('open');
        }

        function toggleAlsoAuditor(userId, roleValue) {
            const wrap = document.getElementById('also-auditor-wrap-' + userId);
            if (!wrap) return;
            wrap.style.display = roleValue === 'admin' ? 'flex' : 'none';
            if (roleValue !== 'admin') {
                const cb = document.getElementById('also-auditor-' + userId);
                if (cb) cb.checked = false;
            }
        }

        function toggleUserEditor(userId) {
            const row = document.getElementById('user-editor-' + userId);
            if (!row) return;

            const normalActions = document.getElementById('user-normal-actions-' + userId);
            const editActions = document.getElementById('user-edit-actions-' + userId);

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
            if (normalActions) normalActions.style.display = visible ? 'flex' : 'none';
            if (editActions) editActions.style.display = visible ? 'none' : 'flex';
        }

        function toggleAddUserForm() {
            const form = document.getElementById('add-user-form');
            if (!form) {
                return;
            }

            const visible = form.style.display !== 'none';
            form.style.display = visible ? 'none' : 'flex';
        }

        function toggleCompanyEditor(companyId) {
            const row = document.getElementById('company-editor-' + companyId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }

        function toggleCompanyInfo(companyId) {
            const row = document.getElementById('company-info-' + companyId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }

        function toggleCompanyUsers(companyId) {
            const row = document.getElementById('company-users-' + companyId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }

        function sortCompaniesTable(colIdx) {
            const tbody = document.getElementById('companies-tbody');
            const thead = document.getElementById('companies-thead');
            if (!tbody || !thead) return;

            const ths = thead.querySelectorAll('th');
            const th = ths[colIdx];
            if (!th) return;

            const currentDir = th.dataset.sortDir || '';
            const dir = currentDir === 'asc' ? 'desc' : 'asc';

            // Reset all indicators
            ths.forEach(h => {
                h.dataset.sortDir = '';
                const ind = h.querySelector('.sort-ind');
                if (ind) ind.textContent = '↕';
            });

            th.dataset.sortDir = dir;
            const ind = th.querySelector('.sort-ind');
            if (ind) ind.textContent = dir === 'asc' ? '↑' : '↓';

            // Collect main rows with their group (users row + editor row)
            const mainRows = Array.from(tbody.querySelectorAll('tr[data-company-id]'));

            const multiplier = dir === 'asc' ? 1 : -1;
            mainRows.sort((a, b) => {
                const aVal = (a.dataset['col' + colIdx] || '');
                const bVal = (b.dataset['col' + colIdx] || '');
                return aVal.localeCompare(bVal, undefined, {numeric: true}) * multiplier;
            });

            // Re-insert groups in sorted order
            mainRows.forEach(row => {
                const cid = row.dataset.companyId;
                const usersRow = document.getElementById('company-users-' + cid);
                const editorRow = document.getElementById('company-editor-' + cid);
                tbody.appendChild(row);
                if (usersRow) tbody.appendChild(usersRow);
                if (editorRow) tbody.appendChild(editorRow);
            });
        }

        function toggleCompanyContactsForm(companyId) {
            const form = document.getElementById('company-contacts-form-' + companyId);
            if (!form) {
                return;
            }

            const visible = form.style.display !== 'none';
            form.style.display = visible ? 'none' : 'flex';
        }

        function toggleAssignedUserEditor(companyId, userId) {
            const row = document.getElementById('assigned-user-editor-row-' + companyId + '-' + userId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }

        function toggleAddCompanyForm() {
            const form = document.getElementById('add-company-form');
            if (!form) {
                return;
            }

            const visible = form.style.display !== 'none';
            form.style.display = visible ? 'none' : 'block';
        }


        async function lookupCompanyByNip() {
            const nipInput = document.getElementById('new-company-nip');
            const nameInput = document.getElementById('new-company-name');
            const streetInput = document.getElementById('new-company-street');
            const postalInput = document.getElementById('new-company-postal');
            const cityInput = document.getElementById('new-company-city');
            const messageBox = document.getElementById('nip-lookup-message');

            if (!nipInput || !nameInput || !streetInput || !postalInput || !cityInput || !messageBox) {
                return;
            }

            const rawNip = nipInput.value.trim();
            const nip = rawNip.replace(/[\s\-\.\,]/g, '');

            if (!nip) {
                messageBox.textContent = '{{ __('ui.settings.clients.lookup.enter_nip') }}';
                return;
            }

            messageBox.textContent = '{{ __('ui.settings.clients.lookup.loading') }}';

            try {
                const params = new URLSearchParams({ nip });
                const response = await fetch(`{{ route('settings.company-lookup-by-nip', [], false) }}?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const payload = await response.json();

                if (!response.ok || !payload.ok) {
                    messageBox.textContent = payload.message || '{{ __('ui.settings.clients.lookup.not_found') }}';
                    return;
                }

                nameInput.value = payload.data?.name ?? '';
                streetInput.value = payload.data?.street ?? '';
                postalInput.value = payload.data?.postal_code ?? '';
                cityInput.value = payload.data?.city ?? '';
                nipInput.value = payload.data?.nip ?? nip;
                messageBox.textContent = '{{ __('ui.settings.clients.lookup.success') }}';
            } catch (error) {
                messageBox.textContent = '{{ __('ui.settings.clients.lookup.error') }}';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const openAddUser = @json(old('open_add_user'));
            const openAddCompany = @json(old('open_add_company'));
            const openCompanyEditorId = @json(old('open_company_editor_id'));
            const openCompanyContacts = @json(old('open_company_contacts'));
            const openAssignedUserId = @json(old('open_assigned_user_id'));

            if (openAddUser) {
                const addUserForm = document.getElementById('add-user-form');
                if (addUserForm) {
                    addUserForm.style.display = 'flex';
                }
            }

            if (openAddCompany) {
                const addForm = document.getElementById('add-company-form');
                if (addForm) {
                    addForm.style.display = 'block';
                }
            }

            if (openCompanyEditorId) {
                const editorRow = document.getElementById('company-editor-' + openCompanyEditorId);
                if (editorRow) {
                    editorRow.style.display = 'table-row';
                }

                if (openCompanyContacts) {
                    const contactsForm = document.getElementById('company-contacts-form-' + openCompanyEditorId);
                    if (contactsForm) {
                        contactsForm.style.display = 'flex';
                    }
                }

                if (openAssignedUserId) {
                    const userEditForm = document.getElementById('assigned-user-editor-' + openCompanyEditorId + '-' + openAssignedUserId);
                    if (userEditForm) {
                        userEditForm.style.display = 'flex';
                    }
                }
            }

        });
    </script>

@if($canManage)
    {{-- ── DOSTĘP PUBLICZNY ── --}}
    <section class="panel" style="margin-top:14px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <div style="width:4px; height:36px; border-radius:4px; background:linear-gradient(180deg,#0e55b3,#2d8ee3);"></div>
            <div>
                <h2 style="margin:0;">🌐 Dostęp publiczny do zakładki Informacje</h2>
                <p class="muted" style="margin:4px 0 0;">Kontroluje czy niezalogowani użytkownicy mogą wyświetlać stronę /informacje.</p>
            </div>
        </div>

        @if(session('public_access_status'))
            <div style="background:#f0fff4; border:1px solid #86efac; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; color:#166534;">
                ✅ {{ session('public_access_status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.public-access') }}" style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            @csrf
            @method('PATCH')

            <label style="display:flex; align-items:center; gap:12px; cursor:pointer; padding:14px 18px; border:2px solid {{ $informajePublic ? '#86efac' : '#fca5a5' }}; border-radius:12px; background:{{ $informajePublic ? '#f0fff4' : '#fff5f5' }}; min-width:280px;">
                <input type="checkbox" name="informacje_public" value="1" {{ $informajePublic ? 'checked' : '' }}
                    onchange="this.form.submit()"
                    style="width:20px; height:20px; cursor:pointer; accent-color:#1a5c2e;">
                <div>
                    <div style="font-size:14px; font-weight:700; color:#0f2330;">
                        {{ $informajePublic ? '✅ Dostęp publiczny WŁĄCZONY' : '🔒 Dostęp publiczny WYŁĄCZONY' }}
                    </div>
                    <div style="font-size:12px; color:#4c6373; margin-top:2px;">
                        {{ $informajePublic ? 'Strona /informacje widoczna dla wszystkich bez logowania' : 'Strona /informacje tylko dla zalogowanych użytkowników z uprawnieniem' }}
                    </div>
                </div>
            </label>

            <noscript>
                <button type="submit" style="padding:9px 18px; background:#0e55b3; color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer;">Zapisz</button>
            </noscript>
        </form>

        <div style="margin-top:10px; font-size:12px; color:#4c6373; background:#edf3f8; border-radius:8px; padding:10px 14px; border-left:3px solid #0e89d8;">
            💡 Niezależnie od tego ustawienia, zalogowani użytkownicy z uprawnieniem <strong>Informacje</strong> zawsze widzą zakładkę w menu.
        </div>
    </section>

    {{-- ── WSKAŹNIKI EMISJI CO₂ ── --}}
    <section class="panel" style="margin-top:14px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
            <div style="width:4px; height:36px; border-radius:4px; background:linear-gradient(180deg,#1a5c2e,#2d9e57);"></div>
            <div>
                <h2 style="margin:0;">🌿 Wskaźniki emisji CO₂ — energia elektryczna (KSE)</h2>
                <p class="muted" style="margin:4px 0 0;">Wartości aktualizowane raz w roku po publikacji KOBiZE. Widoczne w kalkulatorze i stałych energetycznych.</p>
            </div>
        </div>

        @if(session('co2_settings_status'))
            <div style="background:#f0fff4; border:1px solid #86efac; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; color:#166534;">
                ✅ {{ session('co2_settings_status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.energy-indicators') }}" style="display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end;">
            @csrf
            @method('PATCH')

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#1a5c2e; margin-bottom:5px;">
                    Rok sprawozdawczy KOBiZE
                </label>
                <input type="number" name="co2_el_year" value="{{ $co2ElYear }}"
                    min="2015" max="2100" step="1" required
                    style="width:120px; padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; font-weight:600;">
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#1a5c2e; margin-bottom:5px;">
                    Wskaźnik — źródła spalania [g CO₂/kWh]
                    <span style="font-weight:400; color:#4c6373;">(EU ETS, białe certyfikaty)</span>
                </label>
                <input type="number" name="co2_el_comb_factor" value="{{ $co2ElCombFactor }}"
                    min="1" max="2000" step="1" required
                    style="width:140px; padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; font-weight:600;">
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#1a5c2e; margin-bottom:5px;">
                    Wskaźnik krajowy z OZE [g CO₂/kWh]
                    <span style="font-weight:400; color:#4c6373;">(CSR, ślad węglowy)</span>
                </label>
                <input type="number" name="co2_el_nat_factor" value="{{ $co2ElNatFactor }}"
                    min="1" max="2000" step="1" required
                    style="width:140px; padding:8px 10px; border-radius:9px; border:1px solid #c9d7e3; font-size:14px; font-weight:600;">
            </div>

            <div>
                <button type="submit" class="btn-primary" style="padding:9px 18px; background:#1a5c2e; color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer;">
                    💾 Zapisz wskaźniki
                </button>
            </div>
        </form>

        <div style="margin-top:12px; font-size:12px; color:#4c6373; background:#edf3f8; border-radius:8px; padding:10px 14px; border-left:3px solid #0e89d8; line-height:1.7;">
            📥 Źródło: <a href="https://www.kobize.pl/pl/fileCategory/id/28/wskazniki-emisyjnosci" target="_blank" rel="noopener noreferrer" style="color:#0e89d8;">KOBiZE — Wskaźniki emisyjności</a>
            · Publikacja zwykle w grudniu każdego roku · Plik:
            <a href="https://www.kobize.pl/uploads/materialy/materialy_do_pobrania/aktualnosci/2025/142_Wskazniki_emisyjnosci_2025.pdf" target="_blank" rel="noopener noreferrer" style="color:#0e89d8;">142_Wskazniki_emisyjnosci_2025.pdf</a>
        </div>

        {{-- ── Historia wskaźników ── --}}
        <h4 style="margin:24px 0 10px; font-size:14px; font-weight:800; color:#1a5c2e;">📋 Historia wskaźników emisyjności KOBiZE</h4>

        @if(isset($co2History) && $co2History->count())
        <div style="overflow-x:auto; margin-bottom:16px;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:#e8f5e9; text-align:left;">
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;">Rok</th>
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;">Źródła spalania<br><small style="font-weight:400; color:#6b8294;">g CO₂/kWh</small></th>
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;">Krajowy z OZE<br><small style="font-weight:400; color:#6b8294;">g CO₂/kWh</small></th>
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;">Źródło / PDF</th>
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;">Dodano</th>
                        <th style="padding:8px 12px; border-bottom:2px solid #a8d5b5;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($co2History as $h)
                    <tr style="border-bottom:1px solid #d2e3f1; {{ $loop->even ? 'background:#f9fdfb;' : '' }}">
                        <td style="padding:7px 12px; font-weight:700;">{{ $h->year }}</td>
                        <td style="padding:7px 12px; font-weight:600; color:#1a5c2e;">{{ $h->comb_factor }}</td>
                        <td style="padding:7px 12px; font-weight:600; color:#1a5c2e;">{{ $h->nat_factor }}</td>
                        <td style="padding:7px 12px;">
                            @if($h->source_url)
                                <a href="{{ $h->source_url }}" target="_blank" rel="noopener noreferrer" style="color:#0e89d8; font-size:12px;">pobierz PDF</a>
                            @else
                                <span style="color:#9e9e9e; font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="padding:7px 12px; font-size:11px; color:#6b8294;">{{ $h->created_at ? $h->created_at->format('Y-m-d') : '—' }}</td>
                        <td style="padding:7px 12px;">
                            <form method="POST" action="{{ route('settings.co2-history-destroy', $h) }}" onsubmit="return confirm('Usuń rok {{ $h->year }}?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:#fee2e2; color:#c0392b; border:1px solid #f5c6c6; border-radius:6px; padding:3px 10px; font-size:12px; cursor:pointer;">Usuń</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p style="font-size:13px; color:#6b8294; margin-bottom:16px;">Brak zapisanych wpisów historycznych.</p>
        @endif

        {{-- ── Dodaj nowy rok ── --}}
        <details style="margin-top:10px; border:1px solid #c8e6c9; border-radius:10px; padding:12px 16px; background:#f1faf3;">
            <summary style="cursor:pointer; font-size:13px; font-weight:700; color:#1a5c2e; user-select:none;">➕ Dodaj nowy rok do historii</summary>
            <form method="POST" action="{{ route('settings.co2-history-store') }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-top:12px;">
                @csrf
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#355468; margin-bottom:4px;">Rok sprawozdawczy</label>
                    <input type="number" name="year" min="2000" max="2100" step="1" placeholder="np. 2023" required
                        style="width:110px; padding:7px 10px; border-radius:8px; border:1px solid #c9d7e3; font-size:13px; font-weight:600;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#355468; margin-bottom:4px;">Źródła spalania [g CO₂/kWh]</label>
                    <input type="number" name="comb_factor" min="1" max="2000" step="1" placeholder="np. 717" required
                        style="width:130px; padding:7px 10px; border-radius:8px; border:1px solid #c9d7e3; font-size:13px; font-weight:600;">
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#355468; margin-bottom:4px;">Krajowy z OZE [g CO₂/kWh]</label>
                    <input type="number" name="nat_factor" min="1" max="2000" step="1" placeholder="np. 552" required
                        style="width:130px; padding:7px 10px; border-radius:8px; border:1px solid #c9d7e3; font-size:13px; font-weight:600;">
                </div>
                <div style="flex:1; min-width:200px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#355468; margin-bottom:4px;">URL źródłowego PDF (opcjonalnie)</label>
                    <input type="url" name="source_url" placeholder="https://www.kobize.pl/..." maxlength="500"
                        style="width:100%; box-sizing:border-box; padding:7px 10px; border-radius:8px; border:1px solid #c9d7e3; font-size:13px;">
                </div>
                <div>
                    <button type="submit" style="padding:8px 16px; background:#1a5c2e; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
                        Dodaj rekord
                    </button>
                </div>
            </form>
        </details>
    </section>
@endif

</x-layouts.app>

