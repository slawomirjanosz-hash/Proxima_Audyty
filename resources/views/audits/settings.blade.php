<x-layouts.app>
    <section class="panel">
        <style>
            .settings-section { border: 1px solid #d2e3f1; border-radius: 14px; background: #fff; margin-top: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(18, 72, 110, 0.06); }
            .settings-toggle { width: 100%; border: none; background: #fafdff; display:flex; justify-content:space-between; align-items:center; gap:10px; padding: 14px 14px; cursor:pointer; }
            .settings-toggle:hover { background:#f2f8fe; }
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
            .btn-secondary { background: #dbe9f5; color: #1d4f73; }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <h1 style="margin:0;">Ustawienia audytów</h1>
                <p class="muted" style="margin:4px 0 0;">Rodzaje audytów i jednostki — sekcje rozwijane.</p>
            </div>
            <a href="{{ route('audits.index') }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">← Wróć do audytów</a>
        </div>

        <div class="settings-section open" id="settings-audit-types">
            <button type="button" class="settings-toggle" onclick="toggleSettingsSection('settings-audit-types')">
                <div>
                    <h2 style="margin:0;">Rodzaje audytów</h2>
                    <p class="muted" style="margin:4px 0 0;">Tworzenie rodzaju audytu jako osobna sekcja.</p>
                </div>
                <span class="settings-chevron">&#9660;</span>
            </button>

            <div class="settings-body">
                <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                    <button type="button" class="edit-user-btn" onclick="toggleAuditTypeForm()">Dodaj rodzaj audytu</button>
                </div>

                <form id="add-audit-type-form" method="POST" action="{{ route('audits.settings.audit-type-store') }}" class="audit-builder">
                @csrf
                <div style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa rodzaju audytu</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
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
                                <form method="POST" action="{{ route('audits.settings.audit-type-destroy', $type) }}" onsubmit="return confirm('Usunąć rodzaj audytu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary">Usuń</button>
                                </form>
                            </div>
                        </div>

                        <div class="audit-type-details">

                        <form id="edit-audit-type-form-{{ $type->id }}" method="POST" action="{{ route('audits.settings.audit-type-update', $type) }}" class="audit-builder" style="margin-top:10px;">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa rodzaju audytu</label>
                                <input type="text" name="name" value="{{ $type->name }}" required>
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
                                                'default_value' => '',
                                                'notes' => '',
                                                'options_text' => '',
                                            ];
                                        })->filter(fn ($row) => trim($row['name']) !== '')->values();
                                    @endphp
                                    <div class="audit-type-section">
                                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                                            <strong style="font-size:14px; color:#10344c;">Sekcja {{ $sectionIndex + 1 }}</strong>
                                            <button type="button" class="btn-secondary" onclick="this.closest('.audit-type-section').remove()">Usuń</button>
                                        </div>

                                        <div style="display:grid; gap:8px;">
                                            <div>
                                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa sekcji</label>
                                                <input type="text" name="sections[{{ $sectionIndex }}][name]" value="{{ $section->name }}" required>
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
                                                <table class="data-table" id="edit-section-data-table-{{ $type->id }}-{{ $sectionIndex }}">
                                                    <thead>
                                                        <tr>
                                                            <th>Dana</th>
                                                            <th>Token</th>
                                                            <th>Jednostka</th>
                                                            <th>Wartość</th>
                                                            <th>Uwagi</th>
                                                            <th>Opcje listy rozwijanej</th>
                                                            <th>Akcja</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($sectionRows as $rowIndex => $row)
                                                            <tr>
                                                                <td><input type="text" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][name]" value="{{ $row['name'] }}" oninput="updateRowToken(this)"></td>
                                                                <td>
                                                                    <input type="text" class="row-token-preview" name="sections[{{ $sectionIndex }}][rows][{{ $rowIndex }}][key]" value="{{ $row['key'] !== '' ? $row['key'] : \Illuminate\Support\Str::slug((string) $row['name'], '_') }}" readonly style="background:#f2f6fa; color:#34556f;">
                                                                </td>
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
                                                                <td><button type="button" class="btn-secondary" onclick="this.closest('tr').remove()">Usuń</button></td>
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
                                                <div style="font-size:12px; color:#4c6373; margin:0 0 6px;">Tokeny pól: <strong>{token}</strong>, np. <strong>({moc_nominalna} * 1.2) / 1000</strong>. We wzorze wpisuj tylko liczby, operatory i tokeny (bez jednostek typu <strong>kW</strong>).</div>
                                                <div id="edit-section-formulas-{{ $type->id }}-{{ $sectionIndex }}" style="display:grid; gap:8px;">
                                                    @foreach(($section->formulas ?? []) as $formulaIndex => $formula)
                                                        <div class="audit-type-section section-formula-row" style="padding:8px;">
                                                            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                                                                <strong style="font-size:12px; color:#1d4f73;">Wzór {{ $formulaIndex + 1 }}</strong>
                                                                <button type="button" class="btn-secondary" onclick="this.closest('.audit-type-section').remove()">Usuń</button>
                                                            </div>
                                                            <div style="display:grid; grid-template-columns:1fr 180px; gap:8px;">
                                                                <div>
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Co obliczyć</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][label]" value="{{ (string) ($formula['label'] ?? '') }}" placeholder="Np. Zużycie roczne">
                                                                </div>
                                                                <div>
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Jednostka wyniku</label>
                                                                    <input type="text" name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][unit]" value="{{ (string) ($formula['unit'] ?? '') }}" placeholder="Np. kW">
                                                                </div>
                                                                <div style="grid-column:1 / -1;">
                                                                    <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Wzór</label>
                                                                    <textarea name="sections[{{ $sectionIndex }}][formulas][{{ $formulaIndex }}][expression]" rows="2" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;" placeholder="Np. ({moc_nominalna} * {czas_pracy_h}) / 1000 (bez kW w polu wzoru)">{{ (string) ($formula['expression'] ?? '') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div style="margin-top:8px; display:flex; align-items:center; gap:8px;">
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

        <div class="settings-section" id="settings-units">
            <button type="button" class="settings-toggle" onclick="toggleSettingsSection('settings-units')">
                <div>
                    <h2 style="margin:0;">Jednostki</h2>
                    <p class="muted" style="margin:4px 0 0;">Kolejna sekcja ustawień audytów, z możliwością dodawania kolejnych jednostek.</p>
                </div>
                <span class="settings-chevron">&#9660;</span>
            </button>

            <div class="settings-body">

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
            wrapper.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                    <strong style="font-size:12px; color:#1d4f73;">Sekcja ${index + 1}</strong>
                    <button type="button" class="btn-secondary" onclick="this.closest('.audit-type-section').remove()">Usuń</button>
                </div>
                <div style="display:grid; gap:8px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Nazwa sekcji</label>
                        <input type="text" name="sections[${index}][name]" value="${defaults.name || ''}" placeholder="Np. Dane wejściowe" required>
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
                        <table class="data-table" id="${containerId}-table-${index}">
                            <thead>
                                <tr>
                                    <th>Dana</th>
                                    <th>Token</th>
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
                        <div style="font-size:12px; color:#4c6373; margin:0 0 6px;">Tokeny pól: <strong>{token}</strong>, np. <strong>({moc_nominalna} * 1.2) / 1000</strong>. We wzorze wpisuj tylko liczby, operatory i tokeny (bez jednostek typu <strong>kW</strong>).</div>
                        <div id="${containerId}-formulas-${index}" style="display:grid; gap:8px;"></div>
                    </div>
                </div>
            `;

            container.appendChild(wrapper);
            addSectionDataRow(`${containerId}-table-${index}`, index);
        }

        function addSectionDataRow(tableId, sectionIndex) {
            const table = document.querySelector(`#${tableId} tbody`);
            if (!table) {
                return;
            }

            const rowIndex = table.children.length;
            const row = document.createElement('tr');
            const unitOptions = ['<option value="">—</option>']
                .concat(auditUnits.map((unit) => `<option value="${unit}">${unit}</option>`))
                .join('');

            row.innerHTML = `
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][name]" placeholder="Np. Moc nominalna" oninput="updateRowToken(this)"></td>
                <td><input type="text" class="row-token-preview" name="sections[${sectionIndex}][rows][${rowIndex}][key]" value="pole" readonly style="background:#f2f6fa; color:#34556f;"></td>
                <td>
                    <select name="sections[${sectionIndex}][rows][${rowIndex}][unit]" onchange="handleRowUnitKind(this)">
                        ${unitOptions}
                    </select>
                </td>
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][default_value]" placeholder="Wartość domyślna (opcjonalnie)"></td>
                <td><input type="text" name="sections[${sectionIndex}][rows][${rowIndex}][notes]" placeholder="Uwagi (opcjonalnie)"></td>
                <td><textarea class="row-options-input" name="sections[${sectionIndex}][rows][${rowIndex}][options_text]" rows="2" placeholder="Jedna opcja w linii" style="display:none;"></textarea></td>
                <td><button type="button" class="btn-secondary" onclick="this.closest('tr').remove()">Usuń</button></td>
            `;

            table.appendChild(row);
            const nameInput = row.querySelector('input[name$="[name]"]');
            if (nameInput) {
                updateRowToken(nameInput);
            }

            const select = row.querySelector('select[name$="[unit]"]');
            if (select) {
                handleRowUnitKind(select);
            }
        }

        function generateTokenFromName(rawName) {
            const normalized = String(rawName || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '')
                .replace(/_+/g, '_');

            return normalized || 'pole';
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

            tokenInput.value = generateTokenFromName(nameInput.value);
        }

        function addSectionFormulaRow(containerId, sectionIndex, tableId, defaults = {}) {
            const container = document.getElementById(containerId);
            if (!container) {
                return;
            }

            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'audit-type-section section-formula-row';
            row.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                    <strong style="font-size:12px; color:#1d4f73;">Wzór ${index + 1}</strong>
                    <button type="button" class="btn-secondary" onclick="this.closest('.audit-type-section').remove()">Usuń</button>
                </div>
                <div style="display:grid; grid-template-columns:1fr 180px; gap:8px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Co obliczyć</label>
                        <input type="text" name="sections[${sectionIndex}][formulas][${index}][label]" value="${defaults.label || ''}" placeholder="Np. Zużycie roczne">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Jednostka wyniku</label>
                        <input type="text" name="sections[${sectionIndex}][formulas][${index}][unit]" value="${defaults.unit || ''}" placeholder="Np. kW">
                    </div>
                    <div style="grid-column:1 / -1;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Wzór</label>
                        <textarea name="sections[${sectionIndex}][formulas][${index}][expression]" rows="2" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;" placeholder="Np. ({moc_nominalna} * {czas_pracy_h}) / 1000">${defaults.expression || ''}</textarea>
                    </div>
                </div>
                <div style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                    <button type="button" class="btn-secondary" onclick="validateSectionFormula(this, '${tableId}')">Sprawdź wzór</button>
                    <span class="formula-validation-message" style="font-size:12px; color:#4c6373;"></span>
                </div>
            `;

            container.appendChild(row);
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

            const tableBody = document.querySelector(`#${tableId} tbody`);
            const availableTokens = [];
            if (tableBody) {
                tableBody.querySelectorAll('tr').forEach((tableRow) => {
                    const tokenPreview = tableRow.querySelector('.row-token-preview');
                    const nameInput = tableRow.querySelector('input[name$="[name]"]');
                    const token = String(tokenPreview?.value || generateTokenFromName(nameInput?.value || '')).trim();
                    if (token !== '') {
                        availableTokens.push(token);
                    }
                });
            }

            const tokensInExpression = Array.from(new Set(Array.from(expression.matchAll(/\{([a-zA-Z0-9_]+)\}/g)).map((match) => match[1])));
            const missingTokens = tokensInExpression.filter((token) => !availableTokens.includes(token));
            if (missingTokens.length > 0) {
                message.style.color = '#b42318';
                message.textContent = `Błąd: brak tokenów w sekcji: ${missingTokens.join(', ')}`;
                return;
            }

            const replaced = expression.replace(/\{([a-zA-Z0-9_]+)\}/g, '1').replace(/,/g, '.');
            if (!/^[0-9+\-*/().\s]+$/.test(replaced)) {
                message.style.color = '#b42318';
                message.textContent = 'Błąd: użyj tylko liczb, działań i tokenów {token}.';
                return;
            }

            try {
                const result = Function('return (' + replaced + ')')();
                if (!Number.isFinite(result)) {
                    message.style.color = '#b42318';
                    message.textContent = 'Błąd: wzór nie zwraca liczby.';
                    return;
                }

                message.style.color = '#067647';
                message.textContent = 'OK: wzór jest poprawny dla tej sekcji.';
            } catch (error) {
                message.style.color = '#b42318';
                message.textContent = 'Błąd: sprawdź nawiasy i kolejność działań.';
            }
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
        });
    </script>
</x-layouts.app>
