<x-layouts.app>
<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h2 style="margin:0 0 4px;font-size:22px;">Szablony ofert</h2>
            <p style="margin:0;font-size:13px;color:var(--ink-mute);">Każdy szablon definiuje strukturę HTML oferty, domyślne stawki i godziny audytorów.</p>
        </div>
        <a href="{{ route('offer-templates.create') }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:10px;background:var(--green-primary);color:#fff;text-decoration:none;font-weight:600;font-size:14px;">
            + Nowy szablon
        </a>
    </div>

    @if(session('status'))
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#166534;font-weight:600;">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#991b1b;font-weight:600;">
            {{ session('error') }}
        </div>
    @endif

    @if($templates->isEmpty())
        <div style="background:#f3f8f7;border:1px dashed #c3ddd4;border-radius:14px;padding:48px;text-align:center;color:var(--ink-mute);">
            <div style="font-size:40px;margin-bottom:12px;">📄</div>
            <div style="font-size:16px;font-weight:600;margin-bottom:8px;">Brak szablonów ofert</div>
            <div style="font-size:13px;margin-bottom:20px;">Stwórz pierwszy szablon — każdy typ audytu może mieć własny wygląd i domyślne stawki.</div>
            <a href="{{ route('offer-templates.create') }}" style="background:var(--green-primary);color:#fff;padding:10px 22px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
                + Utwórz pierwszy szablon
            </a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;">
            @foreach($templates as $tpl)
            <div style="background:#fff;border:1px solid var(--paper-deep);border-radius:14px;padding:0;overflow:hidden;">
                <div style="background:linear-gradient(135deg,#1A4D3A,#2d7a5f);padding:18px 20px;color:#fff;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                        <div>
                            <div style="font-size:18px;font-weight:700;">{{ $tpl->name }}</div>
                            <div style="font-size:12px;opacity:.7;margin-top:3px;font-family:monospace;">{{ $tpl->type_code }}</div>
                        </div>
                        <div style="display:flex;gap:6px;">
                            @if($tpl->is_active)
                                <span style="background:rgba(255,255,255,.2);color:#fff;font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;">Aktywny</span>
                            @else
                                <span style="background:rgba(0,0,0,.2);color:#fff;font-size:11px;padding:3px 10px;border-radius:20px;">Nieaktywny</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="padding:16px 20px;">
                    @if($tpl->description)
                        <p style="font-size:13px;color:var(--ink-mute);margin:0 0 14px;line-height:1.6;">{{ Str::limit($tpl->description, 100) }}</p>
                    @endif
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px;">
                        <div style="background:#f3f8f7;border-radius:8px;padding:10px;text-align:center;">
                            <div style="font-size:11px;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Stawka km</div>
                            <div style="font-size:15px;font-weight:700;color:#1A4D3A;">{{ number_format($tpl->default_km_rate, 2, ',', ' ') }} zł</div>
                        </div>
                        <div style="background:#f3f8f7;border-radius:8px;padding:10px;text-align:center;">
                            <div style="font-size:11px;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Stawka h</div>
                            <div style="font-size:15px;font-weight:700;color:#1A4D3A;">{{ number_format($tpl->default_hour_rate, 2, ',', ' ') }} zł</div>
                        </div>
                        <div style="background:#f3f8f7;border-radius:8px;padding:10px;text-align:center;">
                            <div style="font-size:11px;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">Godziny</div>
                            <div style="font-size:15px;font-weight:700;color:#1A4D3A;">{{ number_format($tpl->default_auditor_hours, 1, ',', ' ') }} h</div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--ink-mute);">
                        <span>{{ $tpl->offers_count }} {{ $tpl->offers_count === 1 ? 'oferta' : ($tpl->offers_count < 5 ? 'oferty' : 'ofert') }}</span>
                        <span>{{ $tpl->has_html = !empty($tpl->html_content) ? '✔ HTML zdefiniowany' : '⚠ Brak HTML' }}</span>
                    </div>
                </div>
                <div style="padding:12px 20px;border-top:1px solid var(--paper-deep);display:flex;gap:8px;">
                    <a href="{{ route('offer-templates.edit', $tpl) }}" style="flex:1;text-align:center;padding:8px;border-radius:8px;background:#f3f8f7;color:#1A4D3A;text-decoration:none;font-weight:600;font-size:13px;">
                        ✏️ Edytuj
                    </a>
                    <a href="{{ route('offer-templates.preview', $tpl) }}" target="_blank" style="padding:8px 14px;border-radius:8px;background:#eff6ff;color:#1d4ed8;text-decoration:none;font-weight:600;font-size:13px;">
                        👁 Podgląd
                    </a>
                    @if(!$tpl->offers_count)
                    <form method="POST" action="{{ route('offer-templates.destroy', $tpl) }}" onsubmit="return confirm('Usunąć szablon {{ addslashes($tpl->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:8px 12px;border-radius:8px;background:#fee2e2;color:#991b1b;border:0;cursor:pointer;font-weight:600;font-size:13px;">🗑</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
</x-layouts.app>
