<x-layouts.app>
    <style>
        .ai-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 14px; }
        .ai-card { background: var(--paper-soft); border: 1px solid var(--paper-deep); border-radius: 8px; padding: 18px; }
        .ai-card h3 { margin: 0 0 6px; font-size: 15px; font-family: var(--serif); color: var(--green-deep); font-weight: 600; }
        .ai-card .meta { font-size: 12px; color: var(--ink-mute); }
        .ai-card .actions { margin-top: 14px; display: flex; gap: 8px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 5px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; font-family: var(--sans); }
        .btn-primary { background: var(--green-primary); color: var(--paper); }
        .btn-primary:hover { background: var(--green-deep); }
        .btn-secondary { background: var(--paper-soft); color: var(--green-deep); border: 1px solid var(--paper-deep); }
        .btn-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
        .context-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-bottom: 8px; }
        .badge-energy { background: #d1fae5; color: #065f46; }
        .badge-iso { background: #dbeafe; color: #1e40af; }
        .badge-offer { background: #fef3c7; color: #92400e; }
        .badge-general { background: #f3f4f6; color: #374151; }
    </style>

    <div class="topbar">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:20px">🤖</span>
            <strong>Asystent AI</strong>
        </div>
        <a href="{{ route('ai.create') }}" class="btn btn-primary">+ Nowa rozmowa</a>
    </div>

    @if(session('success'))
        <div class="status">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if($conversations->isEmpty())
            <div class="empty-state">
                <div class="icon">🤖</div>
                <h3>Brak rozmów z asystentem AI</h3>
                <p>Rozpocznij nową rozmowę, by AI pomogło Ci zebrać dane do audytu lub przygotować ofertę.</p>
                <a href="{{ route('ai.create') }}" class="btn btn-primary" style="margin-top:12px">Rozpocznij rozmowę</a>
            </div>
        @else
            <div class="ai-grid">
                @foreach($conversations as $conv)
                    @php
                        $badgeClass = match($conv->context_type) {
                            'energy_audit' => 'badge-energy',
                            'iso50001'     => 'badge-iso',
                            'offer'        => 'badge-offer',
                            default        => 'badge-general',
                        };
                        $badgeLabel = match($conv->context_type) {
                            'energy_audit'            => 'Audyt energetyczny',
                            'iso50001'                => 'ISO 50001',
                            'offer'                   => 'Oferta',
                            'compressor_room'         => 'Kompresory',
                            'boiler_room'             => 'Kotłownia',
                            'drying_room'             => 'Suszarnia',
                            'buildings'               => 'Budynki',
                            'technological_processes' => 'Procesy technologiczne',
                            'bc_general'                 => 'BC Ogólnie',
                            'bc_compressor_room'         => 'BC Kompresory',
                            'bc_boiler_room'             => 'BC Kotłownia',
                            'bc_drying_room'             => 'BC Suszarnia',
                            'bc_buildings'               => 'BC Budynki',
                            'bc_technological_processes' => 'BC Procesy technologiczne',
                            default                   => 'Ogólny',
                        };
                    @endphp
                    <div class="ai-card">
                        <span class="context-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        <h3>{{ $conv->title }}</h3>
                        <div class="meta">{{ $conv->created_at->format('d.m.Y H:i') }}</div>
                        <div class="actions">
                            <a href="{{ route('ai.show', $conv) }}" class="btn btn-primary">Kontynuuj</a>
                            <form method="POST" action="{{ route('ai.destroy', $conv) }}" onsubmit="return confirm('Zarchiwizować rozmowę?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Archiwizuj</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
