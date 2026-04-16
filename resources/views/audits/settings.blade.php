<x-layouts.app>
    <section class="panel">
        <style>
            .settings-section { border: 1px solid #d2e3f1; border-radius: 14px; background: #fff; margin-top: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(18, 72, 110, 0.06); }
            .settings-toggle { width: 100%; border: none; background: #fafdff; display:flex; justify-content:space-between; align-items:center; gap:10px; padding: 14px 14px; cursor:pointer; }
            .settings-toggle:hover { background:#f2f8fe; }
            .settings-toggle-content { text-align:left; display:flex; flex-direction:column; align-items:flex-start; }
            .settings-toggle h2 { margin:0; font-size:19px; font-weight:800; color:#10344c; letter-spacing:.2px; }
            .settings-toggle .muted { margin:5px 0 0; font-size:13px; color:#355c77; }
            .settings-body { display:none; padding: 0 12px 12px; border-top:1px solid #e8f1f8; }
            .settings-section.open .settings-body { display:block; }
            .settings-section.open .settings-toggle { background:#eef6ff; }
            .settings-chevron { color:#6b8aa3; font-size:18px; transition:transform .2s; }
            .settings-section.open .settings-chevron { transform: rotate(180deg); }
            .audit-type-card { border: 1px solid #dfeaf3; border-radius: 10px; padding: 10px; margin-top: 10px; background:#fbfdff; }
            .audit-type-header { display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }
            .audit-type-title-btn { border:none; background:transparent; padding:0; margin:0; font:inherit; cursor:pointer; color:#10344c; font-weight:800; font-size:15px; display:flex; align-items:center; gap:8px; }
            .audit-type-index { display:inline-flex; align-items:center; justify-content:center; min-width:24px; height:24px; border-radius:999px; background:#e8f3ff; color:#1d4f73; font-size:12px; font-weight:800; }
            .audit-type-chevron { color:#6b8aa3; font-size:16px; transition:transform .2s; }
            .audit-type-card.open .audit-type-chevron { transform:rotate(180deg); }
            .audit-type-details { display:none; margin-top:10px; }
            .audit-type-card.open .audit-type-details { display:block; }
            .audit-type-section { margin-top: 10px; padding: 10px; border:1px solid #dfeaf7; border-left:4px solid #7fb4e1; border-radius:10px; background:#f8fbff; }
            .audit-type-card .audit-type-section:nth-of-type(even) { border-left-color:#7ed0b2; background:#f6fcfa; }
            .audit-type-section strong { font-size:13px; color:#163f5b; }
            .audit-builder { display:none; margin-top:14px; padding:12px; border:1px solid #dfeaf3; border-radius:10px; background:#f9fcff; }
            .data-table { width:100%; border-collapse: collapse; margin-top:8px; }
            .data-table th, .data-table td { border:1px solid #e5eef6; padding:8px; font-size:13px; text-align:left; }
            .data-table th { background:#f3f8fd; color:#34556f; font-weight:700; }
            .data-table input, .data-table select { width:100%; }
            .data-table-fields th, .data-table-fields td { padding:5px 6px; font-size:12px; vertical-align:middle; }
            .data-table-fields th { font-size:11px; letter-spacing:.3px; }
            .data-table-fields input,
            .data-table-fields select,
            .data-table-fields textarea { width:100%; font-size:12px; padding:5px 7px; min-height:30px; border-radius:8px; }
            .data-table-fields textarea { min-height:30px; line-height:1.2; }
            .data-table-fields th:nth-child(3), .data-table-fields td:nth-child(3) { width:17%; min-width:160px; }
            .data-table-fields th:nth-child(4), .data-table-fields td:nth-child(4) { width:17%; min-width:160px; }
            .data-table-fields th:nth-child(9), .data-table-fields td:nth-child(9) { width:10%; min-width:110px; }
            .data-table-fields th:nth-child(11), .data-table-fields td:nth-child(11) { width:56px; text-align:center; }
            .row-insert-anchor { width:14px !important; height:14px !important; min-height:14px !important; margin:0; accent-color:#2f78af; cursor:pointer; }
            .data-table-wrap { position:relative; }
            .dependency-tree-overlay { position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:2; }
            .dependency-tree-overlay .dependency-path { fill:none; stroke:#9ec2df; stroke-width:1.35; stroke-linecap:round; stroke-linejoin:round; opacity:.9; transition:stroke .15s, stroke-width .15s, opacity .15s; }
            .dependency-tree-overlay .dependency-path.dependency-path-active { stroke:#2f78af; stroke-width:2.2; opacity:1; }
            .data-table tr.dependency-branch-active td:nth-child(3) { background:#eef6ff; }
            .data-table tr.is-dependent-row td:nth-child(3) { position:relative; padding-left:calc(38px + (var(--dependency-depth, 1) - 1) * 24px); color:#264e6b; }
            .data-table tr.is-dependent-row td:nth-child(3)::before { content:attr(data-dependency-label); position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#1d4f73; font-weight:800; font-size:14px; line-height:1; letter-spacing:-1px; min-width:18px; text-align:center; }
            .btn-secondary { background: #dbe9f5; color: #1d4f73; }
            .row-drag-handle { display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; cursor:grab; color:#68849a; user-select:none; font-size:12px; letter-spacing:-1px; }
            .row-drag-handle:active { cursor:grabbing; }
            tr[data-draggable-row].is-dragging { opacity:.55; }
            .section-drag-handle, .formula-drag-handle { display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; cursor:grab; color:#68849a; user-select:none; font-size:12px; letter-spacing:-1px; margin-right:6px; }
            .section-drag-handle:active, .formula-drag-handle:active { cursor:grabbing; }
            .audit-type-section[data-section-item].is-dragging, .section-formula-row[data-formula-item].is-dragging { opacity:.55; }
            .audit-type-section[data-section-item].section-collapsed .section-collapsible-content { display:none !important; }
            .section-formula-row[data-formula-item].formula-collapsed .formula-collapsible-content { display:none !important; }
            .section-collapse-arrow { width:24px; height:24px; border:1px solid #c7d9e8; border-radius:6px; background:#fff; color:#35556f; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:12px; line-height:1; }
            .section-collapse-arrow:hover { background:#eef6ff; }
            .token-helper { margin:0 0 8px; padding:8px; border:1px solid #dbe8f3; border-radius:8px; background:#f7fbff; }
            .token-helper-title { font-size:12px; font-weight:700; color:#35556f; margin-bottom:6px; }
            .token-list { display:flex; flex-wrap:wrap; gap:6px; }
            .token-chip { border:1px solid #c6daeb; border-radius:999px; background:#fff; color:#1d4f73; padding:4px 8px; font-size:12px; cursor:pointer; }
            .token-chip:hover { background:#eef6ff; }
            .token-chip-formula { border-color:#b0d9c8; color:#0a5c46; background:#f4fdf9; }
            .token-chip-formula:hover { background:#d9f5eb; }
            .token-chip-separator { font-size:12px; color:#9ab5c8; align-self:center; }
            .token-helper-hint { font-size:11px; color:#5f7688; margin-top:6px; }
            .btn-trash-icon { width:28px; height:28px; min-height:28px; padding:0; border-radius:8px; background:#e9f1f8; color:#35556f; border:1px solid #c9dceb; font-size:14px; line-height:1; display:inline-flex; align-items:center; justify-content:center; }
            .btn-trash-icon:hover { background:#dceaf6; }
            .settings-tab-btn { padding:8px 14px; border-radius:10px; border:1px solid #d7e5f0; background:#eef5fb; font-weight:700; color:#28485f; display:inline-block; text-decoration:none; font-size:14px; }
            .settings-tab-btn.active { background:#fff; border-color:#0e89d8; color:#0e89d8; }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
            <div>
                <h1 style="margin:0;">
                    @if($activeTab === 'energetyczne') Audyty energetyczne przedsiębiorstw
                    @elseif($activeTab === 'iso50001') Audyty ISO 50001
                    @elseif($activeTab === 'biale-certyfikaty') Audyty na białe certyfikaty
                    @elseif($activeTab === 'ai-audyty') Audyty z AI
                    @else Ustawienia
                    @endif
                </h1>
            </div>
        </div>

        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px;">
            <a href="{{ route('audits.types', ['tab' => 'energetyczne']) }}" class="settings-tab-btn {{ $activeTab === 'energetyczne' ? 'active' : '' }}">Audyty energetyczne</a>
            <a href="{{ route('audits.types', ['tab' => 'iso50001']) }}" class="settings-tab-btn {{ $activeTab === 'iso50001' ? 'active' : '' }}">ISO 50001</a>
            <a href="{{ route('audits.types', ['tab' => 'biale-certyfikaty']) }}" class="settings-tab-btn {{ $activeTab === 'biale-certyfikaty' ? 'active' : '' }}">Białe certyfikaty</a>
            <a href="{{ route('audits.types', ['tab' => 'ai-audyty']) }}" class="settings-tab-btn {{ $activeTab === 'ai-audyty' ? 'active' : '' }}">🤖 Audyty z AI</a>
            <a href="{{ route('audits.types', ['tab' => 'ustawienia']) }}" class="settings-tab-btn {{ $activeTab === 'ustawienia' ? 'active' : '' }}">Ustawienia</a>
        </div>

        @if($activeTab === 'energetyczne')
        <div class="settings-section open" id="settings-audit-types">
            <div class="settings-body" style="display:block;">
                <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                    <button type="button" class="edit-user-btn" onclick="toggleAuditTypeForm()">Dodaj rodzaj audytu</button>
                </div>

                <form id="add-audit-type-form" method="POST" action="{{ route('audits.settings.audit-type-store') }}" class="audit-builder" data-audit-type-builder="new">
                @csrf
                <div style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa rodzaju audytu</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Zmienne globalne rodzaju audytu</label>
                            <div style="font-size:11px; color:#6b8294; margin-top:2px;">Stałe i parametry dostępne we wzorach wszystkich sekcji jako <strong>{token}</strong>.</div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="addVariableRow('new-type-variables-table')">+ Dodaj zmienną</button>
                    </div>
                    <table class="data-table" id="new-type-variables-table">
                        <thead>
                            <tr>
                                <th>Nazwa zmiennej</th>
                                <th>Token</th>
                                <th>Wartość domyślna</th>
                                <th style="width:40px;">Akcja</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                    <strong style="font-size:13px; color:#1d4f73;">Sekcje audytu</strong>
                    <button type="button" class="btn-secondary" onclick="addAuditTypeSection()">+ Dodaj sekcję</button>
                </div>

                <div id="audit-type-sections" style="display:grid; gap:8px; margin-top:8px;"></div>

                <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                    <button type="submit">Zapisz rodzaj audytu</button>
                </div>
                </form>

                <div style="margin-top:12px;">
                @forelse($auditTypes as $type)
                    <div class="audit-type-card" id="audit-type-card-{{ $type->id }}">
                        <div class="audit-type-header">
                            <button type="button" class="audit-type-title-btn" onclick="toggleAuditTypeDetails({{ $type->id }})">
                                <span class="audit-type-index">{{ $loop->iteration }}</span>
                                <span>{{ $type->name }}</span>
                                <span class="audit-type-chevron">&#9660;</span>
                            </button>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <button type="button" class="btn-secondary" onclick="toggleAuditTypeEditForm({{ $type->id }})">Edytuj</button>
                                <form method="POST" action="{{ route('audits.settings.audit-type-copy', $type) }}" onsubmit="return confirm('Skopiować rodzaj audytu?')">
                                    @csrf
                                    <button type="submit" class="btn-secondary">Kopiuj</button>
                                </form>
                                <form method="POST" action="{{ route('audits.settings.audit-type-destroy', $type) }}" onsubmit="return confirm('Usunąć rodzaj audytu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary">Usuń</button>
                                </form>
                            </div>
                        </div>

                        <div class="audit-type-details">

                        <form id="edit-audit-type-form-{{ $type->id }}" method="POST" action="{{ route('audits.settings.audit-type-update', $type) }}" class="audit-builder" style="margin-top:10px;" data-audit-type-builder="{{ $type->id }}">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa rodzaju audytu</label>
                                <input type="text" name="name" value="{{ $type->name }}" required>
                            </div>

                            <div style="margin-top:14px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Zmienne globalne rodzaju audytu</label>
                                        <div style="font-size:11px; color:#6b8294; margin-top:2px;">Stałe i parametry dostępne we wzorach wszystkich sekcji jako <strong>{token}</strong>.</div>
                                    </div>
                                    <button type="button" class="btn-secondary" onclick="addVariableRow('edit-type-variables-table-{{ $type->id }}')">+ Dodaj zmienną</button>
                                </div>
                                <table class="data-table" id="edit-type-variables-table-{{ $type->id }}">
                                    <thead>
                                        <tr>
                                            <th>Nazwa zmiennej</th>
                                            <th>Token</th>
                                            <th>Wartość domyślna</th>
                                            <th style="width:40px;">Akcja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($type->variables ?? [] as $varIndex => $var)
                                            <tr data-variable-item="1">
                                                <td><input type="text" name="variables[{{ $varIndex }}][name]" value="{{ $var['name'] ?? '' }}" placeholder="Np. Sprawność" oninput="updateVariableToken(this)"></td>
                                                <td><input type="text" class="variable-token-preview" name="variables[{{ $varIndex }}][key]" value="{{ $var['key'] ?? '' }}"></td>
                                                <td><input type="text" name="variables[{{ $varIndex }}][default_value]" value="{{ $var['default_value'] ?? '' }}" placeholder="Np. 0.85"></td>
                                                <td><button type="button" class="btn-trash-icon" onclick="removeVariableRow(this)" title="Usuń zmienną" aria-label="Usuń zmienną">🗑</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                                <strong style="font-size:13px; color:#1d4f73;">Sekcje audytu</strong>
                                <button type="button" class="btn-secondary" onclick="addAuditTypeSection('edit-audit-type-sections-{{ $type->id }}')">+ Dodaj sekcję</button>
                            </div>

                            <div id="edit-audit-type-sections-{{ $type->id }}" style="display:grid; gap:8px; margin-top:8px;">
                                @foreach($type->sections as $sectionIndex => $section)
                                    @php
                                        $sectionRows = collect($section->data_fields ?? [])->map(function ($row) {
                                            if (is_array($row)) {
                                                $options = collect($row['options'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all();

                                                return [
                                                    'key' => (string) ($row['key'] ?? \Illuminate\Support\Str::slug((string) ($row['name'] ?? ''), '_')),
                                                    'name' => (string) ($row['name'] ?? ''),
                                                    'unit' => (string) ($row['unit'] ?? ''),
                                                    'kind' => (string) ($row['kind'] ?? 'number'),
                                                    'parent_token' => (string) ($row['parent_token'] ?? ''),
                                                    'show_when' => (string) ($row['show_when'] ?? ''),
                                                    'default_value' => (string) ($row['default_value'] ?? ''),
                                                    'notes' => (string) ($row['notes'] ?? ''),
                                                    'options_text' => implode("\n", $options),
                                                ];
                                            }

                                            return [
                                                'key' => \Illuminate\Support\Str::slug((string) $row, '_'),
                                                'name' => (string) $row,
                                                'unit' => '',
                                                'kind' => 'number',
                                                'parent_token' => '',
                                                'show_when' => '',
                                                'default_value' => '',
                                                'notes' => '',
                                                'options_text' => '',
                                            ];
                                        })->filter(fn ($row) => trim($row['name']) !== '')->values();
                                    @endphp
                                    <div class="audit-type-section" data-section-item="1" draggable="false">
                                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <button type="button" class="section-collapse-arrow" onclick="toggleAuditTypeSectionCollapse(this)" title="Zwiń sekcję">▾</button>
                                                <strong class="section-order-label" style="font-size:14px; color:#10344c;"><span class="section-drag-handle" title="Przeciągnij sekcję">•••</span><span class="section-order-title">{{ $section->name }}</span></strong>
                                            </div>
                                            <button type="button" class="btn-secondary" onclick="removeAuditTypeSection(this)">Usuń</button>
                                        </div>

                                        <div class="section-collapsible-content" style="display:grid; gap:8px;">
                                            <div>
                                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa sekcji</label>
                                                <input type="text" class="section-name-input" name="sections[{{ $sectionIndex }}][name]" value="{{ $section->name }}" oninput="updateSectionHeaderTitle(this)" required>
                                            </div>
                                            <div>
                                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Zadania (jedno zadanie w linii)</label>
                                                <textarea name="sections[{{ $sectionIndex }}][tasks_text]" rows="3" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;">{{ implode("\n", $section->tasks ?? []) }}</textarea>
                                            </div>
                                            <div>
                                                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Tabela danych sekcji</label>
                                                    <button type="button" class="btn-secondary" onclick="addSectionDataRow('edit-section-data-table-{{ $type->id }}-{{ $sectionIndex }}', {{ $sectionIndex }})">+ Dodaj wiersz</button>
                                                </div>
                                                <table class="data-table data-table-fields" id="edit-section-data-table-{{ $type->id }}-{{ $sectionIndex }}">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:34px;"></th>
                                                            <th style="width:28px; text-align:center;">+</th>
                                                            <th>Dana</th>
                                                            <th>Token</th>
                                                            <th>Zależność od</th>
                                                            <th>Pokaż gdy =</th>
                                                            <th>Jednostka</th>
                                                            <th>Wartość</th>
                                                            <th>Uwagi</th>
                                                            <th>Opcje listy rozwijanej</th>
                                                            <th>Akcja</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($sectionRows as $rowIndex => $row)
                                                            <tr data-draggable-row="1" draggable="false" class="{{ !empty($row['parent_token']) ? 'is-dependent-row' : '' }}">
                                                                <td><span class="row-drag-handle" title="Przeciągnij wiersz">•••</span></td>
                                                                <td style="text-align:center;"><input type="checkbox" class="row-insert-anchor" title="Dodawaj nowy wiersz pod tym wierszem" aria-label="Wstawianie pod tym wierszem"></td>
                                                                <td><input type="text" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][name]" value="{{ $row['name'] }}" oninput="updateRowToken(this)"></td>
                                                                <td>
                                                                    <input type="text" class="row-token-preview" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][key]" value="{{ $row['key'] !== '' ? $row['key'] : \Illuminate\Support\Str::slug((string) $row['name'], '_') }}" onfocus="rememberTokenBeforeEdit(this)" oninput="handleTokenEdit(this)" onblur="handleTokenEdit(this)">
                                                                </td>
                                                                <td>
                                                                    <select class="row-parent-token" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][parent_token]" onchange="handleDependencyParentChange(this)">
                                                                        <option value="">—</option>
                                                                        @foreach($sectionRows as $dependencyRow)
                                                                            @php
                                                                                $dependencyToken = $dependencyRow['key'] !== ''
                                                                                    ? $dependencyRow['key']
                                                                                    : \Illuminate\Support\Str::slug((string) $dependencyRow['name'], '_');
                                                                            @endphp
                                                                            <option value="{{ $dependencyToken }}" @selected((string) ($row['parent_token'] ?? '') === (string) $dependencyToken)>{{ $dependencyToken }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="row-show-when" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][show_when]" value="{{ (string) ($row['show_when'] ?? '') }}" placeholder="np. tak / nie / opcja"></td>
                                                                <td>
                                                                    <select name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][unit]" onchange="handleRowUnitKind(this)">
                                                                        <option value="">—</option>
                                                                        @foreach($units as $unit)
                                                                            <option value="{{ $unit->name }}" @selected($row['unit'] === $unit->name)>{{ $unit->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][default_value]" value="{{ $row['default_value'] }}"></td>
                                                                <td><input type="text" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][notes]" value="{{ $row['notes'] }}"></td>
                                                                <td>
                                                                    <textarea class="row-options-input" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][options_text]" rows="2" placeholder="Jedna opcja w linii" style="display:{{ ($row['kind'] ?? 'number') === 'select' ? 'block' : 'none' }};">{{ $row['options_text'] ?? '' }}</textarea>
                                                                </td>
                                                                <td><button type="button" class="btn-trash-icon" onclick="removeDataRow(this)" title="Usuń wiersz" aria-label="Usuń wiersz">🗑</button></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div>
                                                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Wzory sekcji</label>
                                                    <button type="button" class="btn-secondary" onclick="addSectionFormulaRow('edit-section-formulas-{{ $type->id }}-{{ $sectionIndex }}', {{ $sectionIndex }}, 'edit-section-data-table-{{ $type->id }}-{{ $sectionIndex }}')">+ Dodaj wzór</button>
                                                </div>
                                                <div style="font-size:12px; color:#4c6373; margin:0 0 6px;">Tokeny pól: <strong>{token}</strong>, np. <strong>({moc_nominalna} * 1.2) / 1000</strong>. Obsługiwane funkcje: <strong>IF(warunek; jeśli_tak; jeśli_nie)</strong>, <strong>AND(a; b)</strong>, <strong>OR(a; b)</strong>, <strong>NOT(a)</strong>, <strong>MAX(a; b)</strong>, <strong>MIN(a; b)</strong>, <strong>ABS(x)</strong>, <strong>SQRT(x)</strong>, <strong>ROUND(x; miejsca)</strong>. Porównania: <strong>=</strong>, <strong>&lt;&gt;</strong>, <strong>&lt;</strong>, <strong>&gt;</strong>, <strong>&lt;=</strong>, <strong>&gt;=</strong>. Argumenty funkcji rozdzielaj średnikiem <strong>;</strong>. Listy Select: wartość nr 1, 2, 3... Tak/Nie: 1/0.</div>
                                                <div class="token-helper">
                                                    <div class="token-helper-title">Kliknij token, aby wstawić go do wzoru</div>
                                                    <div class="token-list section-token-list"></div>
                                                    <div class="token-helper-hint">Pola danych: nieb. | Wyniki wzorów: ziel. Przy zmianie tokenu jego wystąpienia we wzorach tej sekcji aktualizują się automatycznie.</div>
                                                </div>
                                                <div id="edit-section-formulas-{{ $type->id }}-{{ $sectionIndex }}" style="display:grid; gap:8px;">
                                                    @foreach(($section->formulas ?? []) as $formulaIndex => $formula)
                                                        <div class="audit-type-section section-formula-row" data-formula-item="1" draggable="true" style="padding:8px;">
                                                            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                                                                <div style="display:flex; align-items:center; gap:8px;">
                                                                    <button type="button" class="section-collapse-arrow" onclick="toggleFormulaCollapse(this)" title="Zwiń wzór">▾</button>
                                                                    <strong class="formula-order-label" style="font-size:12px; color:#1d4f73;"><span class="formula-drag-handle" title="Przeciągnij wzór">•••</span><span class="formula-order-title">{{ trim((string) ($formula['label'] ?? '')) !== '' ? (string) $formula['label'] : 'Nowy wzór' }}</span></strong>
                                                                </div>
                                                                <button type="button" class="btn-secondary" onclick="removeFormulaRow(this)">Usuń</button>
                                                            </div>
                                                            <div class="formula-collapsible-content" style="display:grid; grid-template-columns:1fr 160px 160px; gap:8px;">
                                                                <div>
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Co obliczyć</label>
                                                                    <input type="text" class="formula-name-input" name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][label]" value="{{ (string) ($formula['label'] ?? '') }}" placeholder="Np. Zużycie roczne" oninput="updateFormulaHeaderTitle(this)">
                                                                </div>
                                                                <div>
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Token wyniku</label>
                                                                    <input type="text" class="formula-token-preview" name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][key]" value="{{ (string) ($formula['key'] ?? '') }}" placeholder="auto" onfocus="rememberTokenBeforeEdit(this)" oninput="handleFormulaTokenEdit(this)" onblur="handleFormulaTokenEdit(this)">
                                                                </div>
                                                                <div>
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Jednostka wyniku</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][unit]" value="{{ (string) ($formula['unit'] ?? '') }}" placeholder="Np. kW">
                                                                </div>
                                                                <div style="grid-column:1 / -1;">
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Wzór</label>
                                                                    <textarea name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][expression]" rows="2" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;" placeholder="Np. ({moc_nominalna} * {czas_pracy_h}) / 1000 (bez kW w polu wzoru)" onfocus="setActiveFormulaTextarea(this)">{{ (string) ($formula['expression'] ?? '') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="formula-collapsible-content" style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                                                                <button type="button" class="btn-secondary" onclick="validateSectionFormula(this, 'edit-section-data-table-{{ $type->id }}-{{ $sectionIndex }}')">Sprawdź wzór</button>
                                                                <span class="formula-validation-message" style="font-size:12px; color:#4c6373;"></span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                                <button type="submit">Zapisz zmiany rodzaju audytu</button>
                            </div>
                        </form>

                        @if(!empty($type->variables))
                            <div style="margin-bottom:10px;">
                                <div style="font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px;">Zmienne globalne:</div>
                                <table class="data-table" style="margin:0;">
                                    <thead><tr><th>Nazwa</th><th>Token</th><th>Wartość domyślna</th></tr></thead>
                                    <tbody>
                                        @foreach($type->variables as $var)
                                            <tr>
                                                <td>{{ $var['name'] ?? '' }}</td>
                                                <td><code>{{ $var['key'] ?? '' }}</code></td>
                                                <td>{{ $var['default_value'] ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @forelse($type->sections as $section)
                            <div class="audit-type-section">
                                <div style="font-weight:800; font-size:14px; color:#10344c; margin-bottom:6px;">{{ $section->name }}</div>
                                <div style="font-size:12px; color:#4c6373;"><strong>Zadania:</strong> {{ !empty($section->tasks) ? implode(', ', $section->tasks) : 'Brak' }}</div>

                                @php
                                    $rows = collect($section->data_fields ?? [])->map(function ($row) {
                                        if (is_array($row)) {
                                            return [
                                                'key' => (string) ($row['key'] ?? \Illuminate\Support\Str::slug((string) ($row['name'] ?? ''), '_')),
                                                'name' => (string) ($row['name'] ?? ''),
                                                'unit' => (string) ($row['unit'] ?? ''),
                                            ];
                                        }

                                        return [
                                            'key' => \Illuminate\Support\Str::slug((string) $row, '_'),
                                            'name' => (string) $row,
                                            'unit' => '',
                                        ];
                                    })->filter(fn ($row) => trim($row['name']) !== '')->values();
                                @endphp

                                @if($rows->isNotEmpty())
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Dana</th>
                                                <th>Token</th>
                                                <th>Jednostka</th>
                                                <th>Wartość</th>
                                                <th>Uwagi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $row)
                                                <tr>
                                                    <td>{{ $row['name'] }}</td>
                                                    <td>{{ $row['key'] !== '' ? $row['key'] : \Illuminate\Support\Str::slug((string) $row['name'], '_') }}</td>
                                                    <td>{{ $row['unit'] !== '' ? $row['unit'] : '—' }}</td>
                                                    <td>—</td>
                                                    <td>—</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div style="font-size:12px; color:#4c6373; margin-top:4px;"><strong>Dane do wpisania:</strong> Brak</div>
                                @endif

                                @if(!empty($section->formulas))
                                    <div style="font-size:12px; color:#4c6373; margin-top:8px;"><strong>Wzory sekcji:</strong></div>
                                    <ul style="margin:6px 0 0 16px; padding:0; color:#4c6373; font-size:12px;">
                                        @foreach($section->formulas as $formula)
                                            @if(!empty($formula['label']) && !empty($formula['expression']))
                                                <li><strong>{{ $formula['label'] }}:</strong> {{ $formula['expression'] }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @empty
                            <div class="muted" style="margin-top:8px;">Brak sekcji</div>
                        @endforelse
                        </div>
                    </div>
                @empty
                    <div class="muted">Brak zdefiniowanych rodzajów audytu.</div>
                @endforelse
                </div>
            </div>
        </div>
        @endif

        @if($activeTab === 'iso50001')
        <div class="settings-section open" id="settings-iso50001">
            <div class="settings-body" style="display:block;">
                <div style="display:grid; grid-template-columns: 1fr; gap:14px; margin-top:10px;">
                    <section class="audit-type-card">
                        <h3 style="margin:0 0 10px;">Przykladowy audyt do edycji (szablon krokow)</h3>
                        <form method="POST" action="{{ route('audits.settings.iso50001.template-update') }}" style="display:grid; gap:10px;">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa szablonu</label>
                                <input type="text" name="name" value="{{ old('name', $isoTemplate->name ?? 'Szablon ISO 50001') }}" required>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Definicja krokow (JSON)</label>
                                <textarea name="template_json" rows="16" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:13px; font-family: Consolas, monospace;">{{ old('template_json', $isoTemplateJson ?? '[]') }}</textarea>
                            </div>
                            <div style="display:flex; justify-content:flex-end;">
                                <button type="submit">Zapisz konfiguracje ISO50001</button>
                            </div>
                        </form>
                    </section>

                    <section class="audit-type-card">
                        <h3 style="margin:0 0 10px;">Utworz audyt ISO50001 dla klienta</h3>
                        <form method="POST" action="{{ route('audits.settings.iso50001.audit-store') }}" style="display:grid; grid-template-columns: 1fr 1fr 220px auto; gap:10px; align-items:end;">
                            @csrf
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Klient</label>
                                <select name="client_user_id" required>
                                    <option value="">Wybierz klienta</option>
                                    @foreach($isoClients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Firma</label>
                                <select name="company_id" required>
                                    <option value="">Wybierz firme</option>
                                    @foreach($isoCompanies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Data zakonczenia audytu</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}">
                            </div>
                            <div>
                                <button type="submit">Utworz audyt</button>
                            </div>
                            <div style="grid-column:1 / -1;">
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa audytu</label>
                                <input type="text" name="title" value="{{ old('title', 'Audyt ISO 50001 - '.now()->format('Y-m-d')) }}" required>
                            </div>
                        </form>
                    </section>

                    <section class="audit-type-card">
                        <h3 style="margin:0 0 10px;">Istniejace audyty ISO50001 (edycja)</h3>
                        <div style="display:grid; gap:8px;">
                            @forelse($isoAudits as $isoAudit)
                                <form method="POST" action="{{ route('audits.settings.iso50001.audit-update', $isoAudit) }}" style="border:1px solid #dfeaf3; border-radius:10px; padding:10px; background:#fbfdff; display:grid; grid-template-columns: 1fr 1fr 1fr 180px 170px auto; gap:8px; align-items:end;">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#4c6373;">Nazwa audytu</label>
                                        <input type="text" name="title" value="{{ $isoAudit->title }}" required>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#4c6373;">Klient</label>
                                        <select name="client_user_id" required>
                                            @foreach($isoClients as $client)
                                                <option value="{{ $client->id }}" @selected((int) $isoAudit->created_by_user_id === (int) $client->id)>{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#4c6373;">Firma</label>
                                        <select name="company_id" required>
                                            @foreach($isoCompanies as $company)
                                                <option value="{{ $company->id }}" @selected((int) $isoAudit->company_id === (int) $company->id)>{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#4c6373;">Data zakonczenia</label>
                                        <input type="date" name="due_date" value="{{ $isoAudit->due_date?->format('Y-m-d') }}">
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#4c6373;">Status</label>
                                        <select name="status" required>
                                            @foreach($isoStatusOptions as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}" @selected($isoAudit->status === $statusKey)>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display:flex; gap:6px;">
                                        <button type="submit">Zapisz</button>
                                        <a href="{{ route('iso50001.review', $isoAudit) }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px; display:inline-flex; align-items:center;">Podglad</a>
                                    </div>
                                </form>
                            @empty
                                <div class="muted">Brak audytow ISO50001.</div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
        @endif

        @if($activeTab === 'ustawienia')
        <div class="settings-section open" id="settings-units">
            <div class="settings-body" style="display:block;">

            <form method="POST" action="{{ route('audits.settings.unit-store') }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap; margin-top:10px;">
                @csrf
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nowa jednostka</label>
                    <input type="text" name="name" placeholder="Np. kWh" required>
                </div>
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Typ jednostki</label>
                    <select name="kind" required>
                        <option value="number">Liczba</option>
                        <option value="text">Opis</option>
                        <option value="boolean">Tak / Nie</option>
                        <option value="select">Lista rozwijana</option>
                    </select>
                </div>
                <button type="submit">Dodaj jednostkę</button>
            </form>

            <table class="data-table" style="margin-top:12px; max-width:860px;">
                <thead>
                    <tr>
                        <th style="width:80px;">#</th>
                        <th>Jednostka</th>
                        <th>Typ</th>
                        <th style="width:220px;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>
                                @if($unit->kind === 'text')
                                    Opis
                                @elseif($unit->kind === 'boolean')
                                    Tak / Nie
                                @elseif($unit->kind === 'select')
                                    Lista rozwijana
                                @else
                                    Liczba
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn-secondary" onclick="toggleUnitEditForm({{ $unit->id }})">Edytuj</button>
                                <form method="POST" action="{{ route('audits.settings.unit-destroy', $unit) }}" style="display:inline;" onsubmit="return confirm('Usunąć jednostkę?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary">Usuń</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="unit-edit-row-{{ $unit->id }}" style="display:none;">
                            <td colspan="4" style="background:#fbfdff;">
                                <form method="POST" action="{{ route('audits.settings.unit-update', $unit) }}" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa</label>
                                        <input type="text" name="name" value="{{ $unit->name }}" required>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Typ jednostki</label>
                                        <select name="kind" required>
                                            <option value="number" @selected(($unit->kind ?? 'number') === 'number')>Liczba</option>
                                            <option value="text" @selected(($unit->kind ?? 'number') === 'text')>Opis</option>
                                            <option value="boolean" @selected(($unit->kind ?? 'number') === 'boolean')>Tak / Nie</option>
                                            <option value="select" @selected(($unit->kind ?? 'number') === 'select')>Lista rozwijana</option>
                                        </select>
                                    </div>
                                    <button type="submit">Zapisz</button>
                                    <button type="button" class="btn-secondary" onclick="toggleUnitEditForm({{ $unit->id }})">Anuluj</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">Brak zdefiniowanych jednostek.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
        @endif

        @if($activeTab === 'biale-certyfikaty')
        <div class="settings-section open" id="settings-white-certificates">
            <div class="settings-body" style="display:block; padding:12px;">
                <div class="muted">Sekcja w przygotowaniu.</div>
            </div>
        </div>
        @endif

        @if($activeTab === 'ai-audyty')
        <div class="settings-section open" id="settings-ai-audits">
            <div class="settings-body" style="display:block; padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
                    <div>
                        <h2 style="margin:0; font-size:18px;">🤖 Asystent AI do audytów</h2>
                        <p style="margin:6px 0 0; font-size:13px; color:#5a7390;">Prowadź klientów przez zbieranie danych do audytów energetycznych i ISO 50001 za pomocą asystenta Claude AI.</p>
                    </div>
                    <a href="{{ route('ai.create', ['type' => 'energy_audit']) }}" style="display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; border-radius:10px; font-weight:700; font-size:14px; text-decoration:none;">
                        + Nowa rozmowa AI
                    </a>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:12px; margin-bottom:24px;">
                    <a href="{{ route('ai.create', ['type' => 'energy_audit']) }}" style="display:block; text-decoration:none; border:1px solid #d4ebf8; border-radius:14px; padding:18px; background:#f0f9ff; transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(14,137,216,.15)'" onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:32px; margin-bottom:8px;">⚡</div>
                        <div style="font-weight:700; color:#0e4f7a; font-size:15px;">Audyt energetyczny</div>
                        <div style="font-size:12px; color:#5a7a90; margin-top:4px;">AI prowadzi klienta przez zbieranie danych do audytu energetycznego budynku / instalacji</div>
                    </a>
                    <a href="{{ route('ai.create', ['type' => 'iso50001']) }}" style="display:block; text-decoration:none; border:1px solid #d4eaf8; border-radius:14px; padding:18px; background:#f0f6ff; transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(14,89,216,.15)'" onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:32px; margin-bottom:8px;">🏭</div>
                        <div style="font-weight:700; color:#1a3f7a; font-size:15px;">ISO 50001</div>
                        <div style="font-size:12px; color:#5a7a90; margin-top:4px;">AI pomoże wypełnić wymagania systemu zarządzania energią zgodnie z ISO 50001</div>
                    </a>
                    <a href="{{ route('ai.create', ['type' => 'offer']) }}" style="display:block; text-decoration:none; border:1px solid #fde9c0; border-radius:14px; padding:18px; background:#fffbf0; transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(200,130,14,.15)'" onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:32px; margin-bottom:8px;">📄</div>
                        <div style="font-weight:700; color:#7a4e0a; font-size:15px;">Oferta</div>
                        <div style="font-size:12px; color:#8a7050; margin-top:4px;">AI zbiera dane potrzebne do przygotowania wyceny i oferty na audyt</div>
                    </a>
                    <a href="{{ route('ai.create') }}" style="display:block; text-decoration:none; border:1px solid #e0e8f0; border-radius:14px; padding:18px; background:#f8fafb; transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(40,80,110,.1)'" onmouseout="this.style.boxShadow='none'">
                        <div style="font-size:32px; margin-bottom:8px;">💬</div>
                        <div style="font-weight:700; color:#28485f; font-size:15px;">Ogólny</div>
                        <div style="font-size:12px; color:#5a7090; margin-top:4px;">Dowolna rozmowa z asystentem — pytania, analizy, konsultacje energetyczne</div>
                    </a>
                </div>

                @php
                    $aiConvs = \App\Models\AiConversation::where('user_id', auth()->id())
                        ->where('status', 'active')
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp

                @if($aiConvs->isNotEmpty())
                <div style="margin-top:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <strong style="font-size:14px; color:#163f5b;">Ostatnie rozmowy AI</strong>
                        <a href="{{ route('ai.index') }}" style="font-size:13px; color:#0e89d8; font-weight:600; text-decoration:none;">Wszystkie →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Temat</th>
                                <th>Typ</th>
                                <th>Data</th>
                                <th style="width:100px;">Akcja</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aiConvs as $conv)
                            @php
                                $label = match($conv->context_type) {
                                    'energy_audit' => '⚡ Audyt energetyczny',
                                    'iso50001'     => '🏭 ISO 50001',
                                    'offer'        => '📄 Oferta',
                                    default        => '💬 Ogólny',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $conv->title }}</td>
                                <td style="font-size:13px;">{{ $label }}</td>
                                <td style="font-size:13px; color:#5a7390;">{{ $conv->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('ai.show', $conv) }}" class="btn-secondary" style="display:inline-flex;padding:5px 10px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;background:#dbe9f5;color:#1d4f73;border:1px solid #c0d8ee;">Otwórz</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align:center; padding:30px; color:#8a9bac; border:1px dashed #c8d8e6; border-radius:12px;">
                    <div style="font-size:36px; margin-bottom:8px;">🤖</div>
                    <div style="font-weight:600; margin-bottom:4px;">Brak aktywnych rozmów</div>
                    <div style="font-size:13px;">Wybierz typ audytu powyżej, by rozpocząć pierwszą rozmowę z asystentem.</div>
                </div>
                @endif

            </div>
        </div>
        @endif
    </section>

    <script>
        const auditUnits = @json(($units ?? collect())->pluck('name')->values());
        const auditUnitKinds = @json(($units ?? collect())->pluck('kind', 'name'));

        function toggleSettingsSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (!section) {
                return;
            }

            section.classList.toggle('open');
        }

        function toggleUnitEditForm(unitId) {
            const row = document.getElementById('unit-edit-row-' + unitId);
            if (!row) {
                return;
            }

            const visible = row.style.display !== 'none';
            row.style.display = visible ? 'none' : 'table-row';
        }

        function toggleAuditTypeForm() {
            const form = document.getElementById('add-audit-type-form');
            if (!form) {
                return;
            }

            form.style.display = 'block';
        }

        function toggleAuditTypeEditForm(typeId) {
            const form = document.getElementById('edit-audit-type-form-' + typeId);
            const card = document.getElementById('audit-type-card-' + typeId);
            if (!form) {
                return;
            }

            if (card) {
                card.classList.add('open');
            }

            form.style.display = 'block';
        }

        function toggleAuditTypeDetails(typeId) {
            const card = document.getElementById('audit-type-card-' + typeId);
            if (!card) {
                return;
            }

            card.classList.toggle('open');
        }

        function addAuditTypeSection(containerId = 'audit-type-sections', defaults = {}) {
            const container = document.getElementById(containerId);
            if (!container) {
                return;
            }

            const index = container.children.length;
            const wrapper = document.createElement('div');
            wrapper.className = 'audit-type-section';
            wrapper.setAttribute('data-section-item', '1');
            wrapper.setAttribute('draggable', 'false');
            wrapper.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button type="button" class="section-collapse-arrow" onclick="toggleAuditTypeSectionCollapse(this)" title="Zwiń sekcję">▾</button>
                        <strong class="section-order-label" style="font-size:12px; color:#1d4f73;"><span class="section-drag-handle" title="Przeciągnij sekcję">•••</span><span class="section-order-title">${defaults.name || 'Nowa sekcja'}</span></strong>
                    </div>
                    <button type="button" class="btn-secondary" onclick="removeAuditTypeSection(this)">Usuń</button>
                </div>
                <div class="section-collapsible-content" style="display:grid; gap:8px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa sekcji</label>
                        <input type="text" class="section-name-input" name="sections[${index}][name]" value="${defaults.name || ''}" placeholder="Np. Dane wejściowe" oninput="updateSectionHeaderTitle(this)" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Zadania (jedno zadanie w linii)</label>
                        <textarea name="sections[${index}][tasks_text]" rows="3" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;">${defaults.tasksText || ''}</textarea>
                    </div>
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Tabela danych sekcji</label>
                            <button type="button" class="btn-secondary" onclick="addSectionDataRow('${containerId}-table-${index}', ${index})">+ Dodaj wiersz</button>
                        </div>
                        <table class="data-table data-table-fields" id="${containerId}-table-${index}">
                            <thead>
                                <tr>
                                    <th style="width:34px;"></th>
                                    <th style="width:28px; text-align:center;">+</th>
                                    <th>Dana</th>
                                    <th>Token</th>
                                    <th>Zależność od</th>
                                    <th>Pokaż gdy =</th>
                                    <th>Jednostka</th>
                                    <th>Wartość</th>
                                    <th>Uwagi</th>
                                    <th>Opcje listy rozwijanej</th>
                                    <th>Akcja</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:6px;">
                            <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin:0;">Wzory sekcji</label>
                            <button type="button" class="btn-secondary" onclick="addSectionFormulaRow('${containerId}-formulas-${index}', ${index}, '${containerId}-table-${index}')">+ Dodaj wzór</button>
                        </div>
                        <div style="font-size:12px; color:#4c6373; margin:0 0 6px;">Tokeny pól: <strong>{token}</strong>, np. <strong>({moc_nominalna} * 1.2) / 1000</strong>. Możesz też używać tokenu wyniku innego wzoru. We wzorze wpisuj tylko liczby, operatory i tokeny (bez jednostek).</div>
                        <div class="token-helper">
                            <div class="token-helper-title">Kliknij token, aby wstawić go do wzoru</div>
                            <div class="token-list section-token-list"></div>
                            <div class="token-helper-hint">Pola danych: nieb. | Wyniki wzorów: ziel. Przy zmianie tokenu jego wystąpienia we wzorach tej sekcji aktualizują się automatycznie.</div>
                        </div>
                        <div id="${containerId}-formulas-${index}" style="display:grid; gap:8px;"></div>
                    </div>
                </div>
            `;

            container.appendChild(wrapper);
            initializeSectionDragAndDrop(container);
            renumberAuditTypeSections(container);
            syncSectionHeaderTitle(wrapper);
            addSectionDataRow(`${containerId}-table-${index}`, index);
        }

        function syncSectionHeaderTitle(section) {
            if (!section) {
                return;
            }

            const nameInput = section.querySelector('.section-name-input');
            const titleNode = section.querySelector('.section-order-title');
            if (!nameInput || !titleNode) {
                return;
            }

            const value = String(nameInput.value || '').trim();
            titleNode.textContent = value !== '' ? value : 'Nowa sekcja';
        }

        function updateSectionHeaderTitle(input) {
            const section = input?.closest('[data-section-item]');
            syncSectionHeaderTitle(section);
        }

        function addSectionDataRow(tableId, sectionIndex) {
            const table = document.querySelector(`#${tableId} tbody`);
            if (!table) {
                return;
            }

            const rowIndex = table.children.length;
            const row = document.createElement('tr');
            row.setAttribute('data-draggable-row', '1');
            row.setAttribute('draggable', 'false');
            const unitOptions = ['<option value="">—</option>']
                .concat(auditUnits.map((unit) => `<option value="${unit}">${unit}</option>`))
                .join('');

            row.innerHTML = `
                <td><span class="row-drag-handle" title="Przeciągnij wiersz">•••</span></td>
                <td style="text-align:center;"><input type="checkbox" class="row-insert-anchor" title="Dodawaj nowy wiersz pod tym wierszem" aria-label="Wstawianie pod tym wierszem"></td>
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][name]" placeholder="Np. Moc nominalna" oninput="updateRowToken(this)"></td>
                <td><input type="text" class="row-token-preview" name="sections[${sectionIndex}][rows][${rowIndex}][key]" value="pole" onfocus="rememberTokenBeforeEdit(this)" oninput="handleTokenEdit(this)" onblur="handleTokenEdit(this)"></td>
                <td>
                    <select class="row-parent-token" name="sections[${sectionIndex}][rows][${rowIndex}][parent_token]" onchange="handleDependencyParentChange(this)">
                        <option value="">—</option>
                    </select>
                </td>
                <td><input type="text" class="row-show-when" name="sections[${sectionIndex}][rows][${rowIndex}][show_when]" placeholder="np. tak / nie / opcja"></td>
                <td>
                    <select name="sections[${sectionIndex}][rows][${rowIndex}][unit]" onchange="handleRowUnitKind(this)">
                        ${unitOptions}
                    </select>
                </td>
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][default_value]" placeholder="Wartość domyślna (opcjonalnie)"></td>
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][notes]" placeholder="Uwagi (opcjonalnie)"></td>
                <td><textarea class="row-options-input" name="sections[${sectionIndex}][rows][${rowIndex}][options_text]" rows="2" placeholder="Jedna opcja w linii" style="display:none;"></textarea></td>
                <td><button type="button" class="btn-trash-icon" onclick="removeDataRow(this)" title="Usuń wiersz" aria-label="Usuń wiersz">🗑</button></td>
            `;

            const currentTable = table.closest('table');
            const insertAfterRow = resolveInsertAfterSelectedRow(currentTable);

            if (insertAfterRow && insertAfterRow.parentElement === table) {
                table.insertBefore(row, insertAfterRow.nextElementSibling);
            } else {
                table.appendChild(row);
            }
            const nameInput = row.querySelector('input[name$="[name]"]');
            if (nameInput) {
                updateRowToken(nameInput);
            }

            const tokenInput = row.querySelector('.row-token-preview');
            if (tokenInput) {
                tokenInput.dataset.prevToken = normalizeToken(tokenInput.value);
            }

            const select = row.querySelector('select[name$="[unit]"]');
            if (select) {
                handleRowUnitKind(select);
            }

            const tableElement = table.closest('table');
            if (tableElement) {
                initializeRowDragAndDrop(tableElement);
                renumberSectionRows(tableElement);
            }

            refreshTokenHelpersInScope(getTokenScopeRoot(row));
            const parentSelect = row.querySelector('.row-parent-token');
            syncDependencyRowVisual(parentSelect);
        }

        function resolveInsertAfterSelectedRow(tableElement) {
            if (!tableElement) {
                return null;
            }

            const tbody = tableElement.querySelector('tbody');
            if (!tbody) {
                return null;
            }

            const checkedAnchor = tbody.querySelector('.row-insert-anchor:checked');
            const checkedRow = checkedAnchor?.closest('tr[data-draggable-row]');
            if (checkedRow && checkedRow.parentElement === tbody) {
                return checkedRow;
            }

            const activeElement = document.activeElement;
            if (!(activeElement instanceof HTMLElement)) {
                return null;
            }

            if (!tbody.contains(activeElement)) {
                return null;
            }

            const activeRow = activeElement.closest('tr[data-draggable-row]');
            if (!activeRow || activeRow.parentElement !== tbody) {
                return null;
            }

            return activeRow;
        }

        function setInsertAnchorForRow(row, tbody) {
            if (!row || !tbody) {
                return;
            }

            tbody.querySelectorAll('.row-insert-anchor').forEach((checkbox) => {
                checkbox.checked = checkbox.closest('tr[data-draggable-row]') === row;
            });
        }

        function removeAuditTypeSection(button) {
            const section = button?.closest('[data-section-item]');
            const container = section?.parentElement;
            const scopeRoot = getTokenScopeRoot(section);
            if (!section || !container) {
                return;
            }

            section.remove();
            renumberAuditTypeSections(container);
            refreshTokenHelpersInScope(scopeRoot);
            applyDependencyVisualDepthInScope(scopeRoot);
        }

        function toggleAuditTypeSectionCollapse(button) {
            const section = button?.closest('[data-section-item]');
            if (!section) {
                return;
            }

            section.classList.toggle('section-collapsed');
            const isCollapsed = section.classList.contains('section-collapsed');
            button.textContent = isCollapsed ? '▸' : '▾';
            button.title = isCollapsed ? 'Rozwiń sekcję' : 'Zwiń sekcję';
            section.draggable = isCollapsed;
        }

        function addVariableRow(tableId) {
            const table = document.querySelector(`#${tableId} tbody`);
            if (!table) {
                return;
            }

            const index = table.querySelectorAll('tr[data-variable-item]').length;
            const row = document.createElement('tr');
            row.setAttribute('data-variable-item', '1');
            row.innerHTML = `
                <td><input type="text" name="variables[${index}][name]" placeholder="Np. Sprawność" oninput="updateVariableToken(this)"></td>
                <td><input type="text" class="variable-token-preview" name="variables[${index}][key]" value="zmienna"></td>
                <td><input type="text" name="variables[${index}][default_value]" placeholder="Np. 0.85"></td>
                <td><button type="button" class="btn-trash-icon" onclick="removeVariableRow(this)" title="Usuń zmienną" aria-label="Usuń zmienną">🗑</button></td>
            `;
            table.appendChild(row);
            refreshTokenHelpersInScope(getTokenScopeRoot(row));
        }

        function removeVariableRow(button) {
            const row = button?.closest('tr[data-variable-item]');
            const table = row?.closest('table');
            const scopeRoot = getTokenScopeRoot(button);
            if (!row) {
                return;
            }

            row.remove();
            if (table) {
                renumberVariableRows(table);
            }
            refreshTokenHelpersInScope(scopeRoot);
        }

        function updateVariableToken(nameInput) {
            const row = nameInput?.closest('tr[data-variable-item]');
            if (!row) {
                return;
            }

            const tokenInput = row.querySelector('.variable-token-preview');
            if (!tokenInput) {
                return;
            }

            const raw = normalizeToken(nameInput.value);
            const unique = ensureUniqueTokenInScope(tokenInput, raw !== '' ? raw : 'zmienna');
            tokenInput.value = unique;
            refreshTokenHelpersInScope(getTokenScopeRoot(tokenInput));
        }

        function renumberVariableRows(tableElement) {
            if (!tableElement) {
                return;
            }

            Array.from(tableElement.querySelectorAll('tbody > tr[data-variable-item]')).forEach((row, idx) => {
                row.querySelectorAll('input[name]').forEach((field) => {
                    const currentName = field.getAttribute('name') || '';
                    if (!currentName.includes('variables[')) {
                        return;
                    }
                    field.setAttribute('name', currentName.replace(/variables\[\d+\]/, `variables[${idx}]`));
                });
            });
        }


        function removeFormulaRow(button) {
            const formulaRow = button?.closest('[data-formula-item]');
            const container = formulaRow?.parentElement;
            if (!formulaRow || !container) {
                return;
            }

            formulaRow.remove();
            renumberFormulaRows(container);
        }

        function syncFormulaHeaderTitle(formulaRow) {
            if (!formulaRow) {
                return;
            }

            const input = formulaRow.querySelector('.formula-name-input');
            const title = formulaRow.querySelector('.formula-order-title');
            if (!input || !title) {
                return;
            }

            const value = String(input.value || '').trim();
            title.textContent = value !== '' ? value : 'Nowy wzór';
        }

        function updateFormulaHeaderTitle(input) {
            const formulaRow = input?.closest('[data-formula-item]');
            syncFormulaHeaderTitle(formulaRow);
            updateFormulaToken(input);
        }

        function updateFormulaToken(labelInput) {
            const formulaRow = labelInput?.closest('[data-formula-item]');
            if (!formulaRow) {
                return;
            }

            const tokenInput = formulaRow.querySelector('.formula-token-preview');
            if (!tokenInput || tokenInput.dataset.manualEdit === '1') {
                return;
            }

            const raw = normalizeToken(labelInput.value);
            const base = raw !== '' ? raw : 'wzor';
            const oldToken = normalizeToken(tokenInput.dataset.prevToken || tokenInput.value);
            const unique = ensureUniqueTokenInScope(tokenInput, base);
            tokenInput.value = unique;
            tokenInput.dataset.prevToken = unique;

            if (oldToken !== '' && oldToken !== unique) {
                replaceTokenInScopeFormulas(getTokenScopeRoot(tokenInput), oldToken, unique);
            }

            refreshTokenHelpersInScope(getTokenScopeRoot(tokenInput));
        }

        function toggleFormulaCollapse(button) {
            const formulaRow = button?.closest('[data-formula-item]');
            if (!formulaRow) {
                return;
            }

            formulaRow.classList.toggle('formula-collapsed');
            const isCollapsed = formulaRow.classList.contains('formula-collapsed');
            button.textContent = isCollapsed ? '▸' : '▾';
            button.title = isCollapsed ? 'Rozwiń wzór' : 'Zwiń wzór';
        }

        function initializeSectionDragAndDrop(container) {
            if (!container || container.dataset.sectionDndReady === '1') {
                return;
            }

            container.dataset.sectionDndReady = '1';
            let draggingSection = null;

            container.addEventListener('mousedown', function (event) {
                const section = event.target.closest('[data-section-item]');
                if (!section || section.parentElement !== container) {
                    return;
                }

                section.dataset.dragAllowed = event.target.closest('.section-drag-handle') ? '1' : '0';
            });

            container.addEventListener('dragstart', function (event) {
                if (event.target.closest('[data-formula-item]')) {
                    return;
                }

                const section = event.target.closest('[data-section-item]');
                if (!section || section.parentElement !== container) {
                    event.preventDefault();
                    return;
                }

                if (section.dataset.dragAllowed !== '1') {
                    event.preventDefault();
                    return;
                }

                if (!section.classList.contains('section-collapsed')) {
                    event.preventDefault();
                    return;
                }

                draggingSection = section;
                section.classList.add('is-dragging');
                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', 'section');
                }
            });

            container.addEventListener('dragover', function (event) {
                if (!draggingSection) {
                    return;
                }

                if (event.target.closest('[data-formula-item]')) {
                    return;
                }

                event.preventDefault();
                const targetSection = event.target.closest('[data-section-item]');
                if (!targetSection || targetSection.parentElement !== container || targetSection === draggingSection) {
                    return;
                }

                const rect = targetSection.getBoundingClientRect();
                const shouldInsertBefore = event.clientY < rect.top + rect.height / 2;

                if (shouldInsertBefore) {
                    container.insertBefore(draggingSection, targetSection);
                } else {
                    container.insertBefore(draggingSection, targetSection.nextSibling);
                }
            });

            container.addEventListener('drop', function (event) {
                if (!draggingSection) {
                    return;
                }

                event.preventDefault();
                finishSectionDrag(container, draggingSection);
                draggingSection = null;
            });

            container.addEventListener('dragend', function () {
                if (!draggingSection) {
                    return;
                }

                draggingSection.dataset.dragAllowed = '0';
                finishSectionDrag(container, draggingSection);
                draggingSection = null;
            });
        }

        function finishSectionDrag(container, section) {
            section.classList.remove('is-dragging');
            section.dataset.dragAllowed = '0';
            renumberAuditTypeSections(container);
            refreshTokenHelpersInScope(getTokenScopeRoot(container));
        }

        function getFormulaAfterDragPosition(container, mouseY) {
            const items = Array.from(container.querySelectorAll(':scope > [data-formula-item]:not(.is-dragging)'));
            let closest = { offset: Number.NEGATIVE_INFINITY, element: null };

            items.forEach((item) => {
                const box = item.getBoundingClientRect();
                const offset = mouseY - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    closest = { offset, element: item };
                }
            });

            return closest.element;
        }

        function initializeFormulaDragAndDrop(container) {
            if (!container || container.dataset.formulaDndReady === '1') {
                return;
            }

            container.dataset.formulaDndReady = '1';
            let draggingFormula = null;
            let sourceContainer = null;

            container.addEventListener('dragstart', function (event) {
                const formula = event.target.closest('[data-formula-item]');
                if (!formula || formula.parentElement !== container) {
                    event.preventDefault();
                    return;
                }

                if (!event.target.closest('.formula-drag-handle')) {
                    event.preventDefault();
                    return;
                }

                draggingFormula = formula;
                sourceContainer = container;
                formula.classList.add('is-dragging');
                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', 'formula');
                }
            });

            container.addEventListener('dragover', function (event) {
                if (!draggingFormula || sourceContainer !== container) {
                    return;
                }

                event.preventDefault();
                const afterElement = getFormulaAfterDragPosition(container, event.clientY);
                if (!afterElement) {
                    container.appendChild(draggingFormula);
                } else if (afterElement !== draggingFormula) {
                    container.insertBefore(draggingFormula, afterElement);
                }
            });

            container.addEventListener('drop', function (event) {
                if (!draggingFormula || sourceContainer !== container) {
                    return;
                }

                event.preventDefault();
                finishFormulaDrag(container, draggingFormula);
                draggingFormula = null;
                sourceContainer = null;
            });

            container.addEventListener('dragend', function () {
                if (!draggingFormula) {
                    return;
                }

                finishFormulaDrag(container, draggingFormula);
                draggingFormula = null;
                sourceContainer = null;
            });
        }

        function finishFormulaDrag(container, formulaRow) {
            formulaRow.classList.remove('is-dragging');
            renumberFormulaRows(container);
        }

        function renumberFormulaRows(formulaContainer) {
            if (!formulaContainer) {
                return;
            }

            Array.from(formulaContainer.querySelectorAll(':scope > [data-formula-item]')).forEach((row, formulaIndex) => {
                syncFormulaHeaderTitle(row);

                row.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
                    const currentName = field.getAttribute('name') || '';
                    if (!currentName.includes('[formulas][')) {
                        return;
                    }

                    const nextName = currentName.replace(/(\[formulas\])\[\d+\]/, `$1[${formulaIndex}]`);
                    field.setAttribute('name', nextName);
                });
            });
        }

        function renumberAuditTypeSections(container) {
            if (!container) {
                return;
            }

            Array.from(container.querySelectorAll(':scope > [data-section-item]')).forEach((section, sectionIndex) => {
                syncSectionHeaderTitle(section);

                section.draggable = section.classList.contains('section-collapsed');

                section.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
                    const currentName = field.getAttribute('name') || '';
                    if (!currentName.includes('sections[')) {
                        return;
                    }

                    const nextName = currentName.replace(/sections\[\d+\]/, `sections[${sectionIndex}]`);
                    field.setAttribute('name', nextName);
                });

                const tableElement = section.querySelector('table.data-table[id]');
                const formulaContainer = section.querySelector('div[id*="section-formulas-"]');
                const addRowButton = section.querySelector('button[onclick*="addSectionDataRow"]');
                const addFormulaButton = section.querySelector('button[onclick*="addSectionFormulaRow"]');

                if (tableElement) {
                    tableElement.id = tableElement.id.replace(/-\d+$/, `-${sectionIndex}`);
                    initializeRowDragAndDrop(tableElement);
                    renumberSectionRows(tableElement);
                }

                if (formulaContainer) {
                    formulaContainer.id = formulaContainer.id.replace(/-\d+$/, `-${sectionIndex}`);
                    initializeFormulaDragAndDrop(formulaContainer);
                    renumberFormulaRows(formulaContainer);
                }

                if (addRowButton && tableElement) {
                    addRowButton.setAttribute('onclick', `addSectionDataRow('${tableElement.id}', ${sectionIndex})`);
                }

                if (addFormulaButton && formulaContainer && tableElement) {
                    addFormulaButton.setAttribute('onclick', `addSectionFormulaRow('${formulaContainer.id}', ${sectionIndex}, '${tableElement.id}')`);
                }

                if (tableElement) {
                    section.querySelectorAll('button[onclick*="validateSectionFormula"]').forEach((button) => {
                        button.setAttribute('onclick', `validateSectionFormula(this, '${tableElement.id}')`);
                    });
                }
            });
        }

        function renumberSectionRows(tableElement) {
            const tbody = tableElement?.querySelector('tbody');
            if (!tbody) {
                return;
            }

            Array.from(tbody.querySelectorAll('tr[data-draggable-row]')).forEach((row, rowIndex) => {
                row.querySelectorAll('input[name], select[name], textarea[name]').forEach((field) => {
                    const currentName = field.getAttribute('name') || '';
                    if (!currentName.includes('[rows][')) {
                        return;
                    }

                    const nextName = currentName.replace(/(\[rows\])\[\d+\]/, `$1[${rowIndex}]`);
                    field.setAttribute('name', nextName);
                });
            });

            applyDependencyVisualDepthInScope(getTokenScopeRoot(tbody));
        }

        function initializeRowDragAndDrop(tableElement) {
            const tbody = tableElement?.querySelector('tbody');
            if (!tbody || tbody.dataset.dndReady === '1') {
                return;
            }

            tbody.dataset.dndReady = '1';

            tbody.addEventListener('focusin', function (event) {
                const row = event.target.closest('tr[data-draggable-row]');
                if (!row || row.parentElement !== tbody) {
                    return;
                }

                setInsertAnchorForRow(row, tbody);
            });

            tbody.addEventListener('change', function (event) {
                const checkbox = event.target.closest('.row-insert-anchor');
                if (!checkbox) {
                    return;
                }

                const row = checkbox.closest('tr[data-draggable-row]');
                if (!row || row.parentElement !== tbody) {
                    return;
                }

                if (checkbox.checked) {
                    setInsertAnchorForRow(row, tbody);
                }
            });

            let draggingRow = null;
            let isDragging = false;

            const handleMouseMove = function (event) {
                if (!isDragging || !draggingRow) {
                    return;
                }

                event.preventDefault();
                const afterElement = getRowAfterDragPosition(tbody, event.clientY);

                if (!afterElement) {
                    tbody.appendChild(draggingRow);
                } else if (afterElement !== draggingRow) {
                    tbody.insertBefore(draggingRow, afterElement);
                }
            };

            const stopDragging = function () {
                if (!draggingRow) {
                    return;
                }

                finishRowDrag(tableElement, draggingRow);
                draggingRow = null;
                isDragging = false;
                document.body.style.removeProperty('user-select');
                document.removeEventListener('mousemove', handleMouseMove);
                document.removeEventListener('mouseup', stopDragging);
            };

            tbody.addEventListener('mousedown', function (event) {
                const handle = event.target.closest('.row-drag-handle');
                if (!handle) {
                    return;
                }

                const row = handle.closest('tr[data-draggable-row]');
                if (!row || row.parentElement !== tbody) {
                    return;
                }

                event.preventDefault();
                draggingRow = row;
                isDragging = true;
                row.classList.add('is-dragging');
                document.body.style.userSelect = 'none';
                document.addEventListener('mousemove', handleMouseMove);
                document.addEventListener('mouseup', stopDragging);
            });
        }

        function finishRowDrag(tableElement, row) {
            row.classList.remove('is-dragging');
            renumberSectionRows(tableElement);
            refreshTokenHelpersInScope(getTokenScopeRoot(row));
        }

        function getRowAfterDragPosition(tbody, mouseY) {
            const rows = Array.from(tbody.querySelectorAll('tr[data-draggable-row]:not(.is-dragging)'));
            let closest = { offset: Number.NEGATIVE_INFINITY, element: null };

            rows.forEach((row) => {
                const box = row.getBoundingClientRect();
                const offset = mouseY - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    closest = { offset, element: row };
                }
            });

            return closest.element;
        }

        function generateTokenFromName(rawName) {
            const polishMap = {
                'ą': 'a',
                'ć': 'c',
                'ę': 'e',
                'ł': 'l',
                'ń': 'n',
                'ó': 'o',
                'ś': 's',
                'ź': 'z',
                'ż': 'z',
            };

            const normalized = String(rawName || '')
                .toLowerCase()
                .replace(/[ąćęłńóśźż]/g, (char) => polishMap[char] || char)
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, ' ')
                .trim();

            const words = normalized
                .split(/\s+/)
                .filter((word) => word !== '');

            if (words.length === 0) {
                return 'pol';
            }

            return words.map((word) => word.slice(0, 3)).join('_');
        }

        function normalizeToken(rawToken) {
            const plain = String(rawToken ?? '').trim();
            if (plain === '') {
                return '';
            }

            return generateTokenFromName(plain);
        }

        function computeDependencyDepth(token, parentByToken, cache = {}, trail = []) {
            if (!token) {
                return 0;
            }

            if (typeof cache[token] === 'number') {
                return cache[token];
            }

            if (trail.includes(token)) {
                return 1;
            }

            const parent = parentByToken[token] || '';
            if (parent === '') {
                cache[token] = 0;
                return 0;
            }

            const depth = 1 + computeDependencyDepth(parent, parentByToken, cache, trail.concat(token));
            cache[token] = depth;

            return depth;
        }

        function ensureDependencyOverlayHost(table) {
            if (!table) {
                return null;
            }

            const existing = table.closest('.data-table-wrap');
            if (existing) {
                return existing;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'data-table-wrap';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);

            return wrapper;
        }

        function ensureDependencyOverlaySvg(table) {
            const host = ensureDependencyOverlayHost(table);
            if (!host) {
                return null;
            }

            let svg = host.querySelector(':scope > .dependency-tree-overlay');
            if (!svg) {
                svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.classList.add('dependency-tree-overlay');
                host.appendChild(svg);
            }

            return svg;
        }

        function isVisibleRow(row) {
            return !!row && row.style.display !== 'none';
        }

        function collectDependencyGraphForTable(table) {
            const tokenToRow = {};
            const parentByToken = {};
            const childrenByToken = {};

            table.querySelectorAll('tr[data-draggable-row]').forEach((row) => {
                const token = normalizeToken(row.querySelector('.row-token-preview')?.value || '');
                const parentToken = normalizeToken(row.querySelector('.row-parent-token')?.value || '');

                if (token === '') {
                    return;
                }

                tokenToRow[token] = row;
                parentByToken[token] = parentToken;

                if (!childrenByToken[token]) {
                    childrenByToken[token] = [];
                }
            });

            Object.entries(parentByToken).forEach(([token, parentToken]) => {
                if (parentToken !== '' && tokenToRow[parentToken]) {
                    if (!childrenByToken[parentToken]) {
                        childrenByToken[parentToken] = [];
                    }

                    childrenByToken[parentToken].push(token);
                }
            });

            return { tokenToRow, parentByToken, childrenByToken };
        }

        function clearDependencyBranchHighlight(table) {
            if (!table) {
                return;
            }

            table.querySelectorAll('tr[data-draggable-row].dependency-branch-active').forEach((row) => {
                row.classList.remove('dependency-branch-active');
            });

            const svg = table.closest('.data-table-wrap')?.querySelector(':scope > .dependency-tree-overlay');
            if (!svg) {
                return;
            }

            svg.querySelectorAll('.dependency-path.dependency-path-active').forEach((path) => {
                path.classList.remove('dependency-path-active');
            });
        }

        function highlightDependencyBranchForTable(table, row) {
            if (!table || !row) {
                return;
            }

            const rowToken = normalizeToken(row.querySelector('.row-token-preview')?.value || '');
            if (rowToken === '') {
                clearDependencyBranchHighlight(table);
                return;
            }

            const { tokenToRow, parentByToken, childrenByToken } = collectDependencyGraphForTable(table);
            const branchTokens = new Set([rowToken]);

            let cursor = rowToken;
            const seenParents = new Set();
            while (parentByToken[cursor] && !seenParents.has(cursor)) {
                seenParents.add(cursor);
                cursor = parentByToken[cursor];
                branchTokens.add(cursor);
            }

            const queue = [rowToken];
            const seenChildren = new Set(queue);
            while (queue.length > 0) {
                const current = queue.shift();
                (childrenByToken[current] || []).forEach((child) => {
                    if (!seenChildren.has(child)) {
                        seenChildren.add(child);
                        branchTokens.add(child);
                        queue.push(child);
                    }
                });
            }

            table.querySelectorAll('tr[data-draggable-row]').forEach((itemRow) => {
                const token = normalizeToken(itemRow.querySelector('.row-token-preview')?.value || '');
                itemRow.classList.toggle('dependency-branch-active', token !== '' && branchTokens.has(token));
            });

            const svg = table.closest('.data-table-wrap')?.querySelector(':scope > .dependency-tree-overlay');
            if (!svg) {
                return;
            }

            svg.querySelectorAll('.dependency-path').forEach((path) => {
                const from = normalizeToken(path.getAttribute('data-from') || '');
                const to = normalizeToken(path.getAttribute('data-to') || '');
                const isActive = from !== '' && to !== '' && branchTokens.has(from) && branchTokens.has(to);
                path.classList.toggle('dependency-path-active', isActive);
            });
        }

        function initializeDependencyTreeInteractions(table) {
            if (!table || table.dataset.dependencyTreeReady === '1') {
                return;
            }

            table.dataset.dependencyTreeReady = '1';
            const tbody = table.querySelector('tbody');
            if (!tbody) {
                return;
            }

            tbody.addEventListener('mouseover', function (event) {
                const row = event.target.closest('tr[data-draggable-row]');
                if (!row || row.parentElement !== tbody) {
                    return;
                }

                highlightDependencyBranchForTable(table, row);
            });

            tbody.addEventListener('mouseleave', function () {
                clearDependencyBranchHighlight(table);
            });
        }

        function renderDependencyTreeForTable(table) {
            if (!table) {
                return;
            }

            initializeDependencyTreeInteractions(table);
            const svg = ensureDependencyOverlaySvg(table);
            if (!svg) {
                return;
            }

            if (table.offsetParent === null) {
                svg.innerHTML = '';
                return;
            }

            const host = table.closest('.data-table-wrap');
            if (!host) {
                return;
            }

            const hostRect = host.getBoundingClientRect();
            svg.setAttribute('viewBox', `0 0 ${Math.max(1, hostRect.width)} ${Math.max(1, hostRect.height)}`);
            svg.setAttribute('preserveAspectRatio', 'none');
            svg.innerHTML = '';

            const { tokenToRow, parentByToken } = collectDependencyGraphForTable(table);
            const depthCache = {};

            const depthByToken = {};
            Object.keys(tokenToRow).forEach((token) => {
                depthByToken[token] = computeDependencyDepth(token, parentByToken, depthCache);
            });

            Object.entries(parentByToken).forEach(([token, parentToken]) => {
                if (parentToken === '' || !tokenToRow[parentToken]) {
                    return;
                }

                const row = tokenToRow[token];
                const parentRow = tokenToRow[parentToken];
                if (!isVisibleRow(row) || !isVisibleRow(parentRow)) {
                    return;
                }

                const rowCell = row.children[2];
                const parentCell = parentRow.children[2];
                if (!rowCell || !parentCell) {
                    return;
                }

                const childRect = rowCell.getBoundingClientRect();
                const parentRect = parentCell.getBoundingClientRect();
                const parentDepth = Math.max(0, (depthByToken[parentToken] || 0) - 1);
                const childDepth = Math.max(0, (depthByToken[token] || 0) - 1);

                const startX = parentRect.left - hostRect.left + 14 + parentDepth * 24;
                const endX = childRect.left - hostRect.left + 14 + childDepth * 24;
                const startY = parentRect.top - hostRect.top + parentRect.height / 2;
                const endY = childRect.top - hostRect.top + childRect.height / 2;
                const laneX = Math.min(startX, endX) - 12;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.classList.add('dependency-path');
                path.setAttribute('data-from', parentToken);
                path.setAttribute('data-to', token);
                path.setAttribute('d', `M ${startX} ${startY} H ${laneX} V ${endY} H ${endX}`);
                svg.appendChild(path);
            });
        }

        function applyDependencyVisualDepthInScope(scopeRoot) {
            if (!scopeRoot) {
                return;
            }

            const tokenToRow = {};
            const parentByToken = {};

            scopeRoot.querySelectorAll('tr[data-draggable-row]').forEach((row) => {
                const token = normalizeToken(row.querySelector('.row-token-preview')?.value || '');
                const parentToken = normalizeToken(row.querySelector('.row-parent-token')?.value || '');

                if (token !== '') {
                    tokenToRow[token] = row;
                    parentByToken[token] = parentToken;
                }
            });

            const cache = {};
            Object.keys(tokenToRow).forEach((token) => {
                const row = tokenToRow[token];
                const depth = computeDependencyDepth(token, parentByToken, cache);

                if (depth > 0) {
                    row.classList.add('is-dependent-row');
                    row.style.setProperty('--dependency-depth', String(depth));
                    row.setAttribute('data-dependency-depth', String(depth));
                    row.setAttribute('data-dependency-label', '⤷'.repeat(Math.min(depth, 4)));
                } else {
                    row.classList.remove('is-dependent-row');
                    row.style.removeProperty('--dependency-depth');
                    row.removeAttribute('data-dependency-depth');
                    row.removeAttribute('data-dependency-label');
                }
            });

            scopeRoot.querySelectorAll('table.data-table').forEach((table) => {
                if (table.querySelector('.row-token-preview')) {
                    renderDependencyTreeForTable(table);
                }
            });
        }

        function escapeRegExp(value) {
            return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function updateRowToken(nameInput) {
            if (!nameInput) {
                return;
            }

            const row = nameInput.closest('tr');
            if (!row) {
                return;
            }

            const tokenInput = row.querySelector('.row-token-preview');
            if (!tokenInput) {
                return;
            }

            const previousToken = normalizeToken(tokenInput.value);
            if (tokenInput.dataset.tokenManual === '1' && previousToken !== '') {
                return;
            }

            const desiredToken = generateTokenFromName(nameInput.value);
            const uniqueToken = ensureUniqueTokenInScope(tokenInput, desiredToken);

            tokenInput.value = uniqueToken;
            tokenInput.dataset.prevToken = uniqueToken;

            if (previousToken !== '' && previousToken !== uniqueToken) {
                replaceTokenInScopeFormulas(getTokenScopeRoot(tokenInput), previousToken, uniqueToken);
                replaceDependentTokenInScope(getTokenScopeRoot(tokenInput), previousToken, uniqueToken);
            }

            refreshTokenHelpersInScope(getTokenScopeRoot(tokenInput));
            refreshDependencySelectorsInScope(getTokenScopeRoot(tokenInput));
        }

        function handleFormulaTokenEdit(tokenInput) {
            if (!tokenInput) {
                return;
            }

            tokenInput.dataset.manualEdit = '1';
            const oldToken = normalizeToken(tokenInput.dataset.prevToken || tokenInput.value);
            const desiredToken = normalizeToken(tokenInput.value);
            const uniqueToken = ensureUniqueTokenInScope(tokenInput, desiredToken !== '' ? desiredToken : 'wzor');

            tokenInput.value = uniqueToken;
            tokenInput.dataset.prevToken = uniqueToken;

            if (oldToken !== '' && oldToken !== uniqueToken) {
                replaceTokenInScopeFormulas(getTokenScopeRoot(tokenInput), oldToken, uniqueToken);
            }

            refreshTokenHelpersInScope(getTokenScopeRoot(tokenInput));
        }

        function rememberTokenBeforeEdit(tokenInput) {
            if (!tokenInput) {
                return;
            }

            tokenInput.dataset.prevToken = normalizeToken(tokenInput.value);
        }

        function handleTokenEdit(tokenInput) {
            if (!tokenInput) {
                return;
            }

            const oldToken = normalizeToken(tokenInput.dataset.prevToken || tokenInput.value);
            const desiredToken = normalizeToken(tokenInput.value);
            const uniqueToken = ensureUniqueTokenInScope(tokenInput, desiredToken);

            tokenInput.value = uniqueToken;
            tokenInput.dataset.prevToken = uniqueToken;
            tokenInput.dataset.tokenManual = '1';

            if (oldToken !== '' && oldToken !== uniqueToken) {
                replaceTokenInScopeFormulas(getTokenScopeRoot(tokenInput), oldToken, uniqueToken);
                replaceDependentTokenInScope(getTokenScopeRoot(tokenInput), oldToken, uniqueToken);
            }

            refreshTokenHelpersInScope(getTokenScopeRoot(tokenInput));
            refreshDependencySelectorsInScope(getTokenScopeRoot(tokenInput));
        }

        function getTokenScopeRoot(element) {
            return element?.closest('form.audit-builder') || null;
        }

        function ensureUniqueTokenInScope(tokenInput, token) {
            const scopeRoot = getTokenScopeRoot(tokenInput);
            const baseToken = token !== '' ? token : 'pole';
            if (!scopeRoot) {
                return baseToken;
            }

            const used = [
                ...Array.from(scopeRoot.querySelectorAll('.row-token-preview')),
                ...Array.from(scopeRoot.querySelectorAll('.variable-token-preview')),
                ...Array.from(scopeRoot.querySelectorAll('.formula-token-preview')),
            ]
                .filter((input) => input !== tokenInput)
                .map((input) => normalizeToken(input.value))
                .filter((value) => value !== '');

            let candidate = baseToken;
            let suffix = 2;
            while (used.includes(candidate)) {
                candidate = `${baseToken}_${suffix}`;
                suffix += 1;
            }

            return candidate;
        }

        function replaceTokenInScopeFormulas(scopeRoot, oldToken, newToken) {
            if (!scopeRoot || oldToken === '' || oldToken === newToken) {
                return;
            }

            const pattern = new RegExp(`\\{${escapeRegExp(oldToken)}\\}`, 'g');
            scopeRoot.querySelectorAll('textarea[name$="[expression]"]').forEach((textarea) => {
                if (typeof textarea.value !== 'string' || textarea.value === '') {
                    return;
                }

                textarea.value = textarea.value.replace(pattern, `{${newToken}}`);
            });
        }

        function replaceDependentTokenInScope(scopeRoot, oldToken, newToken) {
            if (!scopeRoot || oldToken === '' || oldToken === newToken) {
                return;
            }

            scopeRoot.querySelectorAll('.row-parent-token').forEach((select) => {
                const current = normalizeToken(select.value);
                if (current === oldToken) {
                    select.value = newToken;
                }
            });
        }

        function setActiveFormulaTextarea(textarea) {
            if (!textarea) {
                return;
            }

            window.__activeFormulaTextarea = textarea;
        }

        function insertTextToActiveFormula(section, text) {
            const active = window.__activeFormulaTextarea;
            const target = (active && section && section.contains(active))
                ? active
                : (section ? section.querySelector('textarea[name$="[expression]"]') : null);
            if (!target) {
                return;
            }
            const start = target.selectionStart ?? target.value.length;
            const end = target.selectionEnd ?? target.value.length;
            target.value = target.value.slice(0, start) + text + target.value.slice(end);
            target.focus();
            const cursor = start + text.length;
            if (typeof target.setSelectionRange === 'function') {
                target.setSelectionRange(cursor - (text.endsWith(')') ? 1 : 0), cursor - (text.endsWith(')') ? 1 : 0));
            }
        }

        function insertTokenToFormula(scopeRoot, section, token) {
            if (!section || !token) {
                return;
            }

            const active = window.__activeFormulaTextarea;
            const inThisScope = active && scopeRoot && scopeRoot.contains(active);
            const target = inThisScope
                ? active
                : section.querySelector('textarea[name$="[expression]"]');

            if (!target) {
                return;
            }

            const insertion = `{${token}}`;
            const start = target.selectionStart ?? target.value.length;
            const end = target.selectionEnd ?? target.value.length;
            const before = target.value.slice(0, start);
            const after = target.value.slice(end);

            target.value = `${before}${insertion}${after}`;
            target.focus();

            const cursor = start + insertion.length;
            if (typeof target.setSelectionRange === 'function') {
                target.setSelectionRange(cursor, cursor);
            }
        }

        function collectTokensInScope(scopeRoot) {
            if (!scopeRoot) {
                return [];
            }

            const allTokenInputs = [
                ...Array.from(scopeRoot.querySelectorAll('.row-token-preview')),
                ...Array.from(scopeRoot.querySelectorAll('.variable-token-preview')),
                ...Array.from(scopeRoot.querySelectorAll('.formula-token-preview')),
            ];

            return allTokenInputs
                .map((input) => normalizeToken(input.value))
                .filter((token, index, array) => token !== '' && array.indexOf(token) === index);
        }

        function syncDependencyRowVisual(select) {
            const row = select?.closest('tr[data-draggable-row]');
            if (!row) {
                return;
            }

            const hasParent = String(select.value || '').trim() !== '';
            row.classList.toggle('is-dependent-row', hasParent);
            applyDependencyVisualDepthInScope(getTokenScopeRoot(row));
        }

        function handleDependencyParentChange(select) {
            if (!select) {
                return;
            }

            const row = select.closest('tr');
            const showWhenInput = row?.querySelector('.row-show-when');
            if (!showWhenInput) {
                return;
            }

            const hasParent = String(select.value || '').trim() !== '';
            showWhenInput.disabled = !hasParent;
            syncDependencyRowVisual(select);
            if (!hasParent) {
                showWhenInput.value = '';
                showWhenInput.placeholder = 'najpierw wybierz zależność';
            } else {
                showWhenInput.placeholder = 'np. tak / nie / opcja';
            }
        }

        function refreshDependencySelectorsInScope(scopeRoot) {
            if (!scopeRoot) {
                return;
            }

            const tokens = collectTokensInScope(scopeRoot);
            scopeRoot.querySelectorAll('.row-parent-token').forEach((select) => {
                const row = select.closest('tr');
                const ownToken = normalizeToken(row?.querySelector('.row-token-preview')?.value || '');
                const currentValue = normalizeToken(select.value || '');

                const allowedTokens = tokens.filter((token) => token !== ownToken);
                const options = ['<option value="">—</option>']
                    .concat(allowedTokens.map((token) => `<option value="${token}">${token}</option>`))
                    .join('');

                select.innerHTML = options;
                select.value = allowedTokens.includes(currentValue) ? currentValue : '';
                handleDependencyParentChange(select);
            });

            applyDependencyVisualDepthInScope(scopeRoot);
        }

        function refreshTokenHelpersInScope(scopeRoot) {
            if (!scopeRoot) {
                return;
            }

            const tokenLists = scopeRoot.querySelectorAll('.section-token-list');
            if (tokenLists.length === 0) {
                return;
            }

            const dataTokens = [
                ...Array.from(scopeRoot.querySelectorAll('.row-token-preview')),
                ...Array.from(scopeRoot.querySelectorAll('.variable-token-preview')),
            ]
                .map((input) => normalizeToken(input.value))
                .filter((token, index, array) => token !== '' && array.indexOf(token) === index);

            const formulaTokens = Array.from(scopeRoot.querySelectorAll('.formula-token-preview'))
                .map((input) => normalizeToken(input.value))
                .filter((token, index, array) => token !== '' && array.indexOf(token) === index);

            tokenLists.forEach((list) => {
                list.innerHTML = '';

                if (dataTokens.length === 0 && formulaTokens.length === 0) {
                    const empty = document.createElement('span');
                    empty.style.fontSize = '12px';
                    empty.style.color = '#6b8294';
                    empty.textContent = 'Brak tokenów. Dodaj wiersz w tabeli danych.';
                    list.appendChild(empty);
                    return;
                }

                const operators = [
                    { label: '( )', insert: '()', title: 'Nawiasy' },
                    { label: 'xⁿ', insert: '^', title: 'Potęgowanie — np. {token}^2' },
                ];
                operators.forEach(({ label, insert, title }) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'token-chip';
                    btn.title = title;
                    btn.textContent = label;
                    btn.style.fontWeight = '700';
                    btn.addEventListener('click', function () {
                        insertTextToActiveFormula(list.closest('.audit-type-section'), insert);
                    });
                    list.appendChild(btn);
                });

                if (dataTokens.length > 0 || formulaTokens.length > 0) {
                    const sepOp = document.createElement('span');
                    sepOp.className = 'token-chip-separator';
                    sepOp.textContent = '|';
                    list.appendChild(sepOp);
                }

                dataTokens.forEach((token) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'token-chip';
                    button.title = 'Pole danych';
                    button.textContent = `{${token}}`;
                    button.addEventListener('click', function () {
                        insertTokenToFormula(scopeRoot, list.closest('.audit-type-section'), token);
                    });
                    list.appendChild(button);
                });

                if (formulaTokens.length > 0) {
                    if (dataTokens.length > 0) {
                        const sep = document.createElement('span');
                        sep.className = 'token-chip-separator';
                        sep.textContent = '|';
                        list.appendChild(sep);
                    }

                    formulaTokens.forEach((token) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'token-chip token-chip-formula';
                        button.title = 'Wynik wzoru';
                        button.textContent = `{${token}}`;
                        button.addEventListener('click', function () {
                            insertTokenToFormula(scopeRoot, list.closest('.audit-type-section'), token);
                        });
                        list.appendChild(button);
                    });
                }
            });
        }

        function removeDataRow(button) {
            const row = button?.closest('tr');
            const scopeRoot = getTokenScopeRoot(row);
            if (!row) {
                return;
            }

            const tableElement = row.closest('table');
            row.remove();
            if (tableElement) {
                renumberSectionRows(tableElement);
            }
            refreshTokenHelpersInScope(scopeRoot);
            refreshDependencySelectorsInScope(scopeRoot);
        }

        function addSectionFormulaRow(containerId, sectionIndex, tableId, defaults = {}) {
            const container = document.getElementById(containerId);
            if (!container) {
                return;
            }

            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'audit-type-section section-formula-row';
            row.setAttribute('data-formula-item', '1');
            row.setAttribute('draggable', 'true');
            row.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button type="button" class="section-collapse-arrow" onclick="toggleFormulaCollapse(this)" title="Zwiń wzór">▾</button>
                        <strong class="formula-order-label" style="font-size:12px; color:#1d4f73;"><span class="formula-drag-handle" title="Przeciągnij wzór">•••</span><span class="formula-order-title">${defaults.label || 'Nowy wzór'}</span></strong>
                    </div>
                    <button type="button" class="btn-secondary" onclick="removeFormulaRow(this)">Usuń</button>
                </div>
                <div class="formula-collapsible-content" style="display:grid; grid-template-columns:1fr 160px 160px; gap:8px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Co obliczyć</label>
                        <input type="text" class="formula-name-input" name="sections[${sectionIndex}][formulas][${index}][label]" value="${defaults.label || ''}" placeholder="Np. Zużycie roczne" oninput="updateFormulaHeaderTitle(this)">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Token wyniku</label>
                        <input type="text" class="formula-token-preview" name="sections[${sectionIndex}][formulas][${index}][key]" value="${defaults.key || ''}" placeholder="auto" onfocus="rememberTokenBeforeEdit(this)" oninput="handleFormulaTokenEdit(this)" onblur="handleFormulaTokenEdit(this)">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Jednostka wyniku</label>
                        <input type="text" name="sections[${sectionIndex}][formulas][${index}][unit]" value="${defaults.unit || ''}" placeholder="Np. kW">
                    </div>
                    <div style="grid-column:1 / -1;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Wzór</label>
                        <textarea name="sections[${sectionIndex}][formulas][${index}][expression]" rows="2" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;" placeholder="Np. ({moc_nominalna} * {czas_pracy_h}) / 1000" onfocus="setActiveFormulaTextarea(this)">${defaults.expression || ''}</textarea>
                    </div>
                </div>
                <div class="formula-collapsible-content" style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                    <button type="button" class="btn-secondary" onclick="validateSectionFormula(this, '${tableId}')">Sprawdź wzór</button>
                    <span class="formula-validation-message" style="font-size:12px; color:#4c6373;"></span>
                </div>
            `;

            container.appendChild(row);
            initializeFormulaDragAndDrop(container);
            renumberFormulaRows(container);
            refreshTokenHelpersInScope(getTokenScopeRoot(container));
            refreshDependencySelectorsInScope(getTokenScopeRoot(container));
        }

        function evaluateFormulaExpression(expression, values) {
            const usedTokens = Array.from(new Set(
                Array.from(expression.matchAll(/\{([a-zA-Z0-9_]+)\}/g)).map((m) => m[1])
            ));
            if (usedTokens.some((t) => !(t in values))) { return undefined; }
            if (usedTokens.some((t) => values[t] === null)) { return null; }

            let expr = expression.replace(/\{([a-zA-Z0-9_]+)\}/g, (_, t) => String(values[t] ?? 0));
            expr = expr
                .replace(/JEŻELI\s*\(/gi, 'IF(')
                .replace(/JEZELI\s*\(/gi, 'IF(')
                .replace(/JEŚLI\s*\(/gi, 'IF(')
                .replace(/;/g, ',')
                .replace(/(\d),(\d)/g, '$1.$2')
                .replace(/<>/g, '!=')
                .replace(/([^!<>=])=(?!=)/g, '$1==')
                .replace(/\^/g, '**');

            if (/[`"'\\]/.test(expr)) { return null; }

            const IF    = (c, t, f) => (c ? t : f);
            const AND   = (...a) => (a.every(Boolean) ? 1 : 0);
            const OR    = (...a) => (a.some(Boolean) ? 1 : 0);
            const NOT   = (x) => (x ? 0 : 1);
            const MAX   = Math.max;
            const MIN   = Math.min;
            const ABS   = Math.abs;
            const SQRT  = Math.sqrt;
            const ROUND = (n, dp = 0) => parseFloat(n.toFixed(Math.max(0, dp | 0)));

            try {
                // eslint-disable-next-line no-new-func
                const result = Function('IF','AND','OR','NOT','MAX','MIN','ABS','SQRT','ROUND',
                    '"use strict"; return (' + expr + ')'
                )(IF, AND, OR, NOT, MAX, MIN, ABS, SQRT, ROUND);
                if (typeof result === 'boolean') { return result ? 1 : 0; }
                return Number.isFinite(result) ? result : null;
            } catch (e) {
                return null;
            }
        }

        function validateSectionFormula(button, tableId) {
            const formulaRow = button?.closest('.section-formula-row');
            if (!formulaRow) {
                return;
            }

            const message = formulaRow.querySelector('.formula-validation-message');
            const expressionInput = formulaRow.querySelector('[name$="[expression]"]');
            const expression = String(expressionInput?.value || '').trim();

            if (!message) {
                return;
            }

            if (expression === '') {
                message.style.color = '#b42318';
                message.textContent = 'Podaj wzór do sprawdzenia.';
                return;
            }

            const scopeRoot = getTokenScopeRoot(formulaRow);
            const availableTokens = collectTokensInScope(scopeRoot);

            const tokensInExpression = Array.from(new Set(Array.from(expression.matchAll(/\{([a-zA-Z0-9_]+)\}/g)).map((match) => match[1])));
            const missingTokens = tokensInExpression.filter((token) => !availableTokens.includes(token));
            if (missingTokens.length > 0) {
                message.style.color = '#b42318';
                message.textContent = `Błąd: brak tokenów w rodzaju audytu: ${missingTokens.join(', ')}`;
                return;
            }

            const dummyValues = {};
            availableTokens.forEach((t) => { dummyValues[t] = 1; });

            const evalResult = evaluateFormulaExpression(expression, dummyValues);
            if (evalResult === null) {
                message.style.color = '#b42318';
                message.textContent = 'Błąd: sprawdź składnię wzoru — błędna operacja lub nawiasy.';
                return;
            }

            message.style.color = '#067647';
            message.textContent = 'OK: wzór jest poprawny dla tej sekcji.';
        }

        function handleRowUnitKind(select) {
            if (!select) {
                return;
            }

            const unitName = select.value;
            const kind = auditUnitKinds[unitName] || 'number';
            const row = select.closest('tr');
            if (!row) {
                return;
            }

            const optionsInput = row.querySelector('.row-options-input');
            if (optionsInput) {
                optionsInput.style.display = kind === 'select' ? 'block' : 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('audit-type-sections') && document.getElementById('audit-type-sections').children.length === 0) {
                addAuditTypeSection();
            }

            document.querySelectorAll('select[name$="[unit]"]').forEach((select) => {
                handleRowUnitKind(select);
            });

            document.querySelectorAll('input[name$="[name]"]').forEach((input) => {
                if (input.closest('.data-table')) {
                    updateRowToken(input);
                }
            });

            document.querySelectorAll('.row-token-preview').forEach((input) => {
                input.dataset.prevToken = normalizeToken(input.value);
            });

            document.querySelectorAll('.formula-token-preview').forEach((input) => {
                input.dataset.prevToken = normalizeToken(input.value);
                input.dataset.manualEdit = input.value !== '' ? '1' : '0';
            });

            document.querySelectorAll('textarea[name$="[expression]"]').forEach((textarea) => {
                textarea.addEventListener('focus', function () {
                    setActiveFormulaTextarea(textarea);
                });
            });

            document.querySelectorAll('form.audit-builder').forEach((form) => {
                refreshTokenHelpersInScope(form);

                form.querySelectorAll('[id="audit-type-sections"], [id^="edit-audit-type-sections-"]').forEach((sectionContainer) => {
                    initializeSectionDragAndDrop(sectionContainer);
                    renumberAuditTypeSections(sectionContainer);
                    sectionContainer.querySelectorAll('[data-section-item]').forEach((section) => {
                        section.draggable = section.classList.contains('section-collapsed');
                        syncSectionHeaderTitle(section);
                    });
                });

                form.querySelectorAll('table').forEach((table) => {
                    if (table.querySelector('.row-token-preview')) {
                        initializeRowDragAndDrop(table);
                        renumberSectionRows(table);
                    }
                });

                form.querySelectorAll('div[id*="section-formulas-"]').forEach((formulaContainer) => {
                    if (formulaContainer.querySelector('[data-formula-item]')) {
                        initializeFormulaDragAndDrop(formulaContainer);
                        renumberFormulaRows(formulaContainer);
                    }
                });

                refreshDependencySelectorsInScope(form);
                applyDependencyVisualDepthInScope(form);

                form.querySelectorAll('[data-formula-item]').forEach((formulaRow) => {
                    syncFormulaHeaderTitle(formulaRow);
                });

                form.addEventListener('submit', function () {
                    form.querySelectorAll('[id="audit-type-sections"], [id^="edit-audit-type-sections-"]').forEach((sectionContainer) => {
                        renumberAuditTypeSections(sectionContainer);
                    });

                    form.querySelectorAll('table').forEach((table) => {
                        if (table.querySelector('.row-token-preview')) {
                            renumberSectionRows(table);
                        }
                    });

                    refreshDependencySelectorsInScope(form);

                    form.querySelectorAll('div[id*="section-formulas-"]').forEach((formulaContainer) => {
                        if (formulaContainer.querySelector('[data-formula-item]')) {
                            renumberFormulaRows(formulaContainer);
                        }
                    });
                });
            });

            let dependencyResizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(dependencyResizeTimer);
                dependencyResizeTimer = setTimeout(function () {
                    document.querySelectorAll('form.audit-builder').forEach((form) => {
                        applyDependencyVisualDepthInScope(form);
                    });
                }, 100);
            });
        });
    </script>
</x-layouts.app>
