<x-layouts.app>
<div class="panel">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h2 style="margin:0;font-size:20px;">Ustawienia numeracji ofert</h2>
        <a href="{{ route('offers.index') }}" style="padding:8px 16px;background:#718096;color:#fff;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px;">← Wróć</a>
    </div>

    @if(session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('offers.settings.save') }}">
        @csrf

        <div style="background:#fff;border:1px solid #d5e0ea;border-radius:12px;padding:20px;margin-bottom:16px;">
            <h3 style="margin:0 0 16px;font-size:16px;">Format numeru oferty</h3>
            <p style="color:#4c6373;font-size:13px;margin:0 0 16px;">Zdefiniuj jak będą generowane numery ofert. Możesz użyć do 4 elementów oddzielonych separatorami.</p>

            @php
                $elTypes = ['empty' => '-- puste --', 'text' => 'Tekst stały', 'number' => 'Numer kolejny', 'year' => 'Rok', 'month' => 'Miesiąc', 'date' => 'Data (YYYY-MM-DD)', 'time' => 'Czas (HHmm)'];
                $seps = ['-' => '-', '/' => '/', '_' => '_', '.' => '.', '\\' => '\\'];
            @endphp

            @for($i = 1; $i <= 4; $i++)
            @php($typeVal = $settings ? $settings->{'element'.$i.'_type'} : ($i===1?'text':($i===2?'number':'empty')))
            @php($textVal = $settings ? $settings->{'element'.$i.'_value'} : '')
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;padding:12px;background:#f3f8f7;border-radius:10px;">
                <span style="font-size:13px;font-weight:700;color:#4c6373;min-width:60px;">Element {{ $i }}</span>
                <select name="element{{ $i }}_type" class="offer-el-select" data-index="{{ $i }}"
                    style="padding:8px 10px;border-radius:9px;border:1px solid #c9d7e3;font-size:14px;min-width:180px;" onchange="updateElementPreview()">
                    @foreach($elTypes as $val => $label)
                        <option value="{{ $val }}" {{ $typeVal === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="element{{ $i }}_value" value="{{ $textVal }}"
                    style="padding:8px 10px;border-radius:9px;border:1px solid #c9d7e3;font-size:14px;width:150px;"
                    placeholder="Wartość (dla tekstu)"
                    id="element{{ $i }}-value-input">

                @if($i < 4)
                @php($sepVal = $settings ? $settings->{'separator'.$i} : '-')
                <span style="font-size:13px;color:#4c6373;">Separator {{ $i }}:</span>
                <select name="separator{{ $i }}" style="padding:8px 10px;border-radius:9px;border:1px solid #c9d7e3;font-size:14px;width:80px;" onchange="updateElementPreview()">
                    @foreach($seps as $val => $label)
                        <option value="{{ $val }}" {{ $sepVal === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            @endfor

            <div style="margin-bottom:16px;padding:12px;background:#f3f8f7;border-radius:10px;display:flex;align-items:center;gap:12px;">
                <span style="font-size:13px;font-weight:700;color:#4c6373;min-width:60px;">Numer startowy</span>
                <input type="number" name="start_number" value="{{ $settings->start_number ?? 1 }}" min="1"
                    style="padding:8px 10px;border-radius:9px;border:1px solid #c9d7e3;font-size:14px;width:120px;">
                <span style="font-size:13px;color:#4c6373;">— od jakiego numeru rozpocząć liczenie w bieżącym roku</span>
            </div>

            <div style="background:#fff;border:1px solid #d5e0ea;border-radius:10px;padding:14px;margin-bottom:16px;">
                <span style="font-size:13px;font-weight:700;color:#4c6373;">Podgląd numeru:</span>
                <span id="number-preview" style="font-size:18px;font-weight:800;margin-left:12px;color:#0f2330;">—</span>
            </div>
        </div>

        <button type="submit" style="padding:10px 22px;background:#1ba84a;color:#fff;border-radius:9px;border:0;cursor:pointer;font-size:14px;font-weight:700;">
            💾 Zapisz ustawienia
        </button>
    </form>
</div>

<script>
function updateElementPreview() {
    const parts = [];
    const seps  = [];
    for (let i = 1; i <= 3; i++) {
        const sel = document.querySelector(`select[name="separator${i}"]`);
        seps.push(sel ? sel.value : '-');
    }
    for (let i = 1; i <= 4; i++) {
        const typeSel = document.querySelector(`select[name="element${i}_type"]`);
        const valInp  = document.getElementById(`element${i}-value-input`);
        if (!typeSel) continue;
        const type = typeSel.value;
        const val  = valInp ? valInp.value : '';
        let preview = null;
        switch (type) {
            case 'text':   preview = val || '(tekst)'; break;
            case 'number': preview = '0001'; break;
            case 'year':   preview = new Date().getFullYear().toString(); break;
            case 'month':  preview = String(new Date().getMonth() + 1).padStart(2, '0'); break;
            case 'date':
                const d = new Date();
                preview = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
                break;
            case 'time':
                const t = new Date();
                preview = String(t.getHours()).padStart(2,'0') + String(t.getMinutes()).padStart(2,'0');
                break;
            default: preview = null;
        }
        if (preview !== null) {
            if (parts.length > 0 && i > 1 && seps[i - 2]) {
                parts.push(seps[i - 2]);
            }
            parts.push(preview);
        }
    }
    document.getElementById('number-preview').textContent = parts.join('') || '—';
}
document.addEventListener('DOMContentLoaded', updateElementPreview);
</script>
</x-layouts.app>
