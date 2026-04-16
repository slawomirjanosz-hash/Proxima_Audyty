<x-layouts.app>

    <style>
        .client-hero {
            background: linear-gradient(135deg, #0f2330 0%, #0e4a6e 50%, #0b6e3d 100%);
            border-radius: 16px;
            padding: 36px 40px;
            color: #fff;
            display: grid;
            gap: 10px;
            box-shadow: 0 18px 50px rgba(14,55,85,.18);
            position: relative;
            overflow: hidden;
        }
        .client-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .client-hero::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 30%;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(27,168,74,.1);
        }
        .client-tag {
            width: fit-content;
            background: rgba(27,168,74,.22);
            border: 1px solid rgba(27,168,74,.4);
            color: #6ee7a4;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 6px;
        }
        .client-hero h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 36px);
            font-weight: 800;
            line-height: 1.15;
        }
        .client-hero h1 span { color: #6ee7a4; }
        .client-hero p {
            margin: 0;
            font-size: 14px;
            color: rgba(255,255,255,.65);
            max-width: 560px;
        }
        .client-meta { display: flex; gap: 24px; margin-top: 8px; flex-wrap: wrap; }
        .client-meta-item { display: flex; flex-direction: column; gap: 2px; }
        .client-meta-item .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,.45); font-weight: 700; }
        .client-meta-item .value { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.9); }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 14px;
            margin-top: 14px;
        }
        .action-card {
            background: #fff;
            border: 1px solid #d5e0ea;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(14,55,85,.05);
        }
        .action-card-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: grid; place-items: center;
            font-size: 22px; margin-bottom: 12px;
        }
        .action-card-icon-blue  { background: rgba(14,137,216,.1); }
        .action-card-icon-green { background: rgba(27,168,74,.1); }
        .action-card h3 { margin: 0 0 6px; font-size: 16px; font-weight: 800; color: #0f2330; }
        .action-card p  { margin: 0 0 14px; font-size: 13px; color: #4c6373; }
        .action-card select, .action-card textarea { width: 100%; margin-bottom: 10px; }
        .action-card textarea { min-height: 80px; resize: vertical; }
        .btn-action {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 10px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            border: none; text-decoration: none;
        }
        .btn-action-blue  { background: #0e89d8; color: #fff; }
        .btn-action-blue:hover { background: #0d7cc4; }
        .btn-action-green { background: #1ba84a; color: #fff; }
        .btn-action-green:hover { background: #188c3e; }

        .inquiry-list { display: grid; gap: 10px; margin-top: 12px; }
        .inquiry-item {
            border: 1px solid #d5e0ea; border-radius: 12px;
            padding: 14px 16px; background: #fbfdff;
            display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap;
            justify-content: space-between;
        }
        .inquiry-body { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 200px; }
        .inquiry-type { font-weight: 800; font-size: 15px; color: #10344c; }
        .inquiry-msg  { font-size: 13px; color: #4c6373; white-space: pre-line; }
        .inquiry-date { font-size: 12px; color: #8aa3b5; margin-top: 4px; }
        .inquiry-badge {
            padding: 4px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
            white-space: nowrap;
        }
        .preview-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .preview-table th, .preview-table td { padding: 9px 10px; font-size: 13px; border-bottom: 1px solid #edf2f7; text-align: left; }
        .preview-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #4c6373; background: #f7fafc; }
        .preview-table tr:last-child td { border-bottom: none; }

        @media (max-width: 800px) {
            .client-hero { padding: 24px 20px; }
            .action-grid { grid-template-columns: 1fr; }
        }
    </style>

    {{-- Welcome hero --}}
    <div class="client-hero">
        <span class="client-tag">Strefa klienta</span>
        <h1>Witaj, <span>{{ auth()->user()->name }}</span></h1>
        <p>Twoja dedykowana przestrzeń ENESA — tutaj możesz wysłać zapytanie dotyczące audytu energetycznego lub skontaktować się z nami bezpośrednio.</p>
        <div class="client-meta">
            <div class="client-meta-item">
                <span class="label">Konto</span>
                <span class="value">{{ auth()->user()->email }}</span>
            </div>
            <div class="client-meta-item">
                <span class="label">Poziom dostępu</span>
                <span class="value">{{ auth()->user()->role->label() }}</span>
            </div>
            <div class="client-meta-item">
                <span class="label">Data sesji</span>
                <span class="value">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    @if ($previewMode)
        <section class="panel" style="margin-top:14px; background:#f2f8ff; border-color:#cfe0ff; color:#154f93;">
            <strong>Tryb podglądu:</strong> Przeglądasz Strefę klienta jako konto uprzywilejowane.
        </section>
    @endif

    @if (session('inquiry_status'))
        <div class="status" style="margin-top:12px;">{{ session('inquiry_status') }}</div>
    @endif

    @if (!$previewMode)
        {{-- Client actions --}}
        <div class="action-grid">

            {{-- Card 1: Send inquiry --}}
            <div class="action-card">
                <div class="action-card-icon action-card-icon-blue">📋</div>
                <h3>Wyślij zapytanie o audyt</h3>
                <p>Wybierz rodzaj audytu, który Cię interesuje, i wyślij zapytanie. Odpiszemy tak szybko jak to możliwe.</p>
                <form method="POST" action="{{ route('client.inquiry.store') }}">
                    @csrf
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px;">Rodzaj audytu *</label>
                        <select name="audit_type_id" required>
                            <option value="">— Wybierz rodzaj audytu —</option>
                            @foreach ($auditTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('audit_type_id') == $type->id)>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-top:8px;">
                        <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px;">Wiadomość (opcjonalnie)</label>
                        <textarea name="message" placeholder="Opisz swoje potrzeby lub zadaj pytanie...">{{ old('message') }}</textarea>
                    </div>
                    @error('audit_type_id')
                        <div style="color:#b91c1c; font-size:12px; margin-bottom:6px;">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn-action btn-action-blue">
                        <span>📤</span> Wyślij zapytanie
                    </button>
                </form>
            </div>

            {{-- Card 2: Contact via email --}}
            @if (!empty($contactEmail))
                <div class="action-card">
                    <div class="action-card-icon action-card-icon-green">✉️</div>
                    <h3>Napisz do nas</h3>
                    <p>Masz pytanie poza standardowym zapytaniem? Napisz do nas bezpośrednio na adres e-mail.</p>
                    <a href="mailto:{{ $contactEmail }}" class="btn-action btn-action-green">
                        <span>📧</span> {{ $contactEmail }}
                    </a>
                </div>
            @else
                <div class="action-card" style="border-color:#e8f3ff; background:#f7fbff;">
                    <div class="action-card-icon action-card-icon-green">✉️</div>
                    <h3>Napisz do nas</h3>
                    <p>Skontaktuj się z nami bezpośrednio. Dane kontaktowe zostaną wkrótce udostępnione przez administratora.</p>
                </div>
            @endif

        </div>

        {{-- Client's inquiries --}}
        <section class="panel" style="margin-top:14px;">
            <h2 style="margin:0 0 6px; font-size:17px; font-weight:800; color:#0f2330;">Moje zapytania</h2>
            <p class="muted" style="margin:0 0 10px; font-size:13px;">Lista Twoich wysłanych zapytań o audyt wraz z aktualnym statusem.</p>

            @if ($inquiries->isEmpty())
                <div style="padding:20px; text-align:center; color:#9ab4c5; border:1px dashed #d5e0ea; border-radius:12px; font-size:14px;">
                    Nie wysłałeś jeszcze żadnego zapytania. Użyj formularza powyżej.
                </div>
            @else
                <div class="inquiry-list">
                    @foreach ($inquiries as $inquiry)
                        <div class="inquiry-item">
                            <div class="inquiry-body">
                                <span class="inquiry-type">{{ $inquiry->audit_type_name ?? $inquiry->auditType?->name ?? '—' }}</span>
                                @if ($inquiry->message)
                                    <span class="inquiry-msg">{{ $inquiry->message }}</span>
                                @endif
                                <span class="inquiry-date">Wysłano: {{ $inquiry->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <span class="inquiry-badge" style="background:{{ $inquiry->statusBg() }}; color:{{ $inquiry->statusColor() }}; border:1px solid {{ $inquiry->statusBg() }};">
                                {{ $inquiry->statusLabel() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    @else
        {{-- Admin/auditor preview: show all inquiries --}}
        <section class="panel" style="margin-top:14px;">
            <h2 style="margin:0 0 6px; font-size:17px; font-weight:800; color:#0f2330;">Wszystkie zapytania klientów</h2>
            <p class="muted" style="margin:0 0 10px; font-size:13px;">Lista wszystkich zapytań nadesłanych przez klientów z Strefy klienta.</p>

            @if ($inquiries->isEmpty())
                <div style="padding:20px; text-align:center; color:#9ab4c5; font-size:14px;">Brak zapytań.</div>
            @else
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Klient</th>
                            <th>Firma</th>
                            <th>Rodzaj audytu</th>
                            <th>Wiadomość</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->user?->name ?? '—' }}</td>
                                <td>{{ $inquiry->company?->name ?? '—' }}</td>
                                <td>{{ $inquiry->audit_type_name ?? '—' }}</td>
                                <td>{{ $inquiry->message ? \Illuminate\Support\Str::limit($inquiry->message, 60) : '—' }}</td>
                                <td style="white-space:nowrap;">{{ $inquiry->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <span class="inquiry-badge" style="background:{{ $inquiry->statusBg() }}; color:{{ $inquiry->statusColor() }};">
                                        {{ $inquiry->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endif

</x-layouts.app>

