<x-layouts.app>
    <style>
        .create-card { max-width: 480px; margin: 40px auto; background: #fff; border: 1px solid var(--line); border-radius: 16px; padding: 28px; box-shadow: 0 8px 24px rgba(14,55,85,.08); }
        .create-card h2 { margin: 0 0 6px; }
        .create-card p { color: var(--muted); font-size: 14px; margin: 0 0 22px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; }
        .form-group select, .form-group input { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #c9d7e3; font-size: 14px; }
        .type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .type-btn { border: 2px solid var(--line); border-radius: 12px; padding: 14px 10px; text-align: center; cursor: pointer; background: #fff; transition: all .15s; }
        .type-btn input { display: none; }
        .type-btn .icon { font-size: 24px; margin-bottom: 6px; }
        .type-btn .label { font-size: 13px; font-weight: 600; }
        .type-btn:has(input:checked) { border-color: #0e89d8; background: #eff8ff; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 9px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-primary { background: linear-gradient(130deg, #1ba84a, #0e89d8); color: #fff; width: 100%; justify-content: center; }
        .btn-back { background: #f0f5fa; color: #2a5070; border: 1px solid var(--line); margin-bottom: 16px; }
    </style>

    <div style="padding: 16px">
        <a href="{{ route('ai.index') }}" class="btn btn-back">← Wróć</a>

        <div class="create-card">
            <h2>🤖 Nowa rozmowa z AI</h2>
            <p>Asystent pomoże Ci zebrać dane, przeprowadzić analizę lub przygotować ofertę.</p>

            <form method="POST" action="{{ route('ai.store') }}">
                @csrf

                <div class="form-group">
                    <label>Wybierz typ rozmowy</label>
                    <div class="type-grid">
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="general"
                                {{ (empty($contextType) || $contextType === 'general' ? 'checked' : '') }}>
                            <div class="icon">💬</div>
                            <div class="label">Ogólnie</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="compressor_room"
                                {{ ($contextType === 'compressor_room' ? 'checked' : '') }}>
                            <div class="icon">🔧</div>
                            <div class="label">Sprężarkownia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="boiler_room"
                                {{ ($contextType === 'boiler_room' ? 'checked' : '') }}>
                            <div class="icon">🔥</div>
                            <div class="label">Kotłownia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="drying_room"
                                {{ ($contextType === 'drying_room' ? 'checked' : '') }}>
                            <div class="icon">🌡️</div>
                            <div class="label">Suszarnia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="buildings"
                                {{ ($contextType === 'buildings' ? 'checked' : '') }}>
                            <div class="icon">🏢</div>
                            <div class="label">Budynki</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="technological_processes"
                                {{ ($contextType === 'technological_processes' ? 'checked' : '') }}>
                            <div class="icon">⚙️</div>
                            <div class="label">Procesy technologiczne</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="energy_audit"
                                {{ ($contextType === 'energy_audit' ? 'checked' : '') }}>
                            <div class="icon">⚡</div>
                            <div class="label">Audyt energetyczny</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="iso50001"
                                {{ ($contextType === 'iso50001' ? 'checked' : '') }}>
                            <div class="icon">🏭</div>
                            <div class="label">ISO 50001</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="offer"
                                {{ ($contextType === 'offer' ? 'checked' : '') }}>
                            <div class="icon">📄</div>
                            <div class="label">Oferta</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_general"
                                {{ ($contextType === 'bc_general' ? 'checked' : '') }}>
                            <div class="icon">📋</div>
                            <div class="label">BC Ogólnie</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_compressor_room"
                                {{ ($contextType === 'bc_compressor_room' ? 'checked' : '') }}>
                            <div class="icon">🔧</div>
                            <div class="label">BC Sprężarkownia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_boiler_room"
                                {{ ($contextType === 'bc_boiler_room' ? 'checked' : '') }}>
                            <div class="icon">🔥</div>
                            <div class="label">BC Kotłownia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_drying_room"
                                {{ ($contextType === 'bc_drying_room' ? 'checked' : '') }}>
                            <div class="icon">🌡️</div>
                            <div class="label">BC Suszarnia</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_buildings"
                                {{ ($contextType === 'bc_buildings' ? 'checked' : '') }}>
                            <div class="icon">🏢</div>
                            <div class="label">BC Budynki</div>
                        </label>
                        <label class="type-btn">
                            <input type="radio" name="context_type" value="bc_technological_processes"
                                {{ ($contextType === 'bc_technological_processes' ? 'checked' : '') }}>
                            <div class="icon">⚙️</div>
                            <div class="label">BC Procesy technologiczne</div>
                        </label>
                    </div>
                </div>

                @if($contextId)
                    <input type="hidden" name="context_id" value="{{ $contextId }}">
                @endif

                <button type="submit" class="btn btn-primary">Rozpocznij rozmowę</button>
            </form>
        </div>
    </div>
</x-layouts.app>
