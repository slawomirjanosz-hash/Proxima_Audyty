<x-layouts.app>
    <section class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
            <h1 style="margin:0;">CRM • Typy Klientów</h1>
            <a href="{{ route('crm.index') }}" class="login-btn" style="text-decoration:none;">Powrót do CRM</a>
        </div>

        <p class="muted" style="margin:0 0 14px;">Widok skopiowany z modułu CRM ProximaLumine — zarządzanie typami klientów.</p>

        <table>
            <thead>
                <tr>
                    <th>Nazwa typu</th>
                    <th>Slug</th>
                    <th>Kolor</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customerTypes as $type)
                    <tr>
                        <td><strong>{{ $type->name }}</strong></td>
                        <td><span style="padding:2px 8px; border-radius:8px; background:#eef4fa;">{{ $type->slug }}</span></td>
                        <td>
                            <span style="display:inline-block; padding:4px 10px; border-radius:8px; color:#fff; background:{{ $type->color }};">{{ $type->color }}</span>
                        </td>
                        <td>
                            <button type="button" class="btn-secondary" onclick="editCustomerType({{ $type->id }})">Edytuj</button>
                            <form action="{{ route('crm.customer-types.destroy', $type->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Usunąć typ klienta?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Brak typów klientów CRM.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            <button type="button" onclick="showAddCustomerTypeModal()">Dodaj nowy typ</button>
        </div>
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
                <p class="muted" style="margin:4px 0 0; font-size:12px;">Np: klient, partner, konkurencja (bez spacji, małe litery)</p>

                <label style="display:block; margin:8px 0 4px; font-size:12px; font-weight:700; color:#4c6373;">Kolor (hex) *</label>
                <input type="color" name="color" id="customer-type-color" required style="height:40px; padding:4px;">

                <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:12px;">
                    <button type="button" class="btn-secondary" onclick="closeCustomerTypeModal()">Anuluj</button>
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
    </script>
</x-layouts.app>
