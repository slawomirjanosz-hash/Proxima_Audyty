<x-layouts.app>

    <style>
        /* ─── Hero ─────────────────────────────────────────────── */
        .client-hero {
            background: linear-gradient(135deg, var(--green-deep) 0%, #255c45 60%, var(--green-primary) 100%);
            border-radius: 8px;
            padding: 36px 40px;
            color: var(--paper);
            display: grid;
            gap: 10px;
            box-shadow: 0 8px 32px rgba(26,77,58,.18);
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
            background: rgba(168,127,42,.12);
        }
        .client-tag {
            width: fit-content;
            background: rgba(168,127,42,.22);
            border: 1px solid rgba(212,168,75,.4);
            color: var(--gold-light, #D4A84B);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 4px;
            font-family: var(--mono);
        }
        .client-hero h1 { margin: 0; font-size: clamp(22px, 3vw, 36px); font-weight: 600; line-height: 1.15; font-family: var(--serif); color: var(--paper) !important; }
        .client-hero h1 span { color: #D4A84B; }
        .client-hero p { margin: 0; font-size: 14px; color: rgba(245,239,224,.7); max-width: 560px; }
        .client-meta { display: flex; gap: 24px; margin-top: 8px; flex-wrap: wrap; }
        .client-meta-item { display: flex; flex-direction: column; gap: 2px; }
        .client-meta-item .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: rgba(245,239,224,.5); font-weight: 700; font-family: var(--mono); }
        .client-meta-item .value { font-size: 14px; font-weight: 600; color: rgba(245,239,224,.9); }

        /* ─── Two-column grid ───────────────────────────────────── */
        .client-zone-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
            margin-top: 14px;
        }
        @media (max-width: 900px) { .client-zone-grid { grid-template-columns: 1fr; } }

        /* ─── Collapsible section boxes ─────────────────────────── */
        .cs-box {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .cs-toggle {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
        }
        .cs-toggle:hover { background: var(--green-bg); }
        .cs-toggle h3 { margin: 0; font-size: 15px; font-weight: 600; color: var(--green-deep); font-family: var(--serif); }
        .cs-body { display: none; padding: 0 18px 18px; }
        .cs-box.open .cs-body { display: block; }
        .cs-box.open .cs-chev { transform: rotate(180deg); }
        .cs-chev { font-size: 12px; color: var(--ink-mute); transition: transform .2s; flex-shrink: 0; }
        .cs-head-row { display: flex; align-items: center; gap: 8px; }

        /* ─── Chat notification blink ───────────────────────────── */
        @keyframes cs-blink-yellow {
            0%, 100% { background: var(--paper-soft); border-color: var(--paper-deep); }
            50%       { background: #fffbe0; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(168,127,42,.2); }
        }
        .cs-box--notify { animation: cs-blink-yellow 1s ease-in-out infinite; }
        .cs-box--notify .cs-toggle h3::after { content: ' 🔔'; }

        /* ─── Audit type 3-column radio grid ────────────────────── */
        .audit-type-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }
        @media (max-width: 700px) { .audit-type-grid { grid-template-columns: 1fr; } }
        .audit-col {
            background: var(--paper-soft);
            border: 1px solid var(--paper-deep);
            border-radius: 6px;
            padding: 12px;
        }
        .audit-col-head {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--green-deep);
            margin: 0 0 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--green-light);
            line-height: 1.3;
            font-family: var(--mono);
        }
        .audit-rl {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            margin-bottom: 4px;
            transition: background .12s;
            font-size: 13px;
            font-weight: 600;
            color: var(--green-deep);
        }
        .audit-rl:hover { background: var(--green-bg); }
        .audit-rl:has(input:checked) { background: var(--green-bg); border-color: var(--green-primary); }
        .audit-rl input { accent-color: var(--green-primary); width: 15px; height: 15px; cursor: pointer; flex-shrink: 0; }

        /* ─── Inquiry list ───────────────────────────────────────── */
        .inquiry-list { display: grid; gap: 10px; }
        .inquiry-item {
            border: 1px solid var(--paper-deep); border-radius: 6px;
            padding: 14px 16px; background: var(--paper-soft);
            display: flex; align-items: flex-start; gap: 12px; flex-wrap: wrap;
            justify-content: space-between;
        }
        .inquiry-body { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 200px; }
        .inquiry-type { font-weight: 600; font-size: 15px; color: var(--green-deep); font-family: var(--serif); }
        .inquiry-msg  { font-size: 13px; color: var(--ink-mute); white-space: pre-line; }
        .inquiry-date { font-size: 12px; color: var(--ink-mute); margin-top: 4px; font-family: var(--mono); }
        .inquiry-badge {
            padding: 4px 10px; border-radius: 4px;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
            white-space: nowrap; font-family: var(--mono);
        }
        .count-badge {
            background: var(--green-bg); color: var(--green-deep);
            font-size: 11px; font-weight: 700;
            padding: 3px 8px; border-radius: 4px;
        }

        /* ─── Chat ───────────────────────────────────────────────── */
        .chat-client-box {
            background: var(--paper-soft); border: 1px solid var(--paper-deep); border-radius: 6px;
            padding: 10px 12px; max-height: 300px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 8px; margin-bottom: 10px;
        }
        .chat-bw { display: flex; flex-direction: column; }
        .chat-bw.admin { align-items: flex-end; }
        .chat-bw.client-me { align-items: flex-start; }
        .chat-b { max-width: 80%; padding: 8px 12px; border-radius: 8px; font-size: 13px; line-height: 1.45; white-space: pre-wrap; word-break: break-word; }
        .chat-b.admin  { background: var(--green-deep); color: var(--paper); border-bottom-right-radius: 2px; }
        .chat-b.client-me { background: white; border: 1px solid var(--paper-deep); color: var(--ink); border-bottom-left-radius: 2px; }
        .chat-b-meta { font-size: 11px; color: var(--ink-mute); margin-top: 3px; font-family: var(--mono); }

        /* ─── Offer cards ────────────────────────────────────────── */
        .offer-preview-card {
            margin-top: 10px; background: var(--green-bg); border: 1px solid var(--green-light);
            border-radius: 6px; padding: 14px 16px;
        }
        .offer-preview-card h4 { margin: 0 0 4px; font-size: 15px; font-weight: 600; color: var(--green-deep); font-family: var(--serif); }
        .offer-preview-meta { font-size: 12px; color: var(--ink-mute); margin-bottom: 10px; font-family: var(--mono); }
        .offer-preview-desc { font-size: 13px; color: var(--ink); margin-bottom: 10px; white-space: pre-wrap; }
        .offer-btn-group { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .offer-btn { padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-family: var(--sans); }
        .offer-btn-blue  { background: var(--green-primary); color: var(--paper); }
        .offer-btn-blue:hover { background: var(--green-deep); }
        .offer-btn-gray  { background: var(--paper-deep); color: var(--green-deep); }
        .offer-btn-green { background: var(--green-primary); color: var(--paper); }
        .offer-sections-detail { margin-top: 10px; font-size: 13px; color: var(--ink); }
        .offer-sections-detail table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .offer-sections-detail th { font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--ink-mute); background: var(--paper-deep); padding: 5px 6px; text-align: left; font-family: var(--mono); }
        .offer-sections-detail td { padding: 4px 6px; border-bottom: 1px solid var(--paper-deep); font-size: 12px; }

        /* ─── Audit cards ────────────────────────────────────────── */
        .audit-client-card {
            border: 1px solid var(--paper-deep); border-radius: 6px; padding: 14px 16px;
            background: var(--paper-soft); display: flex; align-items: center; gap: 12px;
            flex-wrap: wrap; justify-content: space-between;
        }
        .audit-client-card.status-wyslany { border-left: 4px solid var(--gold); }
        .audit-client-card.status-rozpoczety { border-left: 4px solid var(--green-primary); }
        .audit-client-card.status-zaakceptowany { border-left: 4px solid var(--green-deep); }
        .audit-client-card.status-zakonczony { border-left: 4px solid var(--ink-mute); }

        /* ─── Admin preview table ────────────────────────────────── */
        .preview-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .preview-table th, .preview-table td { padding: 9px 10px; font-size: 13px; border-bottom: 1px solid var(--paper-deep); text-align: left; }
        .preview-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: var(--ink-mute); background: var(--paper-deep); font-family: var(--mono); }
        .preview-table tr:last-child td { border-bottom: none; }

        /* ─── Shared buttons ─────────────────────────────────────── */
        .btn-action {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 5px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            border: none; text-decoration: none; font-family: var(--sans);
        }
        .btn-action-blue  { background: var(--green-primary); color: var(--paper); }
        .btn-action-blue:hover { background: var(--green-deep); }
        .btn-action-green { background: var(--green-primary); color: var(--paper); }
        .btn-action-green:hover { background: var(--green-deep); }

        @media (max-width: 800px) { .client-hero { padding: 24px 20px; } }
    </style>

    {{-- Welcome hero --}}
    <div class="client-hero">
        <span class="client-tag">Strefa klienta</span>
        <h1>Witaj, <span>{{ auth()->user()->name }}</span></h1>
        @if($company)
            <p style="margin:2px 0 10px; font-size:15px; font-weight:700; color:rgba(255,255,255,.9);">{{ $company->name }}</p>
        @endif
        <p>Twoja dedykowana przestrzeń ENESA — tutaj możesz wysłać zapytanie dotyczące wybranego rodzaju audytu lub skontaktować się z nami bezpośrednio.</p>
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

        <div class="client-zone-grid">

            {{-- LEFT COLUMN --}}
            <div>

                {{-- Section: Wyslij zapytanie --}}
                <div class="cs-box open" id="cs-inquiry">
                    <button class="cs-toggle" type="button" onclick="toggleCs('cs-inquiry')">
                        <h3>📋 Wyślij zapytanie o audyt</h3>
                        <span class="cs-chev">&#9660;</span>
                    </button>
                    <div class="cs-body">
                        <p style="font-size:13px; color:#4c6373; margin:0 0 12px 0;">Wybierz jeden rodzaj audytu który Cię interesuje i wyślij zapytanie. Odpiszemy tak szybko jak to możliwe.</p>

                        @error('audit_type')
                            <div style="color:#b91c1c; font-size:12px; margin-bottom:8px; padding:8px 12px; background:#fef2f2; border-radius:8px; border:1px solid #fca5a5;">
                                &#9888; {{ $message }}
                            </div>
                        @enderror

                        <form method="POST" action="{{ route('client.inquiry.store') }}">
                            @csrf

                            <div class="audit-type-grid">
                                {{-- Audyty energetyczne --}}
                                <div class="audit-col">
                                    <div class="audit-col-head">⚡ Audyty<br>energetyczne</div>
                                    @forelse ($auditTypesByCategory['energy'] as $type)
                                        <label class="audit-rl">
                                            <input type="radio" name="audit_type" value="{{ $type['value'] }}" @checked(old('audit_type') === $type['value'])>
                                            {{ $type['name'] }}
                                        </label>
                                    @empty
                                        <p style="font-size:12px; color:#8aa3b5; font-style:italic; margin:0;">Brak zdefiniowanych typow.</p>
                                    @endforelse
                                </div>

                                {{-- Audyty ISO --}}
                                <div class="audit-col">
                                    <div class="audit-col-head">🏭 Audyty<br>ISO 50001</div>
                                    @forelse ($auditTypesByCategory['iso'] as $type)
                                        <label class="audit-rl">
                                            <input type="radio" name="audit_type" value="{{ $type['value'] }}" @checked(old('audit_type') === $type['value'])>
                                            {{ $type['name'] }}
                                        </label>
                                    @empty
                                        <p style="font-size:12px; color:#8aa3b5; font-style:italic; margin:0;">Brak.</p>
                                    @endforelse
                                </div>

                                {{-- Biale certyfikaty --}}
                                <div class="audit-col">
                                    <div class="audit-col-head">📜 Białe<br>certyfikaty</div>
                                    @forelse ($auditTypesByCategory['white_cert'] as $type)
                                        <label class="audit-rl">
                                            <input type="radio" name="audit_type" value="{{ $type['value'] }}" @checked(old('audit_type') === $type['value'])>
                                            {{ $type['name'] }}
                                        </label>
                                    @empty
                                        <p style="font-size:12px; color:#8aa3b5; font-style:italic; margin:0;">Brak.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div style="margin-bottom:12px;">
                                <label style="display:block; font-size:12px; font-weight:700; color:#4c6373; margin-bottom:4px;">Wiadomość (opcjonalnie)</label>
                                <textarea name="message" rows="3" style="width:100%; resize:vertical;" placeholder="Opisz swoje potrzeby lub zadaj pytanie...">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" class="btn-action btn-action-blue">
                                📤 Wyślij zapytanie
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Section: Moje zapytania --}}
                <div class="cs-box" id="cs-inquiries">
                    <button class="cs-toggle" type="button" onclick="toggleCs('cs-inquiries')">
                        <h3>📬 Moje zapytania</h3>
                        <div class="cs-head-row">
                            @if ($inquiries->isNotEmpty())
                                <span class="count-badge">{{ $inquiries->count() }}</span>
                            @endif
                            <span class="cs-chev">&#9660;</span>
                        </div>
                    </button>
                    <div class="cs-body">
                        @if ($inquiries->isEmpty())
                            <div style="padding:20px; text-align:center; color:#9ab4c5; border:1px dashed #d5e0ea; border-radius:12px; font-size:14px;">
                                Nie wysłałeś jeszcze żadnego zapytania. Użyj formularza powyżej.
                            </div>
                        @else
                            <div class="inquiry-list">
                                @foreach ($inquiries as $inquiry)
                                    <div class="inquiry-item" style="flex-direction:column; align-items:stretch;">
                                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; flex-wrap:wrap;">
                                            <div class="inquiry-body">
                                                <span class="inquiry-type">{{ $inquiry->audit_type_name ?? $inquiry->auditType?->name ?? '&#8212;' }}</span>
                                                @if ($inquiry->message)
                                                    <span class="inquiry-msg">{{ $inquiry->message }}</span>
                                                @endif
                                                <span class="inquiry-date">Wyslano: {{ $inquiry->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            <span class="inquiry-badge" style="background:{{ $inquiry->statusBg() }}; color:{{ $inquiry->statusColor() }}; border:1px solid {{ $inquiry->statusBg() }}; align-self:flex-start;">
                                                {{ $inquiry->statusLabel() }}
                                            </span>
                                        </div>

                                        @if ($inquiry->offer && in_array($inquiry->status, ['in_review', 'offer_accepted']))
                                            <div class="offer-preview-card">
                                                <h4>{{ $inquiry->offer->offer_title }}</h4>
                                                <div class="offer-preview-meta">
                                                    @if ($inquiry->offer->offer_number) Nr {{ $inquiry->offer->offer_number }} &nbsp;&middot;&nbsp; @endif
                                                    @if ($inquiry->offer->offer_date) {{ $inquiry->offer->offer_date->format('d.m.Y') }} &nbsp;&middot;&nbsp; @endif
                                                    Wartosc: <strong>{{ number_format((float)($inquiry->offer->total_price ?? 0), 2, ',', ' ') }} zl</strong>
                                                </div>
                                                @if ($inquiry->offer->offer_description && strip_tags($inquiry->offer->offer_description))
                                                    <div class="offer-preview-desc">{!! nl2br(strip_tags($inquiry->offer->offer_description)) !!}</div>
                                                @endif

                                                <div class="offer-btn-group">
                                                    <button type="button" class="offer-btn offer-btn-gray" onclick="toggleOfferSections({{ $inquiry->id }})">
                                                        Zobacz szczegoly
                                                    </button>
                                                    <a href="{{ route('client.offer.pdf', $inquiry->offer) }}" class="offer-btn offer-btn-blue" target="_blank">
                                                        Generuj PDF
                                                    </a>
                                                    @if ($inquiry->status === 'in_review')
                                                        <form method="POST" action="{{ route('client.offer.accept', $inquiry) }}" style="margin:0;">
                                                            @csrf
                                                            <button type="submit" class="offer-btn offer-btn-green" onclick="return confirm('Zaakceptowac te oferte?')">
                                                                Akceptuj oferte
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span style="padding:8px 16px; background:#d1fae5; color:#065f46; border-radius:9px; font-size:13px; font-weight:700;">
                                                            Oferta zaakceptowana
                                                        </span>
                                                    @endif
                                                </div>

                                                <div id="offer-sections-{{ $inquiry->id }}" class="offer-sections-detail" style="display:none;">
                                                    @foreach (['services' => 'Uslugi', 'works' => 'Prace', 'materials' => 'Materialy'] as $key => $label)
                                                        @if ($inquiry->offer->$key)
                                                            <div style="margin-top:10px; font-weight:700; color:#0f2330;">{{ $label }}</div>
                                                            <table>
                                                                <thead><tr><th>Nazwa</th><th>Ilosc</th><th>Wartosc</th></tr></thead>
                                                                <tbody>
                                                                    @foreach ($inquiry->offer->$key as $item)
                                                                        <tr>
                                                                            <td>{{ $item['name'] ?? '&#8212;' }}</td>
                                                                            <td>{{ $item['quantity'] ?? 1 }}</td>
                                                                            <td>{{ number_format((float)($item['value'] ?? 0), 2, ',', ' ') }} zl</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    @endforeach
                                                    @if ($inquiry->offer->custom_sections)
                                                        @foreach ($inquiry->offer->custom_sections as $cs)
                                                            <div style="margin-top:10px; font-weight:700; color:#0f2330;">{{ $cs['name'] ?? 'Sekcja' }}</div>
                                                            <table>
                                                                <thead><tr><th>Nazwa</th><th>Ilosc</th><th>Wartosc</th></tr></thead>
                                                                <tbody>
                                                                    @foreach (($cs['items'] ?? []) as $item)
                                                                        <tr>
                                                                            <td>{{ $item['name'] ?? '&#8212;' }}</td>
                                                                            <td>{{ $item['quantity'] ?? 1 }}</td>
                                                                            <td>{{ number_format((float)($item['value'] ?? 0), 2, ',', ' ') }} zl</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- end LEFT --}}

            {{-- RIGHT COLUMN --}}
            <div>

                {{-- Section: Chat --}}
                <div class="cs-box{{ $company ? ' open' : '' }}" id="cs-chat">
                    <button class="cs-toggle" type="button" onclick="toggleCs('cs-chat')">
                        <h3>💬 Chat z zespołem ENESA</h3>
                        <span class="cs-chev">&#9660;</span>
                    </button>
                    <div class="cs-body">
                        @if ($company)
                            <p style="font-size:13px; color:#4c6373; margin:0 0 10px;">Masz pytanie? Napisz do nas bezpośrednio — odpowiemy najszybciej jak to możliwe.</p>
                            <div class="chat-client-box" id="client-chat-box">
                                @forelse ($chatMessages as $msg)
                                    <div class="chat-bw {{ $msg->is_from_admin ? 'admin' : 'client-me' }}" data-msg-id="{{ $msg->id }}">
                                        <div class="chat-b {{ $msg->is_from_admin ? 'admin' : 'client-me' }}">{{ $msg->message }}</div>
                                        <div class="chat-b-meta">
                                            {{ $msg->is_from_admin ? 'ENESA' : 'Ty' }} &middot; {{ $msg->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div id="client-chat-empty" style="text-align:center; color:#9ab4c5; font-size:13px; padding:14px;">Brak wiadomosci. Napisz do nas!</div>
                                @endforelse
                            </div>
                            <div style="display:flex; gap:8px; align-items:flex-end;">
                                <textarea id="client-chat-input" rows="2" placeholder="Napisz wiadomość..." style="flex:1; resize:none;" required></textarea>
                                <button type="button" id="client-chat-send-btn" class="btn-action btn-action-green" style="align-self:flex-end; white-space:nowrap;" onclick="clientChatSend()">Wyślij</button>
                            </div>
                            <script>
                            (function() {
                                const chatBox  = document.getElementById('client-chat-box');
                                const input    = document.getElementById('client-chat-input');
                                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                                const csrf     = csrfMeta ? csrfMeta.content : '{{ csrf_token() }}';
                                let lastId     = {{ $chatMessages->last()?->id ?? 0 }};

                                function escHtml(s) {
                                    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
                                }

                                function appendBubble(msg) {
                                    const empty = document.getElementById('client-chat-empty');
                                    if (empty) empty.remove();
                                    const wrap = document.createElement('div');
                                    const cls  = msg.is_from_admin ? 'admin' : 'client-me';
                                    wrap.className = 'chat-bw ' + cls;
                                    wrap.dataset.msgId = msg.id;
                                    const label = msg.is_from_admin ? 'ENESA' : 'Ty';
                                    wrap.innerHTML =
                                        '<div class="chat-b ' + cls + '">' + escHtml(msg.message) + '</div>' +
                                        '<div class="chat-b-meta">' + label + ' &middot; ' + escHtml(msg.created_at) + '</div>';
                                    chatBox.appendChild(wrap);
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                }

                                window.clientChatSend = function() {
                                    const msg = input.value.trim();
                                    if (!msg) return;
                                    const btn = document.getElementById('client-chat-send-btn');
                                    btn.disabled = true;
                                    fetch('{{ route('client.chat.send.ajax') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ message: msg })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.id) {
                                            appendBubble(data);
                                            lastId = Math.max(lastId, data.id);
                                            input.value = '';
                                        }
                                    })
                                    .catch(function(){})
                                    .finally(function() { btn.disabled = false; input.focus(); });
                                };

                                input.addEventListener('keydown', function(e) {
                                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); clientChatSend(); }
                                });

                                function pollChat() {
                                    fetch('{{ route('client.chat.poll') }}?after=' + lastId, {
                                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (!data.messages || !data.messages.length) return;
                                        let hasAdminMsg = false;
                                        data.messages.forEach(function(msg) {
                                            if (msg.id > lastId) {
                                                appendBubble(msg);
                                                lastId = msg.id;
                                                if (msg.is_from_admin) hasAdminMsg = true;
                                            }
                                        });
                                        if (hasAdminMsg) {
                                            var box = document.getElementById('cs-chat');
                                            // Only blink when section is collapsed
                                            if (box && !box.classList.contains('open')) {
                                                box.classList.add('cs-box--notify');
                                            }
                                        }
                                    })
                                    .catch(function(){});
                                }

                                document.addEventListener('DOMContentLoaded', function() {
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                    setInterval(pollChat, 5000);
                                });
                            })();
                            </script>
                        @else
                            <p style="font-size:13px; color:#8aa3b5;">Chat dostępny po przypisaniu Twojego konta do firmy przez administratora.</p>
                        @endif
                    </div>
                </div>

                {{-- Section: Moje audyty --}}
                @if (isset($companyAudits) && $companyAudits->isNotEmpty())
                    <div class="cs-box" id="cs-audits">
                        <button class="cs-toggle" type="button" onclick="toggleCs('cs-audits')">
                            <h3>🔍 Moje audyty</h3>
                            <div class="cs-head-row">
                                <span class="count-badge">{{ $companyAudits->count() }}</span>
                                <span class="cs-chev">&#9660;</span>
                            </div>
                        </button>
                        <div class="cs-body">
                            <p style="font-size:13px; color:#4c6373; margin:0 0 12px;">Audyty przydzielone przez nasz zespół. Wejdź, aby przeprowadzić audyt.</p>
                            <div class="inquiry-list">
                                @foreach ($companyAudits as $audit)
                                    @php
                                        $hasStarted = $audit->status !== 'wysłany';
                                        $hasData = \App\Models\AiConversation::where('context_id', $audit->id)->whereNotNull('protocol_data')->exists();
                                    @endphp
                                    <div class="audit-client-card status-{{ $audit->status }}" style="flex-direction:column; align-items:stretch; gap:10px;">
                                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                            <div style="flex:1; min-width:200px;">
                                                <div style="font-weight:800; font-size:15px; color:#0f2330;">{{ $audit->title }}</div>
                                                <div style="font-size:12px; color:#4c6373; margin-top:2px;">
                                                    {{ $audit->auditType?->name ?? $audit->audit_type ?? '&#8212;' }}
                                                    &nbsp;&middot;&nbsp; {{ $audit->created_at->format('d.m.Y') }}
                                                </div>
                                            </div>
                                            <span class="inquiry-badge" style="background:#e0f2fe; color:#0369a1; border:none; white-space:nowrap;">
                                                {{ $audit->statusLabel() }}
                                            </span>
                                        </div>
                                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                            @if($audit->agent_type === 'compressor_room' || $audit->agent_type === 'bc_compressor_room')
                                                <a href="{{ route('client.audit.compressor.questionnaire', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#2E7D5C,#1A4D3A); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    📋 {{ $audit->questionnaire_completed ? 'Edytuj ankietę Kompresory' : 'Wypełnij ankietę Kompresory' }}
                                                </a>
                                                <a href="{{ route('client.audit.ai', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    {{ $hasStarted ? '▶ Kontynuuj audyt' : '▶ Rozpocznij audyt' }}
                                                </a>
                                            @elseif($audit->agent_type === 'iso50001' && !$audit->questionnaire_completed)
                                                <a href="{{ route('client.audit.iso.questionnaire', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    📋 Wypełnij kwestionariusz
                                                </a>
                                            @elseif($audit->agent_type === 'general')
                                                <a href="{{ route('client.audit.master') }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#d97706,#0e89d8); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    📋 Wypełnij ankietę Master
                                                </a>
                                                @php $masterDone = \App\Models\EnergyAuditMasterData::where('company_id', $audit->company_id)->where('completion_percent', '>=', 30)->exists(); @endphp
                                                @if($masterDone)
                                                <a href="{{ route('client.audit.ai', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    {{ $hasStarted ? '▶ Kontynuuj audyt' : '▶ Rozpocznij audyt' }}
                                                </a>
                                                @endif
                                            @else
                                                <a href="{{ route('client.audit.ai', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:linear-gradient(130deg,#1ba84a,#0e89d8); color:#fff; font-size:12px; font-weight:700; text-decoration:none;">
                                                    {{ $hasStarted ? '▶ Kontynuuj audyt' : '▶ Rozpocznij audyt' }}
                                                </a>
                                            @endif
                                            @if($audit->agent_type === 'iso50001' && $audit->questionnaire_completed)
                                                <a href="{{ route('client.audit.iso.questionnaire', $audit) }}"
                                                   style="padding:7px 14px; border-radius:8px; background:#e0f2fe; color:#0369a1; font-size:12px; font-weight:700; text-decoration:none; border:1px solid #bfdfff;">
                                                    📋 Edytuj kwestionariusz
                                                </a>
                                            @endif
                                            @if($hasData)
                                            <a href="{{ route('client.audit.edit', $audit) }}"
                                               style="padding:7px 14px; border-radius:8px; background:#dbe9f5; color:#1d4f73; font-size:12px; font-weight:700; text-decoration:none;">
                                                ✏️ Edytuj dane / Rekomendacje
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>{{-- end RIGHT --}}

        </div>{{-- end .client-zone-grid --}}

        <script>
        function toggleCs(id) {
            var box = document.getElementById(id);
            box.classList.toggle('open');
            // Clear notification when user opens the chat
            if (id === 'cs-chat' && box.classList.contains('open')) {
                box.classList.remove('cs-box--notify');
            }
        }
        function toggleOfferSections(id) {
            var el = document.getElementById('offer-sections-' + id);
            if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
        }
        @if ($errors->hasAny(['audit_type', 'message']))
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('cs-inquiry');
            if (el) el.classList.add('open');
        });
        @endif
        </script>

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
                            <th>Wiadomosc</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->user?->name ?? '&#8212;' }}</td>
                                <td>{{ $inquiry->company?->name ?? '&#8212;' }}</td>
                                <td>{{ $inquiry->audit_type_name ?? '&#8212;' }}</td>
                                <td>{{ $inquiry->message ? \Illuminate\Support\Str::limit($inquiry->message, 60) : '&#8212;' }}</td>
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
