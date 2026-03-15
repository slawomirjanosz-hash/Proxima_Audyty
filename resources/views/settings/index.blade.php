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
                                </td>
                                <td>
                                    @if($canManage)
                                        <button type="button" class="edit-user-btn" onclick="toggleUserEditor({{ $user->id }})">{{ __('ui.settings.actions.edit') }}</button>
                                    @else
                                        <span style="color:#9ab4c5;font-size:13px;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if($canManage)
                                <tr id="user-editor-{{ $user->id }}" class="permissions-row" style="display:none;">
                                    <td colspan="4">
                                        <div class="permissions-panel">
                                            <form method="POST" action="{{ route('settings.user-access', $user) }}">
                                                @csrf
                                                @method('PATCH')

                                                <div class="inline-form" style="margin-bottom:10px;">
                                                    <label for="first-name-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.first_name') }}</label>
                                                    <input id="first-name-{{ $user->id }}" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}">

                                                    <label for="last-name-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.last_name') }}</label>
                                                    <input id="last-name-{{ $user->id }}" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}">

                                                    <label for="short-name-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.short_name') }} <span style="color:#d00">*</span></label>
                                                    <input id="short-name-{{ $user->id }}" type="text" name="short_name" maxlength="32" required value="{{ old('short_name', $user->short_name ?? (\Illuminate\Support\Str::substr($user->first_name,0,3).\Illuminate\Support\Str::substr($user->last_name,0,3))) }}">
                                                    @error('short_name')
                                                        <span style="color:#d00; font-size:12px;">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="inline-form" style="margin-bottom:10px;">
                                                    <label for="email-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.columns.email') }}</label>
                                                    <input id="email-{{ $user->id }}" type="email" name="email" value="{{ old('email', $user->email) }}" required>

                                                    <label for="phone-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.phone') }}</label>
                                                    <input id="phone-{{ $user->id }}" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                                                </div>

                                                <div class="inline-form" style="margin-bottom:10px;">
                                                    <label for="password-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.password') }}</label>
                                                    <input id="password-{{ $user->id }}" type="password" name="password" placeholder="{{ __('ui.settings.users.password_placeholder') }}">

                                                    <label for="role-{{ $user->id }}" style="font-size:12px; font-weight:700; color:#4c6373;">{{ __('ui.settings.users.role_label') }}</label>
                                                    <select id="role-{{ $user->id }}" name="role">
                                                        @foreach ([\App\Enums\UserRole::Admin, \App\Enums\UserRole::Auditor, \App\Enums\UserRole::Client] as $role)
                                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="permissions-grid">
                                                    @foreach($tabLabels as $tabKey => $tabLabel)
                                                        <label class="perm-item">
                                                            <input type="checkbox" name="tabs[{{ $tabKey }}]" value="1" @checked($currentTabs[$tabKey] ?? false)>
                                                            <span>{{ $tabLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                <div class="permissions-actions">
                                                    <button type="submit">{{ __('ui.settings.actions.save_permissions') }}</button>
                                                    <button type="button" class="btn-secondary" onclick="toggleUserEditor({{ $user->id }})">{{ __('ui.settings.actions.cancel') }}</button>
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
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('ui.settings.clients.columns.company') }}</th>
                            <th>{{ __('ui.settings.clients.columns.current_client') }}</th>
                            <th>{{ __('ui.settings.clients.columns.current_auditor') }}</th>
                            <th>{{ __('ui.settings.clients.columns.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td><strong>{{ $company->name }}</strong></td>
                                <td>{{ $company->client?->name ?? '—' }}</td>
                                <td>{{ $company->auditor?->name ?? '—' }}</td>
                                <td>
                                    @if($canManage)
                                        <form class="inline-form" method="POST" action="{{ route('settings.company-assignments', $company) }}">
                                            @csrf @method('PATCH')
                                            <select name="client_id">
                                                <option value="">{{ __('ui.settings.clients.no_client') }}</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" @selected($company->client_id === $client->id)>{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                            <select name="auditor_id">
                                                <option value="">{{ __('ui.settings.clients.no_auditor') }}</option>
                                                @foreach($auditors as $auditor)
                                                    <option value="{{ $auditor->id }}" @selected($company->auditor_id === $auditor->id)>{{ $auditor->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit">{{ __('ui.settings.actions.save') }}</button>
                                        </form>
                                    @else
                                        <span style="color:#9ab4c5;font-size:13px;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    </div>

    <script>
        function toggleAcc(id) {
            document.getElementById(id).classList.toggle('open');
        }

        function toggleUserEditor(userId) {
            const row = document.getElementById('user-editor-' + userId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }
    </script>

</x-layouts.app>

