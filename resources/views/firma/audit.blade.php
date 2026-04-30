<x-layouts.app>
    <style>
        .audit-info-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:10px; margin-bottom:14px; }
        .audit-info-card { border:1px solid #dbe8f3; border-radius:12px; background:#f9fcff; padding:10px 14px; }
        .audit-info-card strong { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#6b8aa3; margin-bottom:4px; }
        .status-bar { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px; align-items:center; }
        .status-btn { padding:6px 12px; border-radius:8px; border:1.5px solid #d0dded; background:#f4f8fc; font-size:12px; font-weight:700; color:#4c6373; cursor:pointer; transition:all .15s; }
        .status-btn:hover { border-color:#0e89d8; color:#0e89d8; background:#eef6ff; }
        .status-btn.active { border-color:transparent; color:#fff; }
        .status-btn.active.s-wyslany { background:#2563eb; }
        .status-btn.active.s-rozpoczety { background:#059669; }
        .status-btn.active.s-do_analizy { background:#d97706; }
        .status-btn.active.s-zwrocony { background:#dc2626; }
        .status-btn.active.s-zaakceptowany { background:#16a34a; }
        .status-btn.active.s-zakonczony { background:#6b7280; }
        .status-btn.active.s-zafakturowany { background:#7c3aed; }
        .status-btn.active.s-zaplacony { background:#0d9488; }
        .audit-section { border:1px solid #d7e5f0; border-left:5px solid #7fb4e1; border-radius:12px; padding:12px; background:#f8fbff; margin-top:12px; }
        .audit-section h4 { margin:0 0 8px; font-size:16px; color:#10344c; }
        .audit-data-table { width:100%; border-collapse:collapse; margin-top:10px; }
        .audit-data-table th, .audit-data-table td { border:1px solid #e0ecf5; padding:8px; font-size:13px; text-align:left; }
        .audit-data-table th { background:#eef5fb; color:#2c4e67; font-weight:700; }
        .audit-formulas { margin-top:12px; border:1px solid #d7e5f0; border-radius:12px; padding:10px; background:#f7fbff; }
        .formula-line { display:flex; gap:8px; align-items:baseline; font-size:13px; color:#2c4e67; padding:6px 0; border-bottom:1px solid #e0ecf5; }
        .formula-line:last-child { border-bottom:none; }
        .formula-label { font-weight:700; }
        .btn-sm { padding:7px 14px; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; border:none; cursor:pointer; }
        .btn-primary-sm { background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; }
        .btn-secondary-sm { background:#dbe9f5; color:#1d4f73; }
        @media (max-width:900px) { .audit-info-grid { grid-template-columns:1fr; } }
    </style>

    <section class="panel">
        @if (session('status'))
            <div style="margin-bottom:12px; padding:10px 14px; background:#f0fdf4; border:1px solid #86efac; border-radius:10px; color:#166534; font-weight:600;">
                ✅ {{ session('status') }}
            </div>
        @endif

        {{-- Breadcrumb --}}
        <div style="margin-bottom:10px; font-size:13px; color:#4c6373;">
            @if($isClient ?? false)
                <a href="{{ route('strefa-klienta') }}" style="color:#0e89d8; text-decoration:none;">Strefa klienta</a>
            @else
                <a href="{{ route('dashboard') }}" style="color:#0e89d8; text-decoration:none;">Dashboard</a>
                &nbsp;›&nbsp;
                <a href="{{ route('firma.show', $company) }}" style="color:#0e89d8; text-decoration:none;">{{ $company->name }}</a>
            @endif
            &nbsp;›&nbsp;
            <span>{{ $audit->title }}</span>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap; margin-bottom:14px;">
            <div>
                <h1 style="margin:0;">{{ $audit->title }}</h1>
                <p class="muted" style="margin:4px 0 0; font-size:13px;">{{ $audit->auditType?->name ?: $audit->audit_type ?: '—' }} · {{ $company->name }}</p>
            </div>
            @if(!($isClient ?? false))
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <a href="{{ route('firma.report', [$company, $audit]) }}" target="_blank" class="btn-sm btn-primary-sm">🖨 Generuj raport</a>
                <form method="POST" action="{{ route('firma.destroyAudit', [$company, $audit]) }}" style="margin:0;"
                      onsubmit="return confirm('Usunąć audyt „{{ addslashes($audit->title) }}"? Tej operacji nie można cofnąć.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-sm" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;">🗑 Usuń audyt</button>
                </form>
            </div>
            @endif
        </div>

        {{-- Status info grid --}}
        <div class="audit-info-grid">
            <div class="audit-info-card">
                <strong>Aktualny status</strong>
                @php
                    $sk = str_replace(' ', '_', $audit->status);
                    $pillColors = [
                        'wysłany' => 'background:#dbeafe;color:#1e40af',
                        'rozpoczęty' => 'background:#d1fae5;color:#065f46',
                        'do_analizy' => 'background:#fef3c7;color:#92400e',
                        'zwrócony_do_poprawy' => 'background:#fee2e2;color:#991b1b',
                        'zaakceptowany' => 'background:#d1fae5;color:#065f46',
                        'zakończony' => 'background:#e5e7eb;color:#374151',
                        'zafakturowany' => 'background:#ede9fe;color:#5b21b6',
                        'zapłacony' => 'background:#d1fae5;color:#064e3b',
                    ];
                    $pillStyle = $pillColors[$audit->status] ?? 'background:#e0f2fe;color:#0369a1';
                @endphp
                <span style="display:inline-block; padding:3px 10px; border-radius:6px; font-weight:700; font-size:13px; {{ $pillStyle }}">{{ $audit->statusLabel() }}</span>
            </div>
            <div class="audit-info-card">
                <strong>Audytor</strong>
                {{ $audit->auditor?->name ?? '—' }}
            </div>
            <div class="audit-info-card">
                <strong>Ostatnia zmiana</strong>
                {{ $audit->updated_at?->format('d.m.Y H:i') ?? '—' }}
            </div>
        </div>

        {{-- Status change (admin/auditor only) --}}
        @if(!($isClient ?? false))
        <div style="background:#fff; border:1px solid #d5e0ea; border-radius:14px; padding:16px 20px; margin-bottom:14px;">
            <div style="font-size:13px; font-weight:700; color:#1d3a50; margin-bottom:10px;">Zmień status audytu:</div>
            <form method="POST" action="{{ route('firma.updateStatus', [$company, $audit]) }}" id="status-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" id="status-input" value="{{ $audit->status }}">
                <div class="status-bar">
                    @php
                        $statusDefs = [
                            'wysłany'             => ['label' => '📤 Wysłany',              'cls' => 's-wyslany'],
                            'rozpoczęty'          => ['label' => '▶ Rozpoczęty',             'cls' => 's-rozpoczety'],
                            'do_analizy'          => ['label' => '🔍 Do analizy',            'cls' => 's-do_analizy'],
                            'zwrócony_do_poprawy' => ['label' => '↩ Zwrócony do poprawy',   'cls' => 's-zwrocony'],
                            'zaakceptowany'       => ['label' => '✅ Zaakceptowany',          'cls' => 's-zaakceptowany'],
                            'zakończony'          => ['label' => '🏁 Zakończony',             'cls' => 's-zakonczony'],
                            'zafakturowany'       => ['label' => '🧾 Zafakturowany',          'cls' => 's-zafakturowany'],
                            'zapłacony'           => ['label' => '💰 Zapłacony',              'cls' => 's-zaplacony'],
                        ];
                    @endphp
                    @foreach($statusDefs as $statusKey => $def)
                        <button type="button"
                            class="status-btn {{ $def['cls'] }} {{ $audit->status === $statusKey ? 'active' : '' }}"
                            onclick="selectStatus('{{ $statusKey }}', this)"
                        >{{ $def['label'] }}</button>
                    @endforeach
                    <button type="submit" id="save-status-btn" class="btn-sm btn-primary-sm" style="margin-left:8px;">Zapisz status</button>
                </div>
            </form>
        </div>
        @endif

        {{-- Dane protokołu audytu --}}
        @if(!empty($conversation) && !empty($conversation->protocol_data))
            @php $proto = $conversation->protocol_data; @endphp
            <h3 style="margin:24px 0 8px;">Dane zebrane z audytu</h3>
            @if(!empty($proto['sekcje']))
                @foreach($proto['sekcje'] as $protoSection)
                    <div class="audit-section" style="border-left-color:#059669;">
                        <h4>{{ $protoSection['nazwa'] ?? '' }}</h4>
                        @if(!empty($protoSection['pola']))
                            <table class="audit-data-table">
                                <thead>
                                    <tr><th>Pole</th><th>Wartość</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($protoSection['pola'] as $pole)
                                        <tr>
                                            <td style="font-weight:600;">{{ $pole['klucz'] ?? '' }}</td>
                                            <td>{{ $pole['wartosc'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach
            @endif
            @if(!empty($proto['uwagi']))
                <div class="audit-section" style="border-left-color:#d97706; margin-top:10px;">
                    <h4>Uwagi</h4>
                    <p style="margin:0; font-size:13px; color:#2c4e67;">{{ $proto['uwagi'] }}</p>
                </div>
            @endif

            {{-- Rekomendacje i analiza --}}
            @php $analiza = $proto['analiza'] ?? null; @endphp
            @if($analiza)
                <h3 style="margin:28px 0 10px;">Rekomendacje i analiza</h3>
                @if(!empty($analiza['podsumowanie']))
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:14px 18px; margin-bottom:16px;">
                        <div style="font-size:11px; font-weight:700; color:#0369a1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">Stan energetyczny obiektu</div>
                        <p style="margin:0; font-size:13px; color:#0c4a6e; line-height:1.7;">{{ $analiza['podsumowanie'] }}</p>
                    </div>
                @endif
                @if(!empty($analiza['rekomendacje']))
                    @foreach($analiza['rekomendacje'] as $rec)
                        @php $p = strtolower($rec['priorytet'] ?? 'sredni'); @endphp
                        <div class="audit-section" style="border-left-color:{{ $p === 'wysoki' ? '#dc2626' : ($p === 'sredni' ? '#d97706' : '#059669') }}; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; flex-wrap:wrap;">
                                <span style="font-weight:800; font-size:14px; color:#0e3755;">{{ $rec['nr'] ?? '' }}. {{ $rec['obszar'] ?? '' }}</span>
                                <span style="padding:2px 10px; border-radius:5px; font-size:11px; font-weight:700; background:{{ $p === 'wysoki' ? '#fee2e2' : ($p === 'sredni' ? '#fef3c7' : '#d1fae5') }}; color:{{ $p === 'wysoki' ? '#991b1b' : ($p === 'sredni' ? '#92400e' : '#065f46') }};">
                                    {{ $p === 'wysoki' ? '🔴 Wysoki priorytet' : ($p === 'sredni' ? '🟡 Średni priorytet' : '🟢 Niski priorytet') }}
                                </span>
                            </div>
                            <div style="font-size:13.5px; font-weight:600; color:#1a2d3d; margin-bottom:4px;">{{ $rec['dzialanie'] ?? '' }}</div>
                            @if(!empty($rec['uzasadnienie']))
                                <div style="font-size:12.5px; color:#4c6373; margin-bottom:4px;">{{ $rec['uzasadnienie'] }}</div>
                            @endif
                            @if(!empty($rec['szacowane_oszczednosci']))
                                <div style="font-size:12px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:4px 10px; display:inline-block; color:#166534; font-weight:600;">
                                    💰 Szacowane oszczędności: {{ $rec['szacowane_oszczednosci'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
                @if(!empty($analiza['kolejnosc_dzialan']))
                    <div style="background:#fafafa; border:1px solid #e0e0e0; border-radius:10px; padding:14px 18px; margin-top:10px;">
                        <div style="font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">Optymalna kolejność działań</div>
                        <p style="margin:0; font-size:13px; color:#333; line-height:1.7;">{{ $analiza['kolejnosc_dzialan'] }}</p>
                    </div>
                @endif
            @endif

            @if(!($isClient ?? false))
                <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <a href="{{ route('firma.report', [$company, $audit]) }}" target="_blank" class="btn-sm btn-primary-sm">🖨 Raport do druku</a>
                    <a href="{{ route('ai.protocol.pdf', $conversation) }}" class="btn-sm btn-secondary-sm">📥 Pobierz PDF</a>
                    @if(empty($analiza))
                        <form method="POST" action="{{ route('ai.recommendations.generate', $conversation) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-sm" style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">⚡ Generuj rekomendacje</button>
                        </form>
                    @endif
                </div>
            @endif
        @elseif(!empty($conversation) && ($isClient ?? false) === false)
            <div style="padding:14px 18px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; margin-top:14px; font-size:13px; color:#92400e;">
                Klient przeprowadził rozmowę z audytorem, ale protokół nie został jeszcze wygenerowany.
                <form method="POST" action="{{ route('ai.protocol.generate', $conversation) }}" style="display:inline; margin-left:10px;">
                    @csrf
                    <button type="submit" class="btn-sm" style="background:#fef3c7; border:1px solid #fcd34d; color:#92400e;">⚙️ Generuj protokół teraz</button>
                </form>
            </div>
        @endif
    </section>

    <script>
        function selectStatus(key, btn) {
            document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('status-input').value = key;
        }
    </script>

    <x-admin-chat-float :chatMessages="$chatMessages" :company="$company" />
</x-layouts.app>
