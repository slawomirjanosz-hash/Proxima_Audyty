<x-layouts.app>

    <style>
        .iso-header {
            background: linear-gradient(135deg, #163347 0%, #0e89d8 55%, #1ba84a 100%);
            color: #fff;
            border-radius: 16px;
            padding: 26px;
            display: grid;
            gap: 8px;
            box-shadow: 0 12px 34px rgba(14, 55, 85, .2);
        }
        .iso-header h2 { margin: 0; font-size: 30px; }
        .iso-header p { margin: 0; color: rgba(255, 255, 255, .88); max-width: 760px; }
        .iso-grid {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            border: 1px solid #d5e0ea;
            background: #f6fafc;
            color: #36536a;
        }
        .status-submitted { background: #e7f4ff; border-color: #bfdfff; color: #195d93; }
        .status-approved { background: #def7e8; border-color: #b7edcb; color: #1f6d3d; }
        .status-changes_required { background: #fff3dd; border-color: #ffdca0; color: #8a5a00; }
        .status-in_review { background: #f1edff; border-color: #d6cbff; color: #513797; }
        .status-draft, .status-in_progress { background: #f2f7fb; }
        .filters {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>

    <section class="iso-header">
        <h2>ISO 50001</h2>
        <p>
            @if($isClient)
                Przeprowadź audyt krok po kroku i wyślij gotowy formularz do weryfikacji audytora.
            @else
                Kontroluj audyty klientów, nadawaj statusy i przekazuj zalecenia po weryfikacji.
            @endif
        </p>
    </section>

    <div class="iso-grid">
        @if($isClient)
            <section class="panel">
                <h3 style="margin-top:0;">Nowy audyt ISO 50001</h3>
                @if($companies->isEmpty())
                    <p class="muted">Nie masz przypisanej firmy. Skontaktuj się z administratorem, aby rozpocząć audyt.</p>
                @else
                    <form method="POST" action="{{ route('iso50001.store') }}" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr auto; align-items:end;">
                        @csrf
                        <div>
                            <label for="iso-company" style="display:block; margin-bottom:6px; font-weight:700;">Firma</label>
                            <select id="iso-company" name="company_id" required>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" @selected((int) old('company_id') === (int) $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="iso-title" style="display:block; margin-bottom:6px; font-weight:700;">Nazwa audytu</label>
                            <input id="iso-title" type="text" name="title" value="{{ old('title', 'Audyt ISO 50001 - '.now()->format('Y-m-d')) }}" required>
                        </div>
                        <button type="submit">Rozpocznij audyt</button>
                    </form>
                @endif
            </section>
        @else
            <section class="panel">
                <h3 style="margin-top:0;">Utwórz audyt dla klienta</h3>
                <form method="POST" action="{{ route('iso50001.store') }}" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr 1fr auto; align-items:end; margin-bottom:14px;">
                    @csrf
                    <div>
                        <label for="iso-client" style="display:block; margin-bottom:6px; font-weight:700;">Klient</label>
                        <select id="iso-client" name="client_user_id" required>
                            <option value="">Wybierz klienta</option>
                            @foreach(($clients ?? collect()) as $client)
                                <option value="{{ $client->id }}" @selected((int) old('client_user_id') === (int) $client->id)>{{ $client->name }} ({{ $client->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="iso-company" style="display:block; margin-bottom:6px; font-weight:700;">Firma</label>
                        <select id="iso-company" name="company_id" required>
                            <option value="">Wybierz firmę</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected((int) old('company_id') === (int) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="iso-title" style="display:block; margin-bottom:6px; font-weight:700;">Nazwa audytu</label>
                        <input id="iso-title" type="text" name="title" value="{{ old('title', 'Audyt ISO 50001 - '.now()->format('Y-m-d')) }}" required>
                    </div>
                    <button type="submit">Utwórz</button>
                </form>

                <form method="GET" action="{{ route('iso50001.index') }}" class="filters">
                    <strong>Filtr statusu:</strong>
                    <select name="status" onchange="this.form.submit()">
                        <option value="all" @selected($statusFilter === 'all')>Wszystkie</option>
                        @foreach($statusOptions as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" @selected($statusFilter === $statusKey)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </form>
            </section>
        @endif

        <section class="panel">
            <h3 style="margin-top:0;">
                @if($isClient)
                    Twoje audyty ISO 50001
                @else
                    Audyty ISO 50001 klientów
                @endif
            </h3>

            <table>
                <thead>
                <tr>
                    <th>Audyt</th>
                    <th>Firma</th>
                    @if(!$isClient)
                        <th>Klient</th>
                    @endif
                    <th>Status</th>
                    <th>Aktualizacja</th>
                    <th>Akcja</th>
                </tr>
                </thead>
                <tbody>
                @forelse($audits as $audit)
                    @php($statusClass = 'status-'.str_replace(' ', '_', $audit->status))
                    <tr>
                        <td>{{ $audit->title }}</td>
                        <td>{{ $audit->company?->name ?? '—' }}</td>
                        @if(!$isClient)
                            <td>{{ $audit->creator?->name ?? '—' }}</td>
                        @endif
                        <td>
                            <span class="status-pill {{ $statusClass }}">
                                {{ $statusOptions[$audit->status] ?? $audit->status }}
                            </span>
                        </td>
                        <td>{{ $audit->updated_at?->format('d.m.Y H:i') }}</td>
                        <td style="white-space:nowrap; display:flex; gap:6px;">
                            @if($isClient)
                                <a href="{{ route('iso50001.step', ['isoAudit' => $audit, 'step' => max(1, (int) $audit->current_step)]) }}" style="display:inline-block; background:#0e89d8; color:#fff; padding:6px 10px; border-radius:8px; text-decoration:none;">Kontynuuj</a>
                                <a href="{{ route('iso50001.review', $audit) }}" style="display:inline-block; background:#eff6fb; color:#174666; padding:6px 10px; border-radius:8px; text-decoration:none; border:1px solid #d3e4f1;">Podgląd</a>
                            @else
                                <a href="{{ route('iso50001.review', $audit) }}" style="display:inline-block; background:#0e89d8; color:#fff; padding:6px 10px; border-radius:8px; text-decoration:none;">Kontrola</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isClient ? '5' : '6' }}" class="muted" style="text-align:center;">Brak audytów ISO 50001.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>

</x-layouts.app>
