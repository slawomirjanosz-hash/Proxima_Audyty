<x-layouts.app>
    <section class="panel">
        <style>
            .audit-edit-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
            .audit-edit-grid input, .audit-edit-grid select, .audit-edit-grid textarea { width:100%; }
            .audit-section { border:1px solid #d7e5f0; border-radius:12px; padding:12px; background:#f9fcff; margin-top:10px; }
            .audit-section h4 { margin:0 0 8px; }
            .audit-task-list { display:grid; gap:6px; }
            .audit-field-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; margin-top:10px; }
            .btn-secondary { background:#dbe9f5; color:#1d4f73; }
            @media (max-width:900px) {
                .audit-edit-grid, .audit-field-grid { grid-template-columns:1fr; }
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
                                $fieldValues = is_array($sectionPayload['fields'] ?? null) ? $sectionPayload['fields'] : [];
                            @endphp
                            <div class="audit-section">
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

                                @if(!empty($section->data_fields))
                                    <div class="audit-field-grid">
                                        @foreach($section->data_fields as $field)
                                            <div>
                                                <label>{{ $field }}</label>
                                                <input type="text" name="section_payload[{{ $section->id }}][fields][{{ $field }}]" value="{{ (string) ($fieldValues[$field] ?? '') }}">
                                            </div>
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
        }

        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('audit-type-select');
            if (!select) {
                return;
            }

            toggleAuditSectionsByType(select.value);
        });
    </script>
</x-layouts.app>
