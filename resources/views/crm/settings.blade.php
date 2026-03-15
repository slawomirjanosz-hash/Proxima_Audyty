<x-layouts.app>
    <section class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
            <h1 style="margin:0;">⚙️ Ustawienia CRM</h1>
            <a href="{{ route('crm.index') }}" class="login-btn" style="text-decoration:none;">Powrót do CRM</a>
        </div>

        <h2 style="margin:0 0 8px;">Typy Klientów</h2>
        <table style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Nazwa typu</th>
                    <th>Slug</th>
                    <th>Kolor</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customerTypes as $type)
                    <tr>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->slug }}</td>
                        <td><span style="display:inline-block; padding:4px 10px; border-radius:8px; color:#fff; background:{{ $type->color }};">{{ $type->color }}</span></td>
                        <td>
                            <button onclick="editCustomerType({{ $type->id }})" class="btn-secondary" type="button">✏️ Edytuj</button>
                            <form action="{{ route('crm.customer-types.destroy', $type->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć typ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-secondary">🗑️ Usuń</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" onclick="showAddCustomerTypeModal()">➕ Dodaj Nowy Typ</button>

        <h2 style="margin:18px 0 8px;">Etapy Szans Sprzedażowych</h2>
        <table style="margin-bottom:12px;">
            <thead>
                <tr>
                    <th>Kolejność</th>
                    <th>Nazwa etapu</th>
                    <th>Slug</th>
                    <th>Kolor</th>
                    <th>Aktywny</th>
                    <th>Zakończenie lejka</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($crmStages as $stage)
                    <tr>
                        <td>{{ $stage->order }}</td>
                        <td>{{ $stage->name }}</td>
                        <td>{{ $stage->slug }}</td>
                        <td><span style="display:inline-block; padding:4px 10px; border-radius:8px; color:#fff; background:{{ $stage->color }};">{{ $stage->color }}</span></td>
                        <td>{{ $stage->is_active ? '✓ Tak' : '✗ Nie' }}</td>
                        <td>{{ $stage->is_closed ? '✓ Tak' : '✗ Nie' }}</td>
                        <td>
                            <button type="button" class="btn-secondary" onclick="editStage({{ $stage->id }})">✏️ Edytuj</button>
                            @if(!in_array($stage->slug, ['wygrana','przegrana'], true))
                                <form action="{{ route('crm.stage.delete', $stage->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć etap?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary">🗑️ Usuń</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" onclick="showAddStageModal()">➕ Dodaj Nowy Etap</button>

        <h2 style="margin:18px 0 8px;">📚 Historia zmian CRM</h2>
        <table style="margin-bottom:12px;">
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
                            @elseif(isset($details['changes']) && is_array($details['changes']))
                                Zmienione pola: {{ implode(', ', array_keys($details['changes'])) }}
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
    </section>

    <div id="customer-type-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; align-items:center; justify-content:center;">
        <div style="background:#fff; width:min(520px, 92vw); border-radius:14px; padding:16px; border:1px solid #d5e0ea;">
            <h3 id="customer-type-modal-title" style="margin:0 0 10px;">Dodaj Typ Klienta</h3>
            <form id="customer-type-form" method="POST" action="{{ route('crm.customer-types.store') }}">
                @csrf
                <input type="hidden" id="customer-type-method" name="_method" value="">
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Nazwa typu *</label>
                <input type="text" name="name" id="customer-type-name" required>
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Slug *</label>
                <input type="text" name="slug" id="customer-type-slug" required>
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Kolor (hex) *</label>
                <input type="color" name="color" id="customer-type-color" required style="height:40px; padding:4px;">
                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
                    <button type="button" class="btn-secondary" onclick="closeCustomerTypeModal()">Anuluj</button>
                    <button type="submit">Zapisz</button>
                </div>
            </form>
        </div>
    </div>

    <div id="stage-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; align-items:center; justify-content:center;">
        <div style="background:#fff; width:min(560px, 92vw); border-radius:14px; padding:16px; border:1px solid #d5e0ea;">
            <h3 id="stage-modal-title" style="margin:0 0 10px;">Dodaj Etap</h3>
            <form id="stage-form" method="POST" action="{{ route('crm.stage.add') }}">
                @csrf
                <input type="hidden" id="stage-method" name="_method" value="">

                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Nazwa etapu *</label>
                <input type="text" name="name" id="stage-name" required>
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Slug *</label>
                <input type="text" name="slug" id="stage-slug" required>
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Kolor (hex) *</label>
                <input type="color" name="color" id="stage-color" required style="height:40px; padding:4px;">
                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Kolejność *</label>
                <input type="number" name="order" id="stage-order" min="0" required>

                <label style="display:block; margin:8px 0 4px;"><input type="checkbox" name="is_active" id="stage-is-active" value="1"> Aktywny</label>
                <label style="display:block; margin:8px 0 4px;"><input type="checkbox" name="is_closed" id="stage-is-closed" value="1"> Zakończenie Lejka</label>

                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
                    <button type="button" class="btn-secondary" onclick="closeStageModal()">Anuluj</button>
                    <button type="submit">Zapisz</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddCustomerTypeModal() {
            document.getElementById('customer-type-modal-title').textContent = 'Dodaj Typ Klienta';
            document.getElementById('customer-type-form').action = '{{ route("crm.customer-types.store") }}';
            document.getElementById('customer-type-method').value = '';
            document.getElementById('customer-type-name').value = '';
            document.getElementById('customer-type-slug').value = '';
            document.getElementById('customer-type-slug').readOnly = false;
            document.getElementById('customer-type-color').value = '#3b82f6';
            document.getElementById('customer-type-modal').style.display = 'flex';
        }

        function editCustomerType(id) {
            fetch(`/crm/customer-types/${id}`)
                .then(res => res.json())
                .then(type => {
                    document.getElementById('customer-type-modal-title').textContent = 'Edytuj Typ Klienta';
                    document.getElementById('customer-type-form').action = `/crm/customer-types/${id}`;
                    document.getElementById('customer-type-method').value = 'PUT';
                    document.getElementById('customer-type-name').value = type.name;
                    document.getElementById('customer-type-slug').value = type.slug;
                    document.getElementById('customer-type-slug').readOnly = true;
                    document.getElementById('customer-type-color').value = type.color;
                    document.getElementById('customer-type-modal').style.display = 'flex';
                });
        }

        function closeCustomerTypeModal() {
            document.getElementById('customer-type-modal').style.display = 'none';
        }

        function showAddStageModal() {
            document.getElementById('stage-modal-title').textContent = 'Dodaj Nowy Etap';
            document.getElementById('stage-form').action = '{{ route("crm.stage.add") }}';
            document.getElementById('stage-method').value = '';
            document.getElementById('stage-name').value = '';
            document.getElementById('stage-slug').value = '';
            document.getElementById('stage-slug').readOnly = false;
            document.getElementById('stage-color').value = '#3b82f6';
            document.getElementById('stage-order').value = {{ ($crmStages->max('order') ?? 0) + 1 }};
            document.getElementById('stage-is-active').checked = true;
            document.getElementById('stage-is-closed').checked = false;
            document.getElementById('stage-modal').style.display = 'flex';
        }

        function editStage(id) {
            fetch(`/crm/stage/${id}/edit`)
                .then(res => res.json())
                .then(stage => {
                    document.getElementById('stage-modal-title').textContent = 'Edytuj Etap';
                    document.getElementById('stage-form').action = `/crm/stage/${id}`;
                    document.getElementById('stage-method').value = 'PUT';
                    document.getElementById('stage-name').value = stage.name;
                    document.getElementById('stage-slug').value = stage.slug;
                    document.getElementById('stage-slug').readOnly = true;
                    document.getElementById('stage-color').value = stage.color;
                    document.getElementById('stage-order').value = stage.order;
                    document.getElementById('stage-is-active').checked = !!stage.is_active;
                    document.getElementById('stage-is-closed').checked = !!stage.is_closed;
                    document.getElementById('stage-modal').style.display = 'flex';
                });
        }

        function closeStageModal() {
            document.getElementById('stage-modal').style.display = 'none';
        }
    </script>
</x-layouts.app>
