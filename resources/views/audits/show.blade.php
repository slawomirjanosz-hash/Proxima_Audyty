<x-layouts.app>
    <section class="panel">
        <style>
            .audit-info-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; }
            .audit-info-card { border:1px solid #dbe8f3; border-radius:12px; background:#f9fcff; padding:10px; }
            .audit-info-card strong { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:4px; }
            .audit-section { border:1px solid #d7e5f0; border-left:5px solid #7fb4e1; border-radius:12px; padding:12px; background:#f8fbff; margin-top:12px; }
            .audit-section h4 { margin:0 0 8px; font-size:16px; color:#10344c; }
            .audit-data-table { width:100%; border-collapse:collapse; margin-top:10px; }
            .audit-data-table th, .audit-data-table td { border:1px solid #e0ecf5; padding:8px; font-size:13px; text-align:left; }
            .audit-data-table th { background:#eef5fb; color:#2c4e67; font-weight:700; }
            .audit-formulas { margin-top:12px; border:1px solid #d7e5f0; border-radius:12px; padding:10px; background:#f7fbff; }
            .formula-line { display:flex; gap:8px; align-items:baseline; font-size:13px; color:#2c4e67; padding:6px 0; border-bottom:1px solid #e0ecf5; }
            .formula-line:last-child { border-bottom:none; }
            .formula-label { font-weight:700; }
            .btn-secondary { background:#dbe9f5; color:#1d4f73; }
            @media (max-width: 900px) {
                .audit-info-grid { grid-template-columns:1fr; }
            }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <h1 style="margin:0;">Info audytu</h1>
                <p class="muted" style="margin:4px 0 0;">Podgląd danych bez możliwości edycji.</p>
            </div>
            <a href="{{ route('audits.index', ['tab' => 'in-progress']) }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">← Wróć do audytów</a>
        </div>

        <div class="audit-info-grid">
            <div class="audit-info-card">
                <strong>Nazwa audytu</strong>
                {{ $audit->title }}
            </div>
            <div class="audit-info-card">
                <strong>Rodzaj audytu</strong>
                {{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Status</strong>
                {{ $audit->status }}
            </div>
            <div class="audit-info-card">
                <strong>Firma</strong>
                {{ $audit->company?->name ?? '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Audytor</strong>
                {{ $audit->auditor?->name ?? '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Ostatnia aktualizacja</strong>
                {{ $audit->updated_at?->format('Y-m-d H:i') ?? '—' }}
            </div>
        </div>

        @php
            $payload = is_array($audit->data_payload) ? $audit->data_payload : [];
            $sections = $audit->auditType?->sections ?? collect();
        @endphp

        <div style="margin-top:14px;">
            <?php if ($sections->isNotEmpty()): ?>
                <?php foreach ($sections as $sectionIndex => $section): ?>
                    @php
                        $sectionPayload = $payload[(string) $section->id] ?? [];
                        $taskValues = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
                        $sectionTasks = is_array($section->tasks ?? null) ? $section->tasks : [];
                        $sectionFormulas = is_array($section->formulas ?? null) ? $section->formulas : [];
                        $fieldRows = collect($section->data_fields ?? [])->map(function ($field) {
                            if (is_array($field)) {
                                return [
                                    'key' => trim((string) ($field['key'] ?? \Illuminate\Support\Str::slug((string) ($field['name'] ?? ''), '_'))),
                                    'name' => trim((string) ($field['name'] ?? '')),
                                    'unit' => trim((string) ($field['unit'] ?? '')),
                                ];
                            }

                            return [
                                'key' => \Illuminate\Support\Str::slug((string) $field, '_'),
                                'name' => trim((string) $field),
                                'unit' => '',
                            ];
                        })->filter(fn ($field) => $field['name'] !== '')->values();

                        $payloadRows = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
                    @endphp
                    <div class="audit-section" data-audit-section="{{ $section->id }}">
                        <h4>{{ $sectionIndex + 1 }}. {{ $section->name }}</h4>

                        <?php if (!empty($sectionTasks)): ?>
                            <div style="font-size:12px; color:#4c6373; margin-bottom:6px;"><strong>Zadania:</strong></div>
                            <ul style="margin:0 0 8px 18px; padding:0; display:grid; gap:4px; color:#355468;">
                                <?php foreach ($sectionTasks as $task): ?>
                                    <li>
                                        {{ $task }}
                                        <?php if (!empty($taskValues[$task])): ?>
                                            <span style="color:#0c5f28; font-weight:700;">(wykonane)</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($fieldRows->isNotEmpty()): ?>
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
                                <?php foreach ($fieldRows as $rowIndex => $field): ?>
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
                                            <span
                                                class="formula-source"
                                                data-audit-section="{{ $section->id }}"
                                                data-field-token="{{ $field['key'] }}"
                                                data-field-value="{{ $value }}"
                                            >{{ $value !== '' ? $value : '—' }}</span>
                                        </td>
                                        <td>{{ $notes !== '' ? $notes : '—' }}</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php if (!empty($sectionFormulas)): ?>
                            <div class="audit-formulas">
                                <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:6px;">Wyniki obliczeń</div>
                                <?php foreach ($sectionFormulas as $formula): ?>
                                    <?php if (!empty($formula['label']) && !empty($formula['expression'])): ?>
                                        <div class="formula-line">
                                            <span class="formula-label">{{ $formula['label'] }}</span>
                                            <span>=</span>
                                            <strong data-audit-section="{{ $section->id }}" data-formula-expression="{{ $formula['expression'] }}" data-formula-unit="{{ (string) ($formula['unit'] ?? '') }}">—</strong>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($sections->isEmpty()): ?>
                <?php if (!empty($payload)): ?>
                    <div class="audit-section">
                        <h4>Zapisane dane audytu</h4>
                        <div class="muted" style="margin-bottom:8px;">Nie znaleziono aktualnych sekcji rodzaju audytu, ale zapisane dane są nadal dostępne.</div>
                        <pre style="white-space:pre-wrap; margin:0; font-size:12px; color:#2c4e67;">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                <?php else: ?>
                    <div class="muted">Ten audyt nie ma zdefiniowanych sekcji w rodzaju audytu.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
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

                let raw = String(input.getAttribute('data-field-value') ?? '').trim();
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

        function recalculateFormulas() {
            document.querySelectorAll('.audit-section[data-audit-section]').forEach((section) => {
                const sectionId = section.getAttribute('data-audit-section');
                if (!sectionId) {
                    return;
                }

                recalculateFormulasForSection(sectionId);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            recalculateFormulas();
        });
    </script>
</x-layouts.app>
