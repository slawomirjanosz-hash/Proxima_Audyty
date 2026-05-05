<div class="master-bottom-nav">
    @if($prev)
    <button class="btn-master-prev" type="button" data-goto-section="{{ $prev }}">← Poprzednia sekcja</button>
    @else
    <div></div>
    @endif
    <div style="font-size:12px; color:#8aa3b5; font-style:italic; text-align:center;">Dane zapisują się automatycznie</div>
    @if($next)
    <button class="btn-master-next" type="button" data-goto-section="{{ $next }}">Następna sekcja →</button>
    @else
    <button class="btn-master-next" type="button" onclick="document.getElementById('master-save-status').textContent='✓ Gotowe!'">✓ Zakończ ankietę</button>
    @endif
</div>
