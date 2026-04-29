<x-layouts.app>
    <style>
        .protocol-wrapper { max-width: 900px; margin: 0 auto; padding: 24px 0; }
        .protocol-topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; text-decoration: none; background: #f0f4f8; color: #1d4f73; border: 1px solid #c5d8ea; }
        .btn-pdf  { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 9px; font-size: 13px; font-weight: 700; text-decoration: none; background: linear-gradient(130deg,#c0392b,#e74c3c); color: #fff; border: none; }
        .btn-regen { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; text-decoration: none; background: linear-gradient(130deg,#1ba84a,#0e89d8); color: #fff; border: none; cursor: pointer; }
        .protocol-card { background: #fff; border: 1px solid #d4e6f5; border-radius: 14px; margin-bottom: 20px; overflow: hidden; box-shadow: 0 2px 10px rgba(14,55,85,.06); }
        .protocol-card-header { background: linear-gradient(130deg,rgba(27,168,74,.08),rgba(14,137,216,.1)); padding: 12px 20px; border-bottom: 1px solid #d4e6f5; font-weight: 700; font-size: 15px; color: #0e3755; }
        .protocol-table { width: 100%; border-collapse: collapse; }
        .protocol-table tr:nth-child(even) td { background: #f7fbfe; }
        .protocol-table td { padding: 9px 16px; font-size: 13.5px; border-bottom: 1px solid #e8eef3; vertical-align: top; }
        .protocol-table td:first-child { color: #6b8499; font-weight: 600; width: 40%; white-space: nowrap; }
        .protocol-table td:last-child { color: #1a2d3d; }
        .remarks-card { background: #fffbf0; border: 1px solid #f0d890; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; }
        .remarks-card h4 { margin: 0 0 8px; font-size: 14px; color: #7d5e00; }
        .meta-line { font-size: 12px; color: #8a9fb3; text-align: right; margin-bottom: 6px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #8a9fb3; font-size: 15px; }
    </style>

    <div class="protocol-wrapper">
        @if(session('success'))
            <div style="background:#e8f8ee;border:1px solid #a3d9b5;color:#1a6636;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13.5px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="protocol-topbar">
            <a href="{{ route('ai.show', $conversation) }}" class="btn-back">← {{ __('Back to conversations') }}</a>

            <div style="display:flex;gap:8px;align-items:center;">
                <form method="POST" action="{{ route('ai.protocol.generate', $conversation) }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn-regen"
                            onclick="return confirm('Wygenerować protokół ponownie? Obecny zostanie nadpisany.')">
                        🔄 Regeneruj
                    </button>
                </form>
                @if(!empty($protocol))
                    <button type="button" onclick="openPdfModal('{{ route('ai.protocol.stream', $conversation) }}')"
                       style="padding:7px 14px;border-radius:9px;font-size:13px;font-weight:600;background:#f0f4f8;color:#1d4f73;border:1px solid #c5d8ea;cursor:pointer;">
                        👁 Podgląd PDF
                    </button>
                    <a href="{{ route('ai.protocol.pdf', $conversation) }}" class="btn-pdf" target="_blank">
                        📥 Pobierz PDF
                    </a>
                @endif
            </div>
        </div>

        <h1 style="font-size:20px;font-weight:700;margin:0 0 4px;">📋 Protokół z rozmowy</h1>
        <p style="font-size:13px;color:#8a9fb3;margin:0 0 20px;">
            {{ $conversation->title }}
            @if($conversation->protocol_generated_at)
                · wygenerowany {{ $conversation->protocol_generated_at->format('d.m.Y H:i') }}
            @endif
        </p>

        @if(empty($protocol) || empty($protocol['sekcje'] ?? null))
            <div class="empty-state">
                <div style="font-size:40px;margin-bottom:12px;">📭</div>
                Protokół nie został jeszcze wygenerowany lub jest pusty.<br>
                <form method="POST" action="{{ route('ai.protocol.generate', $conversation) }}" style="margin-top:16px">
                    @csrf
                    <button type="submit" class="btn-regen" style="font-size:14px;padding:10px 20px;">
                        ⚡ Generuj teraz
                    </button>
                </form>
            </div>
        @else
            @foreach($protocol['sekcje'] as $sekcja)
                <div class="protocol-card">
                    <div class="protocol-card-header">{{ $sekcja['nazwa'] ?? 'Sekcja' }}</div>
                    <table class="protocol-table">
                        @foreach($sekcja['pola'] ?? [] as $pole)
                            <tr>
                                <td>{{ $pole['klucz'] ?? '' }}</td>
                                <td>{{ $pole['wartosc'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach

            @if(!empty($protocol['uwagi']))
                <div class="remarks-card">
                    <h4>📝 Uwagi i zalecenia</h4>
                    <p style="margin:0;font-size:13.5px;color:#6b4e00;line-height:1.6;">{{ $protocol['uwagi'] }}</p>
                </div>
            @endif
        @endif
    </div>

    {{-- PDF viewer modal --}}
    <div id="pdf-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.75);">
        <div style="position:absolute;inset:0;display:flex;flex-direction:column;">
            <div style="flex-shrink:0;background:#1a252f;height:48px;display:flex;align-items:center;padding:0 16px;gap:12px;">
                <span style="color:#c8d8e4;font-size:13px;font-weight:600;flex:1;">👁 Podgląd protokołu</span>
                <button onclick="closePdfModal()" style="background:#4a6375;color:#fff;border:none;border-radius:7px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;">✕ Zamknij</button>
            </div>
            <iframe id="pdf-iframe" src="" style="flex:1;border:none;width:100%;background:#2c3e50;"></iframe>
        </div>
    </div>

    <script>
        function openPdfModal(url) {
            document.getElementById('pdf-iframe').src = url;
            document.getElementById('pdf-modal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closePdfModal() {
            document.getElementById('pdf-modal').style.display = 'none';
            document.getElementById('pdf-iframe').src = '';
            document.body.style.overflow = '';
        }
        document.getElementById('pdf-modal').addEventListener('click', function(e) {
            if (e.target === this) closePdfModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePdfModal();
        });
    </script>
</x-layouts.app>
