<x-layouts.app>
    <section class="panel">
        <style>
            .tab-button { padding: 8px 12px; border-radius: 10px; border: 1px solid #d7e5f0; background: #eef5fb; font-weight: 700; color: #28485f; cursor: pointer; }
            .tab-button.active { background: #fff; border-color: #0e89d8; color: #0e89d8; }
            .tab-content { display: none; margin-top: 14px; }
            .tab-content.active { display: block; }
            .dashboard-sections { display: flex; flex-direction: column; gap: 10px; }
            .dashboard-section { border: 1px solid #d7e5f0; border-radius: 12px; background: #fff; padding: 10px; }
            .dashboard-section.dragging { opacity: 0.55; }
            .dashboard-section.drag-over { border-style: dashed; border-color: #0e89d8; background: #f0f8ff; }
            .dashboard-section-header { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
            .dashboard-drag-handle { border: 1px solid #d7e5f0; background: #eef5fb; color: #28485f; border-radius: 8px; padding: 3px 8px; cursor: grab; font-size: 12px; font-weight: 700; user-select: none; }
            .dashboard-drag-handle:active { cursor: grabbing; }
            .dashboard-section-title { margin: 0; font-size: 18px; }
            #modal-content input:not([type="checkbox"]),
            #modal-content select,
            #modal-content textarea { width: 100%; }
            .crm-form-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:10px; }
            @media (max-width: 860px) {
                .crm-form-grid { grid-template-columns:1fr; }
            }
        </style>

        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
            <h1 style="margin:0;">👥 CRM - System Zarządzania Relacjami z Klientami</h1>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('crm.diagnostics') }}" class="login-btn" style="text-decoration:none;">🔧 Diagnostyka</a>
                <a href="{{ route('crm.settings') }}" class="login-btn" style="text-decoration:none;">⚙️ Ustawienia CRM</a>
            </div>
        </div>

        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
            <button class="tab-button active" onclick="switchTab(event,'dashboard')">📊 Dashboard</button>
            <button class="tab-button" onclick="switchTab(event,'deals')">💼 Lejek Sprzedażowy</button>
            <button class="tab-button" onclick="switchTab(event,'companies')">🏢 Firmy</button>
            <button class="tab-button" onclick="switchTab(event,'activities')">📝 Historia</button>
        </div>

        <div id="tab-dashboard" class="tab-content active">
            <h2 style="margin:0 0 10px;">📊 Przegląd</h2>
            <div id="dashboard-sections" class="dashboard-sections">
                <div class="dashboard-section" data-section-id="stats" draggable="true">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">📈 Kluczowe wskaźniki</h3>
                        <span class="dashboard-drag-handle" title="Przeciągnij sekcję">↕ Przesuń</span>
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
                        <div style="padding:10px; border-radius:10px; background:#0e89d8; color:#fff;"><div>Firmy CRM</div><div style="font-size:26px; font-weight:800;">{{ $stats['total_companies'] }}</div></div>
                        <div style="padding:10px; border-radius:10px; background:#8b5cf6; color:#fff;"><div>Aktywne szanse</div><div style="font-size:26px; font-weight:800;">{{ $stats['active_deals'] }}</div></div>
                        <div style="padding:10px; border-radius:10px; background:#10b981; color:#fff;"><div>Wartość pipeline</div><div style="font-size:26px; font-weight:800;">{{ number_format($stats['total_pipeline_value'], 0, ',', ' ') }} zł</div></div>
                        <div style="padding:10px; border-radius:10px; background:#ef4444; color:#fff;"><div>Zadań po terminie</div><div style="font-size:26px; font-weight:800;">{{ $stats['overdue_tasks'] }}</div></div>
                    </div>
                </div>

                <div class="dashboard-section" data-section-id="funnel" draggable="true">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">💼 Lejek Sprzedażowy</h3>
                        <span class="dashboard-drag-handle" title="Przeciągnij sekcję">↕ Przesuń</span>
                    </div>
                    @if($stats['deals_by_stage']->isEmpty())
                        <div style="padding:14px; border:1px solid #e4edf3; border-radius:10px; background:#f8fbff; text-align:center;" class="muted">
                            Brak aktywnych szans sprzedażowych
                        </div>
                    @else
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @foreach($crmStages as $stage)
                                @if($stats['deals_by_stage']->has($stage->slug))
                                    @php($stageDeals = $stats['deals_by_stage'][$stage->slug])
                                    <div style="border:1px solid {{ $stage->color }}55; background:{{ $stage->color }}1A; border-radius:10px; padding:10px;">
                                        <div style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
                                            <div style="min-width:120px; text-align:center;">
                                                <div style="font-size:12px; font-weight:700;">{{ $stage->name }}</div>
                                                <div style="font-size:28px; font-weight:800;">{{ $stageDeals->count() }}</div>
                                                <div class="muted" style="font-size:12px;">{{ number_format($stageDeals->sum('value'), 0, ',', ' ') }} zł</div>
                                            </div>
                                            <div style="display:flex; flex-wrap:wrap; gap:8px; flex:1;">
                                                @foreach($stageDeals as $deal)
                                                    <div onclick="showDealPreview({{ $deal->id }})" style="cursor:pointer; border:1px solid #d7e5f0; border-radius:10px; background:#fff; padding:8px; min-width:200px; max-width:250px;">
                                                        <div style="font-weight:700; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $deal->name }}">{{ $deal->name }}</div>
                                                        <div class="muted" style="font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">🏢 {{ $deal->company->name ?? 'Brak firmy' }}</div>
                                                        <div class="muted" style="font-size:12px;">📅 {{ $deal->expected_close_date ? $deal->expected_close_date->format('d.m.Y') : 'Brak daty' }}</div>
                                                        <div style="font-size:12px; font-weight:800; color:#0f8a45;">{{ number_format((float) $deal->value, 0, ',', ' ') }} {{ $deal->currency }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="dashboard-section" data-section-id="won-lost" draggable="true">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">🎯 Ostatnio Wygrane/Przegrane</h3>
                        <span class="dashboard-drag-handle" title="Przeciągnij sekcję">↕ Przesuń</span>
                    </div>
                    @if($stats['recent_won_deals']->isEmpty())
                        <div style="padding:10px; border:1px solid #e4edf3; border-radius:10px; background:#f8fbff; text-align:center;" class="muted">Brak zakończonych szans</div>
                    @else
                        @php($stageMap = collect($crmStages)->keyBy('slug'))
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($stats['recent_won_deals']->take(5) as $deal)
                                @php($dealStage = $stageMap[$deal->stage] ?? null)
                                <div onclick="showDealPreview({{ $deal->id }})" style="cursor:pointer; display:flex; justify-content:space-between; gap:8px; border:1px solid {{ $dealStage?->color ?? '#e5e7eb' }}; background:{{ $dealStage?->color ?? '#f3f4f6' }}20; border-radius:10px; padding:10px;">
                                    <div>
                                        <div style="font-weight:700;">{{ $deal->name }}</div>
                                        <div class="muted" style="font-size:12px;">{{ $deal->company->name ?? 'Brak firmy' }} • {{ $deal->actual_close_date?->format('d.m.Y') }}</div>
                                    </div>
                                    <div style="font-weight:800; color:{{ $dealStage?->color ?? '#0f2330' }};">{{ number_format((float) $deal->value, 0, ',', ' ') }} zł</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="dashboard-section" data-section-id="tasks" draggable="true">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">✅ Zadania i Przypomnienia</h3>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <span class="dashboard-drag-handle" title="Przeciągnij sekcję">↕ Przesuń</span>
                            <button type="button" onclick="showTaskModal()">➕ Dodaj Zadanie</button>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Zadanie</th>
                                <th>Typ</th>
                                <th>Priorytet</th>
                                <th>Status</th>
                                <th>Termin</th>
                                <th>Przypisane do</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ ucfirst((string) $task->type) }}</td>
                                    <td>{{ ucfirst((string) $task->priority) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', (string) $task->status)) }}</td>
                                    <td>{{ $task->due_date ? $task->due_date->format('d.m.Y H:i') : '—' }}</td>
                                    <td>{{ $task->assignedTo?->short_name ?: $task->assignedTo?->name ?: 'Nie przypisane' }}</td>
                                    <td>
                                        <button class="btn-secondary" type="button" onclick="editTask({{ $task->id }})">✏️</button>
                                        <form action="{{ route('crm.task.delete', $task->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć zadanie?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-secondary">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="muted">Brak aktywnych zadań</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="dashboard-section" data-section-id="deals-list" draggable="true">
                    <div class="dashboard-section-header">
                        <h3 class="dashboard-section-title">💼 Lejek Sprzedażowy - Szanse</h3>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <span class="dashboard-drag-handle" title="Przeciągnij sekcję">↕ Przesuń</span>
                            <button type="button" onclick="showDealModal()">➕ Dodaj Szansę</button>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Firma</th>
                                <th>Etap</th>
                                <th>Wartość</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deals as $deal)
                                <tr>
                                    <td>{{ $deal->name }}</td>
                                    <td>{{ $deal->company?->name ?: '—' }}</td>
                                    <td>{{ $deal->stage }}</td>
                                    <td>{{ number_format((float) $deal->value, 2, ',', ' ') }} {{ $deal->currency }}</td>
                                    <td>
                                        <button class="btn-secondary" type="button" onclick="editDeal({{ $deal->id }})">✏️</button>
                                        <form action="{{ route('crm.deal.delete', $deal->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć szansę?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-secondary">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="muted">Brak szans CRM.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="tab-deals" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin:0 0 8px;">
                <h3 style="margin:0;">💼 Lejek Sprzedażowy - Szanse</h3>
                <button type="button" onclick="showDealModal()">➕ Dodaj Szansę</button>
            </div>
            @php($stageMap = collect($crmStages)->keyBy('slug'))
            <table>
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Firma</th>
                        <th>Wartość</th>
                        <th>Etap</th>
                        <th>Szansa %</th>
                        <th>Oczek. Zam</th>
                        <th>Opiekun</th>
                        <th>Przypisani</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deals as $deal)
                        @php($dealStage = $stageMap[$deal->stage] ?? null)
                        <tr>
                            <td>{{ $deal->name }}</td>
                            <td>{{ $deal->company->name ?? '-' }}</td>
                            <td><strong>{{ number_format((float) $deal->value, 2, ',', ' ') }} {{ $deal->currency }}</strong></td>
                            <td style="background:{{ $dealStage?->color ?? '#f3f4f6' }}20;">{{ $dealStage?->name ?? $deal->stage }}</td>
                            <td>{{ $deal->probability }}%</td>
                            <td>{{ $deal->expected_close_date ? $deal->expected_close_date->format('d.m.Y') : '-' }}</td>
                            <td>{{ $deal->owner?->short_name ?? $deal->owner?->name ?? '-' }}</td>
                            <td>
                                @if($deal->assignedUsers->count() > 0)
                                    {{ $deal->assignedUsers->map(fn($u) => $u->short_name ?: $u->name)->implode(', ') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button class="btn-secondary" type="button" onclick="editDeal({{ $deal->id }})">✏️</button>
                                <form action="{{ route('crm.deal.delete', $deal->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć szansę?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="muted">Brak szans sprzedażowych</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="tab-companies" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin:0 0 8px;">
                <h3 style="margin:0;">🏢 Firmy CRM</h3>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-secondary" onclick="showClientModal()">👤 Dodaj Klienta</button>
                    <button type="button" onclick="showCompanyModal()">➕ Dodaj Firmę</button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>NIP</th>
                        <th>Typ</th>
                        <th>Status</th>
                        <th>Opiekun</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->nip ?: '—' }}</td>
                            <td>{{ $company->type ?: '—' }}</td>
                            <td>{{ $company->status ?: '—' }}</td>
                            <td>{{ $company->owner?->name ?: '—' }}</td>
                            <td>
                                @if($company->system_company_id)
                                    <span class="muted" style="display:inline-block; margin-right:8px; font-size:12px; font-weight:700;">✓ W systemie</span>
                                @else
                                    <form action="{{ route('crm.company.addToSystem', $company->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Dodać firmę do ustawień systemu?')">
                                        @csrf
                                        <button type="submit" class="btn-secondary">➕ Do systemu</button>
                                    </form>
                                @endif
                                <button class="btn-secondary" type="button" onclick="editCompany({{ $company->id }})">✏️</button>
                                <form action="{{ route('crm.company.delete', $company->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć firmę?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">Brak firm CRM.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="tab-activities" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin:0 0 8px;">
                <h3 style="margin:0;">📝 Historia aktywności</h3>
                <button type="button" onclick="showActivityModal()">➕ Dodaj Aktywność</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Typ</th>
                        <th>Temat</th>
                        <th>Firma</th>
                        <th>Szansa</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->activity_date ? $activity->activity_date->format('d.m.Y H:i') : '—' }}</td>
                            <td>{{ ucfirst((string) $activity->type) }}</td>
                            <td>{{ $activity->subject }}</td>
                            <td>{{ $activity->company?->name ?: '—' }}</td>
                            <td>{{ $activity->deal?->name ?: '—' }}</td>
                            <td>
                                <button class="btn-secondary" type="button" onclick="editActivity({{ $activity->id }})">✏️</button>
                                <form action="{{ route('crm.activity.delete', $activity->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć aktywność?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">Brak aktywności CRM.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <h3 style="margin:16px 0 8px;">📚 Historia klientów i zmian CRM</h3>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Obiekt</th>
                        <th>Zmiana</th>
                        <th>Szczegóły</th>
                        <th>Użytkownik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taskChanges as $change)
                        <tr>
                            <td>{{ $change->created_at?->format('d.m.Y H:i') ?: '—' }}</td>
                            <td>{{ ucfirst((string) $change->entity_type) }} #{{ $change->entity_id }}</td>
                            <td>{{ ucfirst((string) $change->change_type) }}</td>
                            <td>
                                @php($details = is_array($change->change_details) ? $change->change_details : [])
                                @if(isset($details['name']))
                                    {{ $details['name'] }}
                                @elseif(isset($details['title']))
                                    {{ $details['title'] }}
                                @elseif(isset($details['subject']))
                                    {{ $details['subject'] }}
                                @else
                                    {{ json_encode($details, JSON_UNESCAPED_UNICODE) }}
                                @endif
                            </td>
                            <td>{{ $change->user?->name ?: 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Brak historii zmian CRM.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div id="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; align-items:center; justify-content:center;" onclick="closeModal(event)">
        <div style="background:#fff; width:min(920px, 96vw); max-height:90vh; overflow:auto; border-radius:14px; padding:16px; border:1px solid #d5e0ea;" onclick="event.stopPropagation()">
            <div id="modal-content"></div>
        </div>
    </div>

    <script>
        function switchTab(event, tabName) {
            document.querySelectorAll('.tab-button').forEach((btn) => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach((content) => content.classList.remove('active'));
            event.target.classList.add('active');
            const tab = document.getElementById('tab-' + tabName);
            if (tab) tab.classList.add('active');
        }

        function openModal(html) {
            document.getElementById('modal-content').innerHTML = html;
            document.getElementById('modal-overlay').style.display = 'flex';
        }

        function closeModal(event = null) {
            if (event && event.target && event.target.id !== 'modal-overlay') return;
            document.getElementById('modal-overlay').style.display = 'none';
        }

        async function searchByNip() {
            const input = document.getElementById('search-nip');
            const resultBox = document.getElementById('search-result');
            const nip = (input?.value || '').trim();

            if (!nip) {
                resultBox.textContent = 'Podaj NIP';
                return;
            }

            resultBox.textContent = 'Wyszukiwanie...';

            const response = await fetch(`{{ route('crm.company.searchByNip') }}?nip=${encodeURIComponent(nip)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!data.success || !data.data) {
                resultBox.textContent = data.message || 'Nie znaleziono danych.';
                return;
            }

            resultBox.textContent = `✓ Znaleziono firmę (${data.source})`;
            document.getElementById('company-name').value = data.data.name || '';
            document.getElementById('company-short_name').value = (data.data.name || '').substring(0, 12);
            document.getElementById('company-nip').value = data.data.nip || '';
            document.getElementById('company-email').value = data.data.email || '';
            document.getElementById('company-phone').value = data.data.phone || '';
            document.getElementById('company-city').value = data.data.city || '';
            document.getElementById('company-postal_code').value = data.data.postal_code || '';
            document.getElementById('company-address').value = data.data.address || '';
        }

        function companyFormHtml(action, method, data = {}) {
            return `
                <h3 style="margin:0 0 10px;">${method === 'PUT' ? 'Edytuj Firmę' : 'Dodaj Firmę'}</h3>
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px; margin-bottom:10px;">
                    <div style="font-weight:700; margin-bottom:6px;">🔍 Wyszukaj po NIP</div>
                    <div style="display:flex; gap:8px;">
                        <input id="search-nip" type="text" placeholder="Wpisz NIP" />
                        <button type="button" class="btn-secondary" onclick="searchByNip()">Szukaj</button>
                    </div>
                    <div id="search-result" class="muted" style="font-size:12px; margin-top:4px;"></div>
                </div>
                <form method="POST" action="${action}">
                    @csrf
                    ${method === 'PUT' ? '<input type="hidden" name="_method" value="PUT">' : ''}
                    <div class="crm-form-grid">
                        <div><label>Nazwa *</label><input id="company-name" name="name" value="${data.name || ''}" required></div>
                        <div><label>Nazwa skrócona</label><input id="company-short_name" name="short_name" value="${data.short_name || ''}"></div>
                        <div><label>NIP</label><input id="company-nip" name="nip" value="${data.nip || ''}"></div>
                        <div><label>Email</label><input id="company-email" name="email" value="${data.email || ''}"></div>
                        <div><label>Telefon</label><input id="company-phone" name="phone" value="${data.phone || ''}"></div>
                        <div><label>WWW</label><input name="website" value="${data.website || ''}"></div>
                        <div><label>Miasto</label><input id="company-city" name="city" value="${data.city || ''}"></div>
                        <div><label>Kod pocztowy</label><input id="company-postal_code" name="postal_code" value="${data.postal_code || ''}"></div>
                        <div><label>Kraj</label><input name="country" value="${data.country || 'Polska'}"></div>
                        <div><label>Źródło</label><input name="source" value="${data.source || ''}"></div>
                        <div><label>Typ *</label>
                            <select name="type" required>
                                <option value="potencjalny" ${data.type === 'potencjalny' ? 'selected' : ''}>Potencjalny</option>
                                <option value="klient" ${data.type === 'klient' ? 'selected' : ''}>Klient</option>
                                <option value="partner" ${data.type === 'partner' ? 'selected' : ''}>Partner</option>
                                <option value="konkurencja" ${data.type === 'konkurencja' ? 'selected' : ''}>Konkurencja</option>
                            </select>
                        </div>
                        <div><label>Status *</label>
                            <select name="status" required>
                                <option value="aktywny" ${data.status === 'aktywny' ? 'selected' : ''}>Aktywny</option>
                                <option value="nieaktywny" ${data.status === 'nieaktywny' ? 'selected' : ''}>Nieaktywny</option>
                                <option value="zawieszony" ${data.status === 'zawieszony' ? 'selected' : ''}>Zawieszony</option>
                            </select>
                        </div>
                        <div><label>Opiekun</label>
                            <select name="owner_id">
                                <option value="">Brak</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" ${String(data.owner_id ?? '') === '{{ $user->id }}' ? 'selected' : ''}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label>Typ klienta CRM</label>
                            <select name="customer_type_id">
                                <option value="">Brak</option>
                                @foreach($customerTypes as $type)
                                    <option value="{{ $type->id }}" ${String(data.customer_type_id ?? '') === '{{ $type->id }}' ? 'selected' : ''}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column:1 / -1;"><label>Adres</label><textarea id="company-address" name="address" rows="2">${data.address || ''}</textarea></div>
                        <div style="grid-column:1 / -1;"><label>Notatki</label><textarea name="notes" rows="3">${data.notes || ''}</textarea></div>
                        ${method === 'POST' ? `
                            <div style="grid-column:1 / -1; border:1px solid #d7e5f0; border-radius:10px; background:#f8fbff; padding:8px 10px;">
                                <label style="display:flex; align-items:center; gap:8px; margin:0; font-weight:700;">
                                    <input type="checkbox" name="add_to_system" value="1">
                                    Dodaj tę firmę również do systemu (Ustawienia → Firmy)
                                </label>
                            </div>
                        ` : ''}
                    </div>
                    <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Anuluj</button>
                        <button type="submit">Zapisz</button>
                    </div>
                </form>
            `;
        }

        function showCompanyModal() {
            openModal(companyFormHtml('{{ route('crm.company.add') }}', 'POST', {}));
        }

        function showClientModal() {
            openModal(companyFormHtml('{{ route('crm.company.add') }}', 'POST', {
                type: 'klient',
                status: 'aktywny'
            }));
        }

        async function editCompany(id) {
            const res = await fetch(`/crm/company/${id}/edit`);
            const data = await res.json();
            openModal(companyFormHtml(`/crm/company/${id}`, 'PUT', data));
        }

        function dealFormHtml(action, method, data = {}) {
            const assignedUsers = Array.isArray(data.assigned_users) ? data.assigned_users.map((user) => user.id) : [];
            return `
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin:0 0 10px;">
                    <div>
                        <h3 style="margin:0;">${method === 'PUT' ? 'Edytuj Szansę' : 'Dodaj Szansę Sprzedażową'}</h3>
                        <div class="muted" style="font-size:12px; margin-top:2px;">Uzupełnij kluczowe dane sprzedażowe i przypisania zespołu</div>
                    </div>
                    <span style="padding:4px 8px; border-radius:999px; background:#eef5fb; border:1px solid #d7e5f0; font-size:12px; font-weight:700; color:#28485f;">${method === 'PUT' ? 'TRYB EDYCJI' : 'NOWA SZANSA'}</span>
                </div>
                <form method="POST" action="${action}">
                    @csrf
                    ${method === 'PUT' ? '<input type="hidden" name="_method" value="PUT">' : ''}
                    <div class="crm-form-grid">
                        <div style="grid-column:1 / -1; border:1px solid #d7e5f0; border-radius:10px; padding:10px; background:#f8fbff;">
                            <div style="font-weight:700; margin-bottom:6px;">Podstawowe informacje</div>
                            <div class="crm-form-grid">
                                <div style="grid-column:1 / -1;"><label>Nazwa *</label><input name="name" value="${data.name || ''}" required placeholder="Np. Audyt energetyczny - Firma X"></div>
                                <div><label>Firma</label>
                                    <select name="company_id">
                                        <option value="">Brak</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" ${String(data.company_id ?? '') === '{{ $company->id }}' ? 'selected' : ''}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label>Wartość (PLN) *</label><input name="value" type="number" step="0.01" min="0" value="${data.value || ''}" required></div>
                            </div>
                        </div>

                        <div style="grid-column:1 / -1; border:1px solid #d7e5f0; border-radius:10px; padding:10px; background:#fff;">
                            <div style="font-weight:700; margin-bottom:6px;">Kwalifikacja i terminy</div>
                            <div class="crm-form-grid">
                                <div><label>Etap *</label>
                                    <select name="stage" required>
                                        @foreach($crmStages->sortBy('order') as $stage)
                                            <option value="{{ $stage->slug }}" ${data.stage === '{{ $stage->slug }}' ? 'selected' : ''}>{{ $stage->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><label>Prawdopodobieństwo (%) *</label><input name="probability" type="number" min="0" max="100" value="${data.probability ?? 10}" required></div>
                                <div><label>Oczekiwane zamknięcie</label><input type="date" name="expected_close_date" value="${data.expected_close_date ? String(data.expected_close_date).substring(0, 10) : ''}"></div>
                                <div><label>Rzeczywiste zamknięcie</label><input type="date" name="actual_close_date" value="${data.actual_close_date ? String(data.actual_close_date).substring(0, 10) : ''}"></div>
                            </div>
                        </div>

                        <div style="grid-column:1 / -1; border:1px solid #d7e5f0; border-radius:10px; padding:10px; background:#fff;">
                            <div style="font-weight:700; margin-bottom:6px;">Zespół i dodatkowe informacje</div>
                            <div class="crm-form-grid">
                                <div><label>Opiekun</label>
                                    <select name="owner_id">
                                        <option value="">Brak</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" ${String(data.owner_id ?? '') === '{{ $user->id }}' ? 'selected' : ''}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="grid-column:1 / -1;"><label>Przypisani użytkownicy</label>
                                    <div style="border:1px solid #d7e5f0; border-radius:10px; padding:8px; max-height:120px; overflow:auto; background:#f9fbfd;">
                                        @foreach($users as $user)
                                            <label style="display:block; padding:2px 0;"><input type="checkbox" name="assigned_users[]" value="{{ $user->id }}" ${assignedUsers.includes({{ $user->id }}) ? 'checked' : ''}> {{ $user->name }}</label>
                                        @endforeach
                                    </div>
                                </div>
                                <div style="grid-column:1 / -1;"><label>Opis</label><textarea name="description" rows="3" placeholder="Kontekst szansy, zakres prac, potrzeby klienta">${data.description || ''}</textarea></div>
                                <div style="grid-column:1 / -1;"><label>Powód przegranej</label><textarea name="lost_reason" rows="2" placeholder="Uzupełnij tylko dla przegranych szans">${data.lost_reason || ''}</textarea></div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Anuluj</button>
                        <button type="submit">Zapisz</button>
                    </div>
                </form>
            `;
        }

        function showDealModal() {
            openModal(dealFormHtml('{{ route('crm.deal.add') }}', 'POST', {}));
        }

        async function editDeal(id) {
            const res = await fetch(`/crm/deal/${id}/edit`);
            const data = await res.json();
            openModal(dealFormHtml(`/crm/deal/${id}`, 'PUT', data));
        }

        async function showDealPreview(id) {
            const res = await fetch(`/crm/deal/${id}/edit`);
            const deal = await res.json();

            const assignedUsers = Array.isArray(deal.assigned_users) && deal.assigned_users.length > 0
                ? deal.assigned_users.map((user) => user.short_name || user.name).join(', ')
                : 'Brak przypisanych';

            const activities = Array.isArray(deal.activities) ? deal.activities : [];
            const tasks = Array.isArray(deal.tasks) ? deal.tasks : [];

            openModal(`
                <h3 style="margin:0 0 10px;">Podgląd szansy</h3>
                <div style="display:grid; grid-template-columns:repeat(2, minmax(220px,1fr)); gap:10px;">
                    <div><strong>Nazwa:</strong><br>${deal.name || '-'}</div>
                    <div><strong>Firma:</strong><br>${deal.company?.name || 'Brak firmy'}</div>
                    <div><strong>Etap:</strong><br>${deal.stage || '-'}</div>
                    <div><strong>Wartość:</strong><br>${Number(deal.value || 0).toLocaleString('pl-PL', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${deal.currency || ''}</div>
                    <div><strong>Prawdopodobieństwo:</strong><br>${deal.probability ?? '-'}%</div>
                    <div><strong>Oczek. zamknięcie:</strong><br>${deal.expected_close_date ? String(deal.expected_close_date).substring(0,10) : '-'}</div>
                    <div style="grid-column:1 / -1;"><strong>Przypisani:</strong><br>${assignedUsers}</div>
                    <div style="grid-column:1 / -1;"><strong>Opis:</strong><br>${deal.description || '—'}</div>
                    <div style="grid-column:1 / -1;"><strong>Powód przegranej:</strong><br>${deal.lost_reason || '—'}</div>
                </div>
                <div style="margin-top:12px;">
                    <strong>Powiązane zadania (${tasks.length})</strong>
                    <div class="muted" style="font-size:12px; margin-top:4px;">${tasks.length ? tasks.map((task) => task.title).join(', ') : 'Brak'}</div>
                </div>
                <div style="margin-top:8px;">
                    <strong>Powiązane aktywności (${activities.length})</strong>
                    <div class="muted" style="font-size:12px; margin-top:4px;">${activities.length ? activities.map((activity) => activity.subject).join(', ') : 'Brak'}</div>
                </div>
                <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Zamknij</button>
                    <button type="button" onclick="editDeal(${id})">Edytuj</button>
                </div>
            `);
        }

        function taskFormHtml(action, method, data = {}) {
            return `
                <h3 style="margin:0 0 10px;">${method === 'PUT' ? 'Edytuj Zadanie' : 'Dodaj Zadanie'}</h3>
                <form method="POST" action="${action}">
                    @csrf
                    ${method === 'PUT' ? '<input type="hidden" name="_method" value="PUT">' : ''}
                    <div style="display:grid; grid-template-columns:repeat(2, minmax(220px,1fr)); gap:10px;">
                        <div style="grid-column:1 / -1;"><label>Tytuł *</label><input name="title" value="${data.title || ''}" required></div>
                        <div><label>Typ *</label>
                            <select name="type" required>
                                <option value="zadanie" ${data.type === 'zadanie' ? 'selected' : ''}>Zadanie</option>
                                <option value="telefon" ${data.type === 'telefon' ? 'selected' : ''}>Telefon</option>
                                <option value="email" ${data.type === 'email' ? 'selected' : ''}>Email</option>
                                <option value="spotkanie" ${data.type === 'spotkanie' ? 'selected' : ''}>Spotkanie</option>
                                <option value="follow_up" ${data.type === 'follow_up' ? 'selected' : ''}>Follow-up</option>
                            </select>
                        </div>
                        <div><label>Priorytet *</label>
                            <select name="priority" required>
                                <option value="niska" ${data.priority === 'niska' ? 'selected' : ''}>Niska</option>
                                <option value="normalna" ${data.priority === 'normalna' ? 'selected' : ''}>Normalna</option>
                                <option value="wysoka" ${data.priority === 'wysoka' ? 'selected' : ''}>Wysoka</option>
                                <option value="pilna" ${data.priority === 'pilna' ? 'selected' : ''}>Pilna</option>
                            </select>
                        </div>
                        <div><label>Status *</label>
                            <select name="status" required>
                                <option value="do_zrobienia" ${data.status === 'do_zrobienia' ? 'selected' : ''}>Do zrobienia</option>
                                <option value="w_trakcie" ${data.status === 'w_trakcie' ? 'selected' : ''}>W trakcie</option>
                                <option value="zakonczone" ${data.status === 'zakonczone' ? 'selected' : ''}>Zakończone</option>
                                <option value="anulowane" ${data.status === 'anulowane' ? 'selected' : ''}>Anulowane</option>
                            </select>
                        </div>
                        <div><label>Termin</label><input type="datetime-local" name="due_date" value="${data.due_date ? String(data.due_date).substring(0, 16) : ''}"></div>
                        <div><label>Przypisz do</label>
                            <select name="assigned_to">
                                <option value="">Nie przypisane</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" ${String(data.assigned_to ?? '') === '{{ $user->id }}' ? 'selected' : ''}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label>Firma</label>
                            <select name="company_id">
                                <option value="">Brak</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" ${String(data.company_id ?? '') === '{{ $company->id }}' ? 'selected' : ''}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label>Szansa</label>
                            <select name="deal_id">
                                <option value="">Brak</option>
                                @foreach($deals as $deal)
                                    <option value="{{ $deal->id }}" ${String(data.deal_id ?? '') === '{{ $deal->id }}' ? 'selected' : ''}>{{ $deal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column:1 / -1;"><label>Opis</label><textarea name="description" rows="3">${data.description || ''}</textarea></div>
                    </div>
                    <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Anuluj</button>
                        <button type="submit">Zapisz</button>
                    </div>
                </form>
            `;
        }

        function showTaskModal() {
            openModal(taskFormHtml('{{ route('crm.task.add') }}', 'POST', {}));
        }

        async function editTask(id) {
            const res = await fetch(`/crm/task/${id}/edit`);
            const data = await res.json();
            openModal(taskFormHtml(`/crm/task/${id}`, 'PUT', data));
        }

        function syncActivityCompanyFromDeal(select) {
            const selected = select?.options?.[select.selectedIndex];
            const companyId = selected?.getAttribute('data-company-id');
            const companySelect = document.querySelector('#modal-content select[name="company_id"]');
            if (companyId && companySelect) companySelect.value = companyId;
        }

        function activityFormHtml(action, method, data = {}) {
            return `
                <h3 style="margin:0 0 10px;">${method === 'PUT' ? 'Edytuj Aktywność' : 'Dodaj Aktywność'}</h3>
                <form method="POST" action="${action}">
                    @csrf
                    ${method === 'PUT' ? '<input type="hidden" name="_method" value="PUT">' : ''}
                    <div style="display:grid; grid-template-columns:repeat(2, minmax(220px,1fr)); gap:10px;">
                        <div><label>Typ *</label>
                            <select name="type" required>
                                <option value="telefon" ${data.type === 'telefon' ? 'selected' : ''}>Telefon</option>
                                <option value="email" ${data.type === 'email' ? 'selected' : ''}>Email</option>
                                <option value="spotkanie" ${data.type === 'spotkanie' ? 'selected' : ''}>Spotkanie</option>
                                <option value="notatka" ${data.type === 'notatka' ? 'selected' : ''}>Notatka</option>
                                <option value="sms" ${data.type === 'sms' ? 'selected' : ''}>SMS</option>
                                <option value="oferta" ${data.type === 'oferta' ? 'selected' : ''}>Oferta</option>
                                <option value="umowa" ${data.type === 'umowa' ? 'selected' : ''}>Umowa</option>
                                <option value="faktura" ${data.type === 'faktura' ? 'selected' : ''}>Faktura</option>
                                <option value="reklamacja" ${data.type === 'reklamacja' ? 'selected' : ''}>Reklamacja</option>
                            </select>
                        </div>
                        <div><label>Data *</label><input type="datetime-local" name="activity_date" value="${data.activity_date ? String(data.activity_date).substring(0, 16) : ''}" required></div>
                        <div style="grid-column:1 / -1;"><label>Temat *</label><input name="subject" value="${data.subject || ''}" required></div>
                        <div><label>Czas trwania (min)</label><input type="number" name="duration" value="${data.duration || ''}"></div>
                        <div><label>Wynik</label>
                            <select name="outcome">
                                <option value="">Brak</option>
                                <option value="pozytywny" ${data.outcome === 'pozytywny' ? 'selected' : ''}>Pozytywny</option>
                                <option value="neutralny" ${data.outcome === 'neutralny' ? 'selected' : ''}>Neutralny</option>
                                <option value="negatywny" ${data.outcome === 'negatywny' ? 'selected' : ''}>Negatywny</option>
                                <option value="brak_odpowiedzi" ${data.outcome === 'brak_odpowiedzi' ? 'selected' : ''}>Brak odpowiedzi</option>
                            </select>
                        </div>
                        <div><label>Firma</label>
                            <select name="company_id">
                                <option value="">Brak</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" ${String(data.company_id ?? '') === '{{ $company->id }}' ? 'selected' : ''}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label>Szansa</label>
                            <select name="deal_id" onchange="syncActivityCompanyFromDeal(this)">
                                <option value="">Brak</option>
                                @foreach($deals as $deal)
                                    <option value="{{ $deal->id }}" data-company-id="{{ $deal->company_id }}" ${String(data.deal_id ?? '') === '{{ $deal->id }}' ? 'selected' : ''}>{{ $deal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="grid-column:1 / -1;"><label>Opis</label><textarea name="description" rows="3">${data.description || ''}</textarea></div>
                    </div>
                    <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Anuluj</button>
                        <button type="submit">Zapisz</button>
                    </div>
                </form>
            `;
        }

        function showActivityModal() {
            openModal(activityFormHtml('{{ route('crm.activity.add') }}', 'POST', {}));
        }

        async function editActivity(id) {
            const res = await fetch(`/crm/activity/${id}/edit`);
            const data = await res.json();
            openModal(activityFormHtml(`/crm/activity/${id}`, 'PUT', data));
        }

        function saveDashboardSectionOrder() {
            const container = document.getElementById('dashboard-sections');
            if (!container) return;
            const order = Array.from(container.querySelectorAll('.dashboard-section')).map((section) => section.dataset.sectionId);
            localStorage.setItem('crm-dashboard-sections-order', JSON.stringify(order));
        }

        function applyDashboardSectionOrder() {
            const container = document.getElementById('dashboard-sections');
            if (!container) return;
            const raw = localStorage.getItem('crm-dashboard-sections-order');
            if (!raw) return;

            let order = [];
            try {
                order = JSON.parse(raw);
            } catch (error) {
                return;
            }

            if (!Array.isArray(order) || !order.length) return;

            const sectionMap = new Map(
                Array.from(container.querySelectorAll('.dashboard-section')).map((section) => [section.dataset.sectionId, section])
            );

            order.forEach((id) => {
                const section = sectionMap.get(id);
                if (section) container.appendChild(section);
            });
        }

        function initDashboardSectionDragAndDrop() {
            const container = document.getElementById('dashboard-sections');
            if (!container) return;

            applyDashboardSectionOrder();

            let draggingSection = null;

            container.querySelectorAll('.dashboard-section').forEach((section) => {
                section.addEventListener('dragstart', () => {
                    draggingSection = section;
                    section.classList.add('dragging');
                });

                section.addEventListener('dragend', () => {
                    section.classList.remove('dragging');
                    container.querySelectorAll('.dashboard-section').forEach((item) => item.classList.remove('drag-over'));
                    saveDashboardSectionOrder();
                    draggingSection = null;
                });

                section.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    if (!draggingSection || draggingSection === section) return;
                    section.classList.add('drag-over');
                });

                section.addEventListener('dragleave', () => {
                    section.classList.remove('drag-over');
                });

                section.addEventListener('drop', (event) => {
                    event.preventDefault();
                    section.classList.remove('drag-over');
                    if (!draggingSection || draggingSection === section) return;

                    const sections = Array.from(container.querySelectorAll('.dashboard-section'));
                    const targetIndex = sections.indexOf(section);
                    const draggingIndex = sections.indexOf(draggingSection);

                    if (targetIndex < draggingIndex) {
                        container.insertBefore(draggingSection, section);
                    } else {
                        container.insertBefore(draggingSection, section.nextSibling);
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initDashboardSectionDragAndDrop();
        });
    </script>
</x-layouts.app>
