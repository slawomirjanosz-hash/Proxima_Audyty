<x-layouts.app>
<style>
.at-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@media (max-width: 1100px) { .at-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 680px)  { .at-grid { grid-template-columns: 1fr; } }

.at-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); border: 1px solid #d9e8f3; background: #fff; display: flex; flex-direction: column; }
.at-card-header { padding: 20px 22px 16px; display: flex; align-items: center; gap: 14px; }
.at-card-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.at-card-title { font-size: 17px; font-weight: 800; }
.at-card-subtitle { font-size: 12px; margin-top: 2px; }
.at-card-body { padding: 0 16px 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; flex: 1; }
.at-action { display: flex; flex-direction: column; align-items: flex-start; gap: 4px; padding: 12px 14px; border-radius: 12px; text-decoration: none; border: 1px solid #e4edf5; background: #f8fbff; transition: background .15s, border-color .15s; }
.at-action:hover { background: #eef5ff; border-color: #b0ccde; }
.at-action-icon { font-size: 20px; }
.at-action-label { font-size: 13px; font-weight: 700; color: #163f5b; }
.at-action-desc { font-size: 11px; color: #677d8e; }

.at-card-energy .at-card-header { background: linear-gradient(135deg, #1A4D3A 0%, #2d7a5f 100%); color: #fff; }
.at-card-energy .at-card-subtitle { color: #a0d4be; }
.at-card-energy .at-card-icon { background: rgba(255,255,255,.15); }
.at-card-energy .at-action:hover { border-color: #1A4D3A; }

.at-card-iso .at-card-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #fff; }
.at-card-iso .at-card-subtitle { color: #bfdbfe; }
.at-card-iso .at-card-icon { background: rgba(255,255,255,.15); }
.at-card-iso .at-action:hover { border-color: #3b82f6; }

.at-card-bc .at-card-header { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: #fff; }
.at-card-bc .at-card-subtitle { color: #e9d5ff; }
.at-card-bc .at-card-icon { background: rgba(255,255,255,.15); }
.at-card-bc .at-action:hover { border-color: #a855f7; }

.at-all-offers { background: #fff; border: 1px solid #d9e8f3; border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.at-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; }
</style>

<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:22px;">
        <div>
            <h1 style="margin:0;font-size:22px;font-weight:800;">Rodzaje audytów</h1>
            <p style="margin:4px 0 0;font-size:13px;color:var(--ink-mute);">Zarządzaj szablonami, ofertami, ankietami i agentami AI dla każdego rodzaju audytu.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('offers.all') }}" style="padding:9px 16px;background:#1A4D3A;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;">📋 Wszystkie oferty</a>
            <a href="{{ route('audits.settings') }}" style="padding:9px 16px;background:#e2e8f0;color:#374151;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;">⚙ Ustawienia</a>
        </div>
    </div>

    @if(session('status'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:10px 16px;margin-bottom:16px;color:#065f46;font-size:13px;font-weight:600;">{{ session('status') }}</div>
    @endif

    <div class="at-grid">

        {{-- ── AUDYT ENERGETYCZNY ─────────────────────────────────────────── --}}
        <div class="at-card at-card-energy">
            <div class="at-card-header">
                <div class="at-card-icon">⚡</div>
                <div>
                    <div class="at-card-title">Audyt Energetyczny</div>
                    <div class="at-card-subtitle">Zakłady przemysłowe · Kompresory · Kotłownia · Budynki</div>
                </div>
            </div>
            <div class="at-card-body">
                <a href="{{ route('offer-templates.index', ['category' => 'energetyczny']) }}" class="at-action">
                    <span class="at-action-icon">📄</span>
                    <span class="at-action-label">Szablon oferty</span>
                    <span class="at-action-desc">Szablony dla audytów energetycznych</span>
                </a>
                <a href="{{ route('offers.all') }}?type=energetyczny" class="at-action">
                    <span class="at-action-icon">📋</span>
                    <span class="at-action-label">Oferty wystawione</span>
                    <span class="at-action-desc">Oferty audytu energetycznego</span>
                </a>
                <a href="{{ route('audits.types', 'energetyczne') }}" class="at-action">
                    <span class="at-action-icon">📝</span>
                    <span class="at-action-label">Ankieta / Dane</span>
                    <span class="at-action-desc">Formularze i kwestionariusze</span>
                </a>
                <a href="{{ route('ai.create') }}?context=energy" class="at-action">
                    <span class="at-action-icon">🤖</span>
                    <span class="at-action-label">Agent AI</span>
                    <span class="at-action-desc">Asystent AI dla audytu energetycznego</span>
                </a>
            </div>
        </div>

        {{-- ── AUDYT ISO 50001 ────────────────────────────────────────────── --}}
        <div class="at-card at-card-iso">
            <div class="at-card-header">
                <div class="at-card-icon">🏭</div>
                <div>
                    <div class="at-card-title">Audyt ISO 50001</div>
                    <div class="at-card-subtitle">System Zarządzania Energią · Norma ISO 50001</div>
                </div>
            </div>
            <div class="at-card-body">
                <a href="{{ route('offer-templates.index', ['category' => 'iso50001']) }}" class="at-action">
                    <span class="at-action-icon">📄</span>
                    <span class="at-action-label">Szablon oferty</span>
                    <span class="at-action-desc">Szablony dla audytów ISO 50001</span>
                </a>
                <a href="{{ route('offers.all') }}?type=iso" class="at-action">
                    <span class="at-action-icon">📋</span>
                    <span class="at-action-label">Oferty wystawione</span>
                    <span class="at-action-desc">Oferty audytu ISO 50001</span>
                </a>
                <a href="{{ route('audits.types', 'iso50001') }}" class="at-action">
                    <span class="at-action-icon">📝</span>
                    <span class="at-action-label">Ankieta / Dane</span>
                    <span class="at-action-desc">Kwestionariusz i etapy ISO</span>
                </a>
                <a href="{{ route('ai.create') }}?context=iso" class="at-action">
                    <span class="at-action-icon">🤖</span>
                    <span class="at-action-label">Agent AI</span>
                    <span class="at-action-desc">Asystent AI dla ISO 50001</span>
                </a>
            </div>
        </div>

        {{-- ── BIAŁE CERTYFIKATY ──────────────────────────────────────────── --}}
        <div class="at-card at-card-bc">
            <div class="at-card-header">
                <div class="at-card-icon">🏅</div>
                <div>
                    <div class="at-card-title">Białe Certyfikaty</div>
                    <div class="at-card-subtitle">Świadectwa efektywności energetycznej · URE</div>
                </div>
            </div>
            <div class="at-card-body">
                <a href="{{ route('offer-templates.index', ['category' => 'biale_certyfikaty']) }}" class="at-action">
                    <span class="at-action-icon">📄</span>
                    <span class="at-action-label">Szablon oferty</span>
                    <span class="at-action-desc">Szablony dla białych certyfikatów</span>
                </a>
                <a href="{{ route('offers.all') }}?type=bc" class="at-action">
                    <span class="at-action-icon">📋</span>
                    <span class="at-action-label">Oferty wystawione</span>
                    <span class="at-action-desc">Oferty białe certyfikaty</span>
                </a>
                <a href="{{ route('audits.types', 'biale-certyfikaty') }}" class="at-action">
                    <span class="at-action-icon">📝</span>
                    <span class="at-action-label">Ankieta / Dane</span>
                    <span class="at-action-desc">Formularze białych certyfikatów</span>
                </a>
                <a href="{{ route('ai.create') }}?context=bc" class="at-action">
                    <span class="at-action-icon">🤖</span>
                    <span class="at-action-label">Agent AI</span>
                    <span class="at-action-desc">Asystent AI dla białych certyfikatów</span>
                </a>
            </div>
        </div>

    </div>

    {{-- ── Skróty ─────────────────────────────────────────────────────────── --}}
    <div style="margin-top:24px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">

        <div class="at-all-offers">
            <div>
                <div style="font-size:16px;font-weight:800;color:#163f5b;">📋 Wszystkie oferty</div>
                <div style="font-size:12px;color:var(--ink-mute);margin-top:3px;">Przeglądaj wszystkie wystawione oferty niezależnie od rodzaju audytu</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('offers.all') }}" style="padding:8px 16px;background:#1A4D3A;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;">📋 Przeglądaj</a>
                <a href="{{ route('offers.create') }}" style="padding:8px 16px;background:#e2f0fb;color:#1A4D3A;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;">+ Nowa oferta</a>
            </div>
        </div>

        <div class="at-all-offers" style="border-color:#c7d2fe;">
            <div>
                <div style="font-size:16px;font-weight:800;color:#1e3a5f;">📄 Szablony globalne</div>
                <div style="font-size:12px;color:var(--ink-mute);margin-top:3px;">Biblioteka layoutów HTML ofert — skopiuj do zakładki audytowej</div>
            </div>
            <a href="{{ route('offer-templates.index', ['category' => 'global']) }}" style="padding:8px 16px;background:#1e3a5f;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;white-space:nowrap;">📄 Otwórz</a>
        </div>

        <div class="at-all-offers">
            <div>
                <div style="font-size:16px;font-weight:800;color:#163f5b;">🛠 Audyty w toku</div>
                <div style="font-size:12px;color:var(--ink-mute);margin-top:3px;">
                    W toku: <strong>{{ $inProgressCount }}</strong> &nbsp;·&nbsp; Zakończone: <strong>{{ $completedCount }}</strong>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('audits.index', ['tab' => 'in-progress']) }}" style="padding:8px 16px;background:#d97706;color:#fff;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;">🛠 W toku</a>
                <a href="{{ route('audits.index', ['tab' => 'new']) }}" style="padding:8px 16px;background:#e2f0fb;color:#1A4D3A;border-radius:9px;text-decoration:none;font-weight:700;font-size:13px;">+ Nowy audyt</a>
            </div>
        </div>

    </div>
</div>
</x-layouts.app>