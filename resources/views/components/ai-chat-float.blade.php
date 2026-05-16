{{--
    Floating AI Agent chat widget — shown on audit/questionnaire pages.
    Props:
      $contextType – string (default: 'general')
      $contextId   – int|null (audit id for context-aware conversations)
--}}
@props(['contextType' => 'general', 'contextId' => null])

@php $user = auth()->user(); @endphp
@if($user)
<style>
    /* ── AI floating chat widget ──────────────────────────── */
    .aicw-btn {
        position: fixed;
        bottom: 24px;
        right: 88px;
        z-index: 1200;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1A4D3A, #2e7d55);
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 18px rgba(26,77,58,.45);
        transition: transform .18s, box-shadow .18s;
        outline: none;
    }
    .aicw-btn:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(26,77,58,.55); }

    .aicw-panel {
        position: fixed;
        bottom: 88px;
        right: 88px;
        z-index: 1200;
        width: 340px;
        max-height: 500px;
        background: #fff;
        border: 1px solid #c8d8e6;
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(14,55,85,.22);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: aicw-slide-in .2s ease;
    }
    @keyframes aicw-slide-in { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }

    .aicw-head {
        padding: 12px 16px;
        background: linear-gradient(135deg, #1A4D3A, #2e7d55);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-shrink: 0;
    }
    .aicw-head-title { font-size: 14px; font-weight: 800; letter-spacing: .2px; }
    .aicw-head-sub   { font-size: 11px; opacity: .8; margin-top: 1px; }
    .aicw-close {
        background: rgba(255,255,255,.2);
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
        flex-shrink: 0;
    }
    .aicw-close:hover { background: rgba(255,255,255,.35); }

    .aicw-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #f4f9f5;
    }
    .aicw-bw { display: flex; flex-direction: column; }
    .aicw-bw.from-ai     { align-items: flex-start; }
    .aicw-bw.from-user   { align-items: flex-end; }
    .aicw-bubble {
        max-width: 85%;
        padding: 8px 12px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .aicw-bubble.from-ai   { background: #fff; border: 1px solid #d1e7d8; color: #0f2330; border-bottom-left-radius: 3px; }
    .aicw-bubble.from-user { background: #1A4D3A; color: #fff; border-bottom-right-radius: 3px; }
    .aicw-meta { font-size: 10px; color: #8aa3b5; margin-top: 2px; }
    .aicw-empty { text-align: center; color: #9ab4c5; font-size: 13px; padding: 20px 10px; }

    .aicw-typing { display: flex; gap: 4px; padding: 8px 12px; align-items: center; }
    .aicw-typing span {
        width: 7px; height: 7px; background: #2e7d55; border-radius: 50%;
        animation: aicw-bounce 1.2s ease-in-out infinite;
    }
    .aicw-typing span:nth-child(2) { animation-delay: .2s; }
    .aicw-typing span:nth-child(3) { animation-delay: .4s; }
    @keyframes aicw-bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

    .aicw-footer {
        padding: 10px 12px;
        background: #fff;
        border-top: 1px solid #e4edf3;
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-shrink: 0;
    }
    .aicw-textarea {
        flex: 1;
        resize: none;
        border: 1px solid #c8dce9;
        border-radius: 9px;
        padding: 8px 10px;
        font-size: 13px;
        font-family: inherit;
        color: #0f2330;
        background: #f9fbfd;
        outline: none;
        transition: border-color .15s;
        max-height: 80px;
    }
    .aicw-textarea:focus { border-color: #2e7d55; background: #fff; }
    .aicw-send {
        background: #1A4D3A;
        color: #fff;
        border: none;
        border-radius: 9px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        flex-shrink: 0;
        transition: background .15s;
    }
    .aicw-send:hover:not(:disabled) { background: #2e7d55; }
    .aicw-send:disabled { opacity: .5; cursor: not-allowed; }
    .aicw-init-msg { text-align:center; font-size:12px; color:#6b7280; padding:16px 12px; }

    @media (max-width:600px) {
        .aicw-panel { width: calc(100vw - 16px); right: 8px; bottom: 150px; }
        .aicw-btn   { right: 88px; }
    }
</style>

{{-- Toggle button --}}
<button class="aicw-btn" id="aicw-btn" onclick="aicwToggle()" title="Chat z Agentem AI" aria-label="Otwórz chat z Agentem AI">
    🤖
</button>

{{-- Panel --}}
<div class="aicw-panel" id="aicw-panel" style="display:none;" role="dialog" aria-label="Chat z Agentem AI">
    <div class="aicw-head">
        <div>
            <div class="aicw-head-title">🤖 Agent AI — ENESA</div>
            <div class="aicw-head-sub">Zadaj pytanie dotyczące audytu</div>
        </div>
        <button class="aicw-close" onclick="aicwToggle()" aria-label="Zamknij chat">✕</button>
    </div>
    <div class="aicw-messages" id="aicw-messages">
        <div class="aicw-init-msg" id="aicw-init-msg">Wczytuję rozmowę…</div>
    </div>
    <div class="aicw-footer">
        <textarea class="aicw-textarea" id="aicw-input" rows="2"
            placeholder="Napisz do Agenta…" aria-label="Treść wiadomości" disabled></textarea>
        <button class="aicw-send" id="aicw-send-btn" type="button" onclick="aicwSend()" disabled>Wyślij</button>
    </div>
</div>

<script>
(function () {
    const csrf        = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const panel       = document.getElementById('aicw-panel');
    const messagesEl  = document.getElementById('aicw-messages');
    const inputEl     = document.getElementById('aicw-input');
    const sendBtn     = document.getElementById('aicw-send-btn');
    const initMsg     = document.getElementById('aicw-init-msg');
    const contextType = '{{ $contextType }}';
    const contextId   = {{ $contextId ? (int)$contextId : 'null' }};

    let convId    = null;
    let isOpen    = false;
    let inited    = false;
    let sending   = false;

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function appendBubble(role, content, ts) {
        const side = role === 'user' ? 'from-user' : 'from-ai';
        const label = role === 'user' ? 'Ty' : 'Agent AI';
        const wrap = document.createElement('div');
        wrap.className = 'aicw-bw ' + side;
        wrap.innerHTML =
            '<div class="aicw-bubble ' + side + '">' + escHtml(content) + '</div>' +
            '<div class="aicw-meta">' + label + (ts ? ' &middot; ' + escHtml(ts) : '') + '</div>';
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return wrap;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.id = 'aicw-typing';
        el.className = 'aicw-bw from-ai';
        el.innerHTML = '<div class="aicw-bubble from-ai aicw-typing"><span></span><span></span><span></span></div>';
        messagesEl.appendChild(el);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function removeTyping() {
        const el = document.getElementById('aicw-typing');
        if (el) el.remove();
    }

    function initConversation() {
        if (inited) return;
        inited = true;

        const body = { context_type: contextType };
        if (contextId) body.context_id = contextId;

        fetch('{{ route('ai.store.ajax') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { initMsg.textContent = 'Błąd inicjalizacji: ' + data.error; return; }
            convId = data.conversation_id;
            if (initMsg) initMsg.remove();

            if (data.messages && data.messages.length) {
                data.messages.forEach(m => appendBubble(m.role, m.content, m.created_at));
            } else {
                appendBubble('assistant', 'Cześć! Jestem Agentem AI ENESA. W czym mogę pomóc?', '');
            }

            inputEl.disabled = false;
            sendBtn.disabled = false;
            inputEl.focus();
        })
        .catch(() => {
            if (initMsg) initMsg.textContent = 'Nie udało się połączyć z Agentem.';
        });
    }

    window.aicwToggle = function () {
        isOpen = !isOpen;
        panel.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            initConversation();
            messagesEl.scrollTop = messagesEl.scrollHeight;
            if (!inputEl.disabled) inputEl.focus();
        }
    };

    window.aicwSend = function () {
        if (!convId || sending) return;
        const text = inputEl.value.trim();
        if (!text) return;

        sending = true;
        sendBtn.disabled = true;
        inputEl.value = '';
        inputEl.disabled = true;

        appendBubble('user', text, '');
        showTyping();

        fetch('/ai/' + convId + '/wiadomosc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text }),
        })
        .then(r => r.json())
        .then(data => {
            removeTyping();
            if (data.success && data.response) {
                appendBubble('assistant', data.response, '');
            } else {
                appendBubble('assistant', data.error || 'Wystąpił błąd. Spróbuj ponownie.', '');
            }
        })
        .catch(() => {
            removeTyping();
            appendBubble('assistant', 'Błąd połączenia. Spróbuj ponownie.', '');
        })
        .finally(() => {
            sending = false;
            sendBtn.disabled = false;
            inputEl.disabled = false;
            inputEl.focus();
        });
    };

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); aicwSend(); }
    });
})();
</script>
@endif
