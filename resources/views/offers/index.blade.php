<x-layouts.app>
<div class="panel">
    <h2 style="margin:0 0 20px; font-size:22px;">Oferty</h2>
    <div style="display:flex; flex-wrap:wrap; gap:12px;">
        <a href="{{ route('offers.portfolio') }}" style="display:flex;align-items:center;gap:8px;padding:14px 22px;background:#0e89d8;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">
            📁 Portfolio
        </a>
        <a href="{{ route('offers.create') }}" style="display:flex;align-items:center;gap:8px;padding:14px 22px;background:#1ba84a;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">
            ➕ Nową Ofertę
        </a>
        <a href="{{ route('offers.inprogress') }}" style="display:flex;align-items:center;gap:8px;padding:14px 22px;background:#d97706;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">
            ⏳ W toku
        </a>
        <a href="{{ route('offers.archived') }}" style="display:flex;align-items:center;gap:8px;padding:14px 22px;background:#718096;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">
            🗄️ Zarchiwizowane
        </a>
        <a href="{{ route('offers.settings') }}" style="display:flex;align-items:center;gap:8px;padding:14px 22px;background:#4c6373;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:15px;">
            ⚙️ Ustawienia
        </a>
    </div>
</div>
</x-layouts.app>
