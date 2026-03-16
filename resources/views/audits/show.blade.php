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

        @php($payload = is_array($audit->data_payload) ? $audit->data_payload : [])

        <div style="margin-top:14px;">
            @forelse(($audit->auditType?->sections ?? collect()) as $section)
                @php
                    $sectionPayload = $payload[(string) $section->id] ?? [];
                    $taskValues = is_array($sectionPayload['tasks'] ?? null) ? $sectionPayload['tasks'] : [];
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
                <div class="audit-section">
                    <h4>{{ $loop->iteration }}. {{ $section->name }}</h4>

                    @if(!empty($section->tasks))
                        <div style="font-size:12px; color:#4c6373; margin-bottom:6px;"><strong>Zadania:</strong></div>
                        <ul style="margin:0 0 8px 18px; padding:0; display:grid; gap:4px; color:#355468;">
                            @foreach($section->tasks as $task)
                                <li>
                                    {{ $task }}
                                    @if(!empty($taskValues[$task]))
                                        <span style="color:#0c5f28; font-weight:700;">(wykonane)</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
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
                                    <td>{{ $value !== '' ? $value : '—' }}</td>
                                    <td>{{ $notes !== '' ? $notes : '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if(!empty($section->formulas))
                        <div class="audit-formulas">
                            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:6px;">Wzory sekcji</div>
                            @foreach($section->formulas as $formula)
                                @if(!empty($formula['label']) && !empty($formula['expression']))
                                    <div class="formula-line">
                                        <span class="formula-label">{{ $formula['label'] }}</span>
                                        <span>=</span>
                                        <span>{{ $formula['expression'] }}</span>
                                        @if(!empty($formula['unit']))
                                            <span>({{ $formula['unit'] }})</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="muted">Ten audyt nie ma zdefiniowanych sekcji w rodzaju audytu.</div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
