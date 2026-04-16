<x-layouts.app>
    <section class="panel">
        <style>
            .audit-tab-btn { padding: 8px 12px; border-radius: 10px; border: 1px solid #d7e5f0; background: #eef5fb; font-weight: 700; color: #28485f; cursor: pointer; }
            .audit-tab-btn.active { background: #fff; border-color: #0e89d8; color: #0e89d8; }
            .audit-tab-content { display: none; margin-top: 14px; }
            .audit-tab-content.active { display: block; }
            .audit-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .audit-form-grid input, .audit-form-grid select { width: 100%; }
            .audit-actions { display: flex; gap: 8px; align-items: center; }
            .btn-secondary { background: #dbe9f5; color: #1d4f73; }
            .status-pill { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 6px; }
            .status-pill.completed { background: #d9f6e3; color: #0c5f28; }
            .audit-list { display:grid; gap:10px; }
            .audit-list-item { border:1px solid #d7e5f0; border-radius:12px; background:#fbfdff; overflow:hidden; }
            .audit-list-header { width:100%; border:none; background:#f6fbff; padding:12px; display:flex; justify-content:space-between; align-items:center; gap:10px; cursor:pointer; text-align:left; }
            .audit-list-header:hover { background:#eef6ff; }
            .audit-list-item.open .audit-list-header { background:#eaf3ff; }
            .audit-list-main { display:flex; flex-direction:column; gap:3px; }
            .audit-list-title { font-weight:800; color:#10344c; }
            .audit-list-meta { font-size:12px; color:#4c6373; }
            .audit-list-chevron { color:#6b8aa3; font-size:16px; transition:transform .2s; }
            .audit-list-item.open .audit-list-chevron { transform:rotate(180deg); }
            .audit-list-body { display:none; padding:12px; border-top:1px solid #e0ecf5; }
            .audit-list-item.open .audit-list-body { display:block; }
            .audit-list-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:8px; margin-bottom:10px; }
            .audit-list-grid div { font-size:13px; color:#355468; }
            .audit-list-grid strong { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.4px; color:#6b8aa3; margin-bottom:2px; }
            @media (max-width: 900px) {
                .audit-form-grid { grid-template-columns: 1fr; }
                .audit-actions { flex-wrap: wrap; }
                .audit-list-grid { grid-template-columns:1fr; }
            }
        </style>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
            <h1 style="margin:0;">Rodzaje audytów</h1>
            <a href="{{ route('audits.settings') }}" class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;">⚙️ Ustawienia</a>
        </div>
        <p class="muted" style="margin:0 0 14px;">Zarządzanie audytami: tworzenie, prowadzenie i zamykanie.</p>

        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
            <button type="button" class="audit-tab-btn {{ $activeTab === 'new' ? 'active' : '' }}" onclick="switchAuditTab(event, 'new')">➕ Nowy audyt</button>
            <button type="button" class="audit-tab-btn {{ $activeTab === 'in-progress' ? 'active' : '' }}" onclick="switchAuditTab(event, 'in-progress')">🛠 Audyty w toku ({{ $inProgressAudits->count() }})</button>
            <button type="button" class="audit-tab-btn {{ $activeTab === 'completed' ? 'active' : '' }}" onclick="switchAuditTab(event, 'completed')">✅ Audyty zakończone ({{ $completedAudits->count() }})</button>
        </div>

        <div id="audit-tab-new" class="audit-tab-content {{ $activeTab === 'new' ? 'active' : '' }}">
            <h3 style="margin:0 0 10px;">Dodaj nowy audyt</h3>
            @if($auditTypes->isEmpty())
                <div style="margin-bottom:12px; padding:10px; border:1px solid #cfe2f5; border-radius:10px; background:#eaf5ff; color:#145086;">
                    Najpierw dodaj rodzaj audytu w Ustawieniach audytów.
                    <a href="{{ route('audits.settings') }}" style="margin-left:8px; font-weight:700;">Przejdź do ustawień</a>
                </div>
            @endif
            <form method="POST" action="{{ route('audits.store') }}">
                @csrf
                <div class="audit-form-grid">
                    <div style="grid-column:1 / -1;">
                        <label>Nazwa audytu *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label>Rodzaj audytu *</label>
                        <select name="audit_type_id" required>
                            <option value="">Wybierz rodzaj audytu</option>
                            @foreach($auditTypes as $type)
                                <option value="{{ $type->id }}" @selected((string) old('audit_type_id') === (string) $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Firma</label>
                        <select name="company_id">
                            <option value="">Brak</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Audytor</label>
                        <select name="auditor_id">
                            <option value="">Brak</option>
                            @foreach($auditors as $auditor)
                                <option value="{{ $auditor->id }}" @selected((string) old('auditor_id') === (string) $auditor->id)>{{ $auditor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                    <button type="submit">Zapisz audyt</button>
                </div>
            </form>
        </div>

        <div id="audit-tab-in-progress" class="audit-tab-content {{ $activeTab === 'in-progress' ? 'active' : '' }}">
            <h3 style="margin:0 0 10px;">Audyty w toku</h3>
            <div class="audit-list">
                @forelse($inProgressAudits as $audit)
                    <div class="audit-list-item" id="in-progress-audit-{{ $audit->id }}">
                        <button type="button" class="audit-list-header" onclick="toggleAuditListItem('in-progress-audit-{{ $audit->id }}')">
                            <div class="audit-list-main">
                                <span class="audit-list-title">{{ $loop->iteration }}. {{ $audit->title }}</span>
                                <span class="audit-list-meta">{{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }} • {{ $audit->company?->name ?? '—' }}</span>
                            </div>
                            <span class="audit-list-chevron">&#9660;</span>
                        </button>
                        <div class="audit-list-body">
                            <div class="audit-list-grid">
                                <div>
                                    <strong>Rodzaj audytu</strong>
                                    {{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }}
                                </div>
                                <div>
                                    <strong>Firma</strong>
                                    {{ $audit->company?->name ?? '—' }}
                                </div>
                                <div>
                                    <strong>Audytor</strong>
                                    {{ $audit->auditor?->name ?? '—' }}
                                </div>
                            </div>
                            <div class="audit-actions">
                                <a class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;" href="{{ route('audits.show', $audit) }}">Info</a>
                                <form method="POST" action="{{ route('audits.complete', $audit) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit">Zakończ</button>
                                </form>
                                <a class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;" href="{{ route('audits.edit', $audit) }}">Edytuj</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="muted">Brak audytów w toku.</div>
                @endforelse
            </div>
        </div>

        <div id="audit-tab-completed" class="audit-tab-content {{ $activeTab === 'completed' ? 'active' : '' }}">
            <h3 style="margin:0 0 10px;">Audyty zakończone</h3>
            <div class="audit-list">
                @forelse($completedAudits as $audit)
                    <div class="audit-list-item" id="completed-audit-{{ $audit->id }}">
                        <button type="button" class="audit-list-header" onclick="toggleAuditListItem('completed-audit-{{ $audit->id }}')">
                            <div class="audit-list-main">
                                <span class="audit-list-title">{{ $loop->iteration }}. {{ $audit->title }}</span>
                                <span class="audit-list-meta">{{ $audit->company?->name ?? '—' }} • {{ $audit->completed_at?->format('Y-m-d H:i') ?? '—' }}</span>
                            </div>
                            <span class="audit-list-chevron">&#9660;</span>
                        </button>
                        <div class="audit-list-body">
                            <div class="audit-list-grid">
                                <div>
                                    <strong>Rodzaj audytu</strong>
                                    {{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }}
                                </div>
                                <div>
                                    <strong>Firma</strong>
                                    {{ $audit->company?->name ?? '—' }}
                                </div>
                                <div>
                                    <strong>Status</strong>
                                    <span class="status-pill completed">Zakończony</span>
                                </div>
                            </div>
                            <div class="audit-actions">
                                <a class="btn-secondary" style="text-decoration:none; padding:8px 10px; border-radius:9px;" href="{{ route('audits.show', $audit) }}">Info</a>
                                <form method="POST" action="{{ route('audits.reopen', $audit) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary">W toku</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="muted">Brak audytów zakończonych.</div>
                @endforelse
            </div>
        </div>

        <script>
            function switchAuditTab(event, tabName) {
                document.querySelectorAll('.audit-tab-btn').forEach((btn) => btn.classList.remove('active'));
                document.querySelectorAll('.audit-tab-content').forEach((content) => content.classList.remove('active'));
                event.target.classList.add('active');
                const tab = document.getElementById('audit-tab-' + tabName);
                if (tab) tab.classList.add('active');
            }

            function toggleAuditListItem(itemId) {
                const item = document.getElementById(itemId);
                if (!item) {
                    return;
                }

                item.classList.toggle('open');
            }
        </script>
    </section>
</x-layouts.app>
