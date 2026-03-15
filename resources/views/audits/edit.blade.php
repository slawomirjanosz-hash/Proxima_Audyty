<x-layouts.app>
    <section class="panel">
        <style>
            .audit-edit-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
            .audit-edit-grid input, .audit-edit-grid select, .audit-edit-grid textarea { width:100%; }
            .audit-section { border:1px solid #d7e5f0; border-radius:12px; padding:12px; background:#f9fcff; margin-top:10px; }
            .audit-section h4 { margin:0 0 8px; }
            .audit-task-list { display:grid; gap:6px; }
            .audit-data-table { width:100%; border-collapse:collapse; margin-top:10px; }
            .audit-data-table th, .audit-data-table td { border:1px solid #e0ecf5; padding:8px; font-size:13px; text-align:left; }
            .audit-data-table th { background:#eef5fb; color:#2c4e67; font-weight:700; }
            .audit-data-table input { width:100%; }
            .audit-formulas { margin-top:12px; border:1px solid #d7e5f0; border-radius:12px; padding:10px; background:#f7fbff; }
            .formula-line { display:flex; gap:8px; align-items:baseline; font-size:13px; color:#2c4e67; padding:6px 0; border-bottom:1px solid #e0ecf5; }
            .formula-line:last-child { border-bottom:none; }
            .formula-label { font-weight:700; }
            .btn-secondary { background:#dbe9f5; color:#1d4f73; }
            @media (max-width:900px) {
                .audit-edit-grid { grid-template-columns:1fr; }
            }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <h1 style="margin:0;">Edycja audytu</h1>
                <p class="muted" style="margin:4px 0 0;">{{ $audit->title }}</p>
            </div>
            <a href="{{ route('audits.index', ['tab' => 'in-progress']) }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">← Wróć do audytów</a>
        </div>

        <form method="POST" action="{{ route('audits.update', $audit) }}">
            @csrf
            @method('PATCH')

            <div class="audit-edit-grid">
                <div style="grid-column:1 / -1;">
                    <label>Nazwa audytu *</label>
                    <input type="text" name="title" value="{{ old('title', $audit->title) }}" required>
                </div>

                <div>
                    <label>Rodzaj audytu *</label>
                    <select name="audit_type_id" id="audit-type-select" required onchange="toggleAuditSectionsByType(this.value)">
                        <option value="">Wybierz rodzaj audytu</option>
                        @foreach($auditTypes as $type)
                            <option value="{{ $type->id }}" @selected((string) old('audit_type_id', $audit->audit_type_id) === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Firma</label>
                    <select name="company_id">
                        <option value="">Brak</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) old('company_id', $audit->company_id) === (string) $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Audytor</label>
                    <select name="auditor_id">
                        <option value="">Brak</option>
                        @foreach($auditors as $auditor)
                            <option value="{{ $auditor->id }}" @selected((string) old('auditor_id', $audit->auditor_id) === (string) $auditor->id)>{{ $auditor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top:14px;">
                <h3 style="margin:0 0 8px;">Sekcje audytu</h3>
                @php
                    $payload = is_array(old('section_payload'))
                        ? old('section_payload')
                        : (is_array($audit->data_payload) ? $audit->data_payload : []);
                @endphp

                @foreach($auditTypes as $type)
                    <div class="audit-type-sections" data-type-id="{{ $type->id }}" style="display:none;">
                        @forelse($type->sections as $section)
                            @php
                                $sectionPayload = $payload[(string) $section->id] ?? [];
                                $taskValues = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
                                $fieldRows = collect($section->data_fields ?? [])->map(function ($field) {
                                    if (is_array($field)) {
                                        return [
                                            'key' => trim((string) ($field['key'] ?? \Illuminate\Support\Str::slug((string) ($field['name'] ?? ''), '_'))),
                                            'name' => trim((string) ($field['name'] ?? '')),
                                            'unit' => trim((string) ($field['unit'] ?? '')),
                                                'kind' => trim((string) ($field['kind'] ?? 'number')),
                                                'options' => collect($field['options'] ?? [])->map(fn ($item) => trim((string) $item))->filter()->values()->all(),
                                        ];
                                    }

                                    return [
                                        'key' => \Illuminate\Support\Str::slug((string) $field, '_'),
                                        'name' => trim((string) $field),
                                        'unit' => '',
                                            'kind' => 'number',
                                            'options' => [],
                                    ];
                                })->filter(fn ($field) => $field['name'] !== '')->values();

                                $payloadRows = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
                            @endphp
                            <div class="audit-section" data-audit-section="{{ $section->id }}">
                                <h4>{{ $section->name }}</h4>

                                @if(!empty($section->tasks))
                                    <div class="audit-task-list">
                                        @foreach($section->tasks as $task)
                                            <label style="display:flex; gap:8px; align-items:flex-start;">
                                                <input type="checkbox" name="section_payload[{{ $section->id }}][tasks][{{ $task }}]" value="1" @checked(!empty($taskValues[$task]))>
                                                <span>{{ $task }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                @if($fieldRows->isNotEmpty())
                                    <table class="audit-data-table">
                                        <thead>
                                            <tr>
                                                <th>Dana</th>
                                                <th>Jednostka</th>
                                                <th>Wartość</th>
                                                <th>Uwagi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fieldRows as $rowIndex => $field)
                                                @php
                                                    $rowPayload = is_array($payloadRows[$rowIndex] ?? null) ? $payloadRows[$rowIndex] : [];
                                                    $legacyValue = is_array($payloadRows) ? ($payloadRows[$field['name']] ?? '') : '';
                                                    $value = (string) ($rowPayload['value'] ?? $legacyValue ?? '');
                                                    $notes = (string) ($rowPayload['notes'] ?? '');
                                                @endphp
                                                <tr>
                                                    <td>{{ $field['name'] }}</td>
                                                    <td>{{ $field['unit'] !== '' ? $field['unit'] : '—' }}</td>
                                                    <td>
                                                        @if($field['kind'] === 'boolean')
                                                            <select data-audit-section="{{ $section->id }}" data-field-token="{{ $field['key'] }}" class="formula-source" name="section_payload[{{ $section->id }}][rows][{{ $rowIndex }}][value]">
                                                                <option value="">—</option>
                                                                <option value="tak" @selected($value === 'tak')>Tak</option>
                                                                <option value="nie" @selected($value === 'nie')>Nie</option>
                                                            </select>
                                                        @elseif($field['kind'] === 'select')
                                                            <select data-audit-section="{{ $section->id }}" data-field-token="{{ $field['key'] }}" class="formula-source" name="section_payload[{{ $section->id }}][rows][{{ $rowIndex }}][value]">
                                                                <option value="">—</option>
                                                                @foreach($field['options'] as $option)
                                                                    <option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>
                                                                @endforeach
                                                            </select>
                                                        @elseif($field['kind'] === 'text')
                                                            <input data-audit-section="{{ $section->id }}" data-field-token="{{ $field['key'] }}" class="formula-source" type="text" name="section_payload[{{ $section->id }}][rows][{{ $rowIndex }}][value]" value="{{ $value }}" placeholder="Wpisz opis">
                                                        @else
                                                            <input data-audit-section="{{ $section->id }}" data-field-token="{{ $field['key'] }}" class="formula-source" type="number" step="any" name="section_payload[{{ $section->id }}][rows][{{ $rowIndex }}][value]" value="{{ $value }}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text" name="section_payload[{{ $section->id }}][rows][{{ $rowIndex }}][notes]" value="{{ $notes }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                @if(!empty($section->formulas))
                                    <div class="audit-formulas">
                                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin:0 0 8px;">
                                            <h4 style="margin:0;">Wyniki obliczeń</h4>
                                            <button type="button" class="btn-secondary" onclick="recalculateFormulasForSection('{{ $section->id }}')">Przelicz</button>
                                        </div>
                                        @foreach($section->formulas as $formula)
                                            @if(!empty($formula['label']) && !empty($formula['expression']))
                                                <div class="formula-line">
                                                    <span class="formula-label">{{ $formula['label'] }}</span>
                                                    <span>=</span>
                                                    <strong data-audit-section="{{ $section->id }}" data-formula-expression="{{ $formula['expression'] }}" data-formula-unit="{{ (string) ($formula['unit'] ?? '') }}">—</strong>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="muted">Ten rodzaj audytu nie ma jeszcze sekcji.</div>
                        @endforelse
                    </div>
                @endforeach
            </div>

            <div style="margin-top:14px; display:flex; justify-content:flex-end; gap:8px;">
                <a href="{{ route('audits.index', ['tab' => 'in-progress']) }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">Anuluj</a>
                <button type="submit">Zapisz audyt</button>
            </div>
        </form>
    </section>

    <script>
        function toggleAuditSectionsByType(typeId) {
            document.querySelectorAll('.audit-type-sections').forEach((container) => {
                container.style.display = container.getAttribute('data-type-id') === String(typeId) ? 'block' : 'none';
            });

            recalculateFormulas();
        }

        function recalculateFormulas() {
            const activeContainer = Array.from(document.querySelectorAll('.audit-type-sections')).find((container) => container.style.display !== 'none');
            if (!activeContainer) {
                return;
            }

            activeContainer.querySelectorAll('.audit-section[data-audit-section]').forEach((section) => {
                const sectionId = section.getAttribute('data-audit-section');
                if (!sectionId) {
                    return;
                }

                recalculateFormulasForSection(sectionId);
            });
        }

        function recalculateFormulasForSection(sectionId) {
            const section = document.querySelector(`.audit-section[data-audit-section="${sectionId}"]`);
            if (!section) {
                return;
            }

            const values = {};
            section.querySelectorAll(`.formula-source[data-audit-section="${sectionId}"]`).forEach((input) => {
                const token = input.getAttribute('data-field-token');
                if (!token) {
                    return;
                }

                let raw = String(input.value ?? '').trim();
                if (raw === '') {
                    values[token] = null;
                    return;
                }

                if (raw.toLowerCase() === 'tak') {
                    values[token] = 1;
                    return;
                }

                if (raw.toLowerCase() === 'nie') {
                    values[token] = 0;
                    return;
                }

                raw = raw.replace(',', '.');

                const number = Number(raw);
                values[token] = Number.isFinite(number) ? number : null;
            });

            section.querySelectorAll(`[data-formula-expression][data-audit-section="${sectionId}"]`).forEach((output) => {
                const expression = String(output.getAttribute('data-formula-expression') ?? '').trim();
                const unit = String(output.getAttribute('data-formula-unit') ?? '').trim();
                const usedTokens = Array.from(new Set(Array.from(expression.matchAll(/\{([a-zA-Z0-9_]+)\}/g)).map((match) => match[1])));

                if (usedTokens.some((token) => values[token] === null || values[token] === undefined)) {
                    output.textContent = '—';
                    return;
                }

                const replaced = expression.replace(/\{([a-zA-Z0-9_]+)\}/g, (_, token) => String(values[token] ?? 0));
                const normalized = replaced.replace(/,/g, '.');

                if (!/^[0-9+\-*/().\s]+$/.test(normalized)) {
                    output.textContent = '—';
                    return;
                }

                try {
                    const result = Function('return (' + normalized + ')')();
                    if (Number.isFinite(result)) {
                        const rounded = String(Math.round((result + Number.EPSILON) * 1000000) / 1000000);
                        output.textContent = unit !== '' ? `${rounded} ${unit}` : rounded;
                    } else {
                        output.textContent = '—';
                    }
                } catch (error) {
                    output.textContent = '—';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('audit-type-select');
            if (!select) {
                return;
            }

            toggleAuditSectionsByType(select.value);

            document.addEventListener('input', function (event) {
                if (event.target instanceof HTMLElement && event.target.classList.contains('formula-source')) {
                    recalculateFormulas();
                }
            });

            document.addEventListener('change', function (event) {
                if (event.target instanceof HTMLElement && event.target.classList.contains('formula-source')) {
                    recalculateFormulas();
                }
            });
        });
    </script>
</x-layouts.app>
