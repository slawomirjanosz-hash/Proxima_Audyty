<x-layouts.app>
    <section class="panel">
        <style>
            .audit-type-card { border: 1px solid #dfeaf3; border-radius: 10px; padding: 10px; margin-top: 10px; background:#fbfdff; }
            .audit-type-section { margin-top: 8px; padding: 8px; border:1px solid #edf2f7; border-radius:8px; background:#fff; }
            .audit-builder { display:none; margin-top:14px; padding:12px; border:1px solid #dfeaf3; border-radius:10px; background:#f9fcff; }
            .btn-secondary { background: #dbe9f5; color: #1d4f73; }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <h1 style="margin:0;">Ustawienia audytów</h1>
                <p class="muted" style="margin:4px 0 0;">Rodzaje audytów, sekcje, zadania i pola danych.</p>
            </div>
            <a href="{{ route('audits.index') }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">← Wróć do audytów</a>
        </div>

        <button type="button" class="edit-user-btn" onclick="toggleAuditTypeForm()">Dodaj rodzaj audytu</button>

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
                <div class="audit-type-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
                        <strong>{{ $type->name }}</strong>
                        <form method="POST" action="{{ route('audits.settings.audit-type-destroy', $type) }}" onsubmit="return confirm('Usunąć rodzaj audytu?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary">Usuń</button>
                        </form>
                    </div>

                    @forelse($type->sections as $section)
                        <div class="audit-type-section">
                            <div style="font-weight:700; margin-bottom:6px;">{{ $section->name }}</div>
                            <div style="font-size:12px; color:#4c6373;"><strong>Zadania:</strong> {{ !empty($section->tasks) ? implode(', ', $section->tasks) : 'Brak' }}</div>
                            <div style="font-size:12px; color:#4c6373; margin-top:4px;"><strong>Dane do wpisania:</strong> {{ !empty($section->data_fields) ? implode(', ', $section->data_fields) : 'Brak' }}</div>
                        </div>
                    @empty
                        <div class="muted" style="margin-top:8px;">Brak sekcji</div>
                    @endforelse
                </div>
            @empty
                <div class="muted">Brak zdefiniowanych rodzajów audytu.</div>
            @endforelse
        </div>
    </section>

    <script>
        function toggleAuditTypeForm() {
            const form = document.getElementById('add-audit-type-form');
            if (!form) {
                return;
            }

            const visible = form.style.display !== 'none';
            form.style.display = visible ? 'none' : 'block';
        }

        function addAuditTypeSection(defaults = {}) {
            const container = document.getElementById('audit-type-sections');
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
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373;">Dane do wpisania (jedno pole w linii)</label>
                        <textarea name="sections[${index}][fields_text]" rows="3" style="width:100%; border:1px solid #c9d7e3; border-radius:9px; padding:8px 10px; font-size:14px;">${defaults.fieldsText || ''}</textarea>
                    </div>
                </div>
            `;

            container.appendChild(wrapper);
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('audit-type-sections') && document.getElementById('audit-type-sections').children.length === 0) {
                addAuditTypeSection();
            }
        });
    </script>
</x-layouts.app>
