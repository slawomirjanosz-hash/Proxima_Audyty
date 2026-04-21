<x-layouts.app>
    <style>
        .edit-section { border:1px solid #d7e5f0; border-left:5px solid #0e89d8; border-radius:12px; padding:16px 18px; background:#f8fbff; margin-bottom:18px; }
        .edit-section h3 { margin:0 0 12px; font-size:15px; color:#0e3755; font-weight:800; }
        .edit-table { width:100%; border-collapse:collapse; }
        .edit-table th { background:#eef5fb; color:#2c4e67; font-weight:700; padding:8px 12px; font-size:12px; text-transform:uppercase; letter-spacing:.4px; border:1px solid #d5e0ea; text-align:left; }
        .edit-table td { border:1px solid #e0ecf5; padding:7px 10px; font-size:13px; vertical-align:middle; }
        .edit-table td.field-name { color:#2c4e67; font-weight:600; width:42%; }
        .edit-table input[type="text"] { width:100%; border:1px solid #c8daea; border-radius:7px; padding:6px 10px; font-size:13px; color:#0f2330; background:#fff; }
        .edit-table input[type="text"]:focus { outline:none; border-color:#0e89d8; box-shadow:0 0 0 2px rgba(14,137,216,.15); }
        .rec-card { background:#fff; border:1px solid #d5e0ea; border-radius:10px; padding:14px 16px; margin-bottom:12px; }
        .rec-card.p-wysoki { border-left:5px solid #dc2626; }
        .rec-card.p-sredni  { border-left:5px solid #d97706; }
        .rec-card.p-niski   { border-left:5px solid #059669; }
        .rec-badge { display:inline-block; padding:2px 10px; border-radius:5px; font-size:11px; font-weight:700; margin-left:8px; }
        .rec-badge.p-wysoki { background:#fee2e2; color:#991b1b; }
        .rec-badge.p-sredni  { background:#fef3c7; color:#92400e; }
        .rec-badge.p-niski   { background:#d1fae5; color:#065f46; }
        .btn-save { padding:10px 24px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; }
        .btn-back { padding:10px 20px; background:#dbe9f5; color:#1d4f73; border:none; border-radius:9px; font-size:14px; font-weight:700; text-decoration:none; display:inline-block; }
    </style>

    <section class="panel">

        {{-- Status --}}
        @if(session('status'))
            <div style="margin-bottom:16px; padding:12px 16px; background:#f0fdf4; border:1px solid #86efac; border-radius:10px; color:#166534; font-weight:600;">
                ✅ {{ session('status') }}
            </div>
        @endif

        {{-- Breadcrumb --}}
        <div style="margin-bottom:12px; font-size:13px; color:#4c6373;">
            <a href="{{ route('strefa-klienta') }}" style="color:#0e89d8; text-decoration:none;">Strefa klienta</a>
            &nbsp;›&nbsp;
            <span>{{ $audit->title }} — Edytuj dane</span>
        </div>

        <h1 style="margin:0 0 4px;">✏️ Edycja danych audytu</h1>
        <p style="margin:0 0 20px; font-size:13px; color:#4c6373;">
            {{ $audit->title }} &middot;
            Możesz poprawić dane zebrane podczas audytu. Po zapisaniu system przygotuje rekomendacje.
        </p>

        @php $protocol = $conversation->protocol_data ?? []; @endphp

        @if(empty($protocol['sekcje']))
            <div style="padding:20px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; color:#92400e;">
                Brak danych do edycji. Najpierw przeprowadź audyt.
            </div>
        @else

        <form method="POST" action="{{ route('client.audit.update', $audit) }}">
            @csrf

            @foreach($protocol['sekcje'] as $si => $sekcja)
                <div class="edit-section">
                    <h3>{{ $sekcja['nazwa'] ?? 'Sekcja ' . ($si+1) }}</h3>
                    <table class="edit-table">
                        <thead>
                            <tr><th>Pole</th><th>Wartość</th></tr>
                        </thead>
                        <tbody>
                            @foreach($sekcja['pola'] ?? [] as $pi => $pole)
                                <tr>
                                    <td class="field-name">{{ $pole['klucz'] ?? '' }}</td>
                                    <td>
                                        <input
                                            type="text"
                                            name="fields[{{ $si }}][{{ $pi }}]"
                                            value="{{ old('fields.' . $si . '.' . $pi, $pole['wartosc'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div style="display:flex; gap:12px; align-items:center; margin-top:8px;">
                <button type="submit" class="btn-save">💾 Zapisz i analizuj</button>
                <a href="{{ route('strefa-klienta') }}" class="btn-back">← Powrót</a>
            </div>
        </form>

        @endif

        {{-- Rekomendacje --}}
        @php $analiza = $protocol['analiza'] ?? null; @endphp
        @if($analiza)
            <div style="margin-top:40px; border-top:2px solid #d5e0ea; padding-top:28px;">
                <h2 style="margin:0 0 4px; font-size:18px; color:#0e3755;">🤖 Analiza i rekomendacje energetyczne</h2>
                <p style="margin:0 0 20px; font-size:13px; color:#4c6373;">Wygenerowane przez system na podstawie Twoich danych.</p>

                @if(!empty($analiza['podsumowanie']))
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:14px 18px; margin-bottom:20px;">
                        <div style="font-size:11px; font-weight:700; color:#0369a1; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">Podsumowanie stanu energetycznego</div>
                        <p style="margin:0; font-size:13.5px; color:#0c4a6e; line-height:1.7;">{{ $analiza['podsumowanie'] }}</p>
                    </div>
                @endif

                @if(!empty($analiza['rekomendacje']))
                    <h3 style="font-size:14px; color:#0e3755; margin:0 0 12px;">Rekomendacje działań</h3>
                    @foreach($analiza['rekomendacje'] as $rec)
                        @php $p = strtolower($rec['priorytet'] ?? 'sredni'); @endphp
                        <div class="rec-card p-{{ $p }}">
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                                <span style="font-weight:800; font-size:13px; color:#0e3755;">
                                    {{ $rec['nr'] ?? '' }}. {{ $rec['obszar'] ?? '' }}
                                </span>
                                <span class="rec-badge p-{{ $p }}">
                                    {{ $p === 'wysoki' ? '🔴 Wysoki priorytet' : ($p === 'sredni' ? '🟡 Średni priorytet' : '🟢 Niski priorytet') }}
                                </span>
                            </div>
                            <div style="font-size:13.5px; font-weight:600; color:#1a2d3d; margin-bottom:4px;">
                                {{ $rec['dzialanie'] ?? '' }}
                            </div>
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
                    <div style="background:#fafafa; border:1px solid #e0e0e0; border-radius:10px; padding:14px 18px; margin-top:18px;">
                        <div style="font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">Optymalna kolejność działań</div>
                        <p style="margin:0; font-size:13px; color:#333; line-height:1.7;">{{ $analiza['kolejnosc_dzialan'] }}</p>
                    </div>
                @endif
            </div>
        @elseif(!empty($protocol['sekcje']))
            <div style="margin-top:30px; padding:14px 18px; background:#f8f9fa; border:1px solid #e0e0e0; border-radius:10px; font-size:13px; color:#6b7280; text-align:center;">
                💡 Zapisz dane, aby system wygenerował rekomendacje energetyczne.
            </div>
        @endif

    </section>
</x-layouts.app>
