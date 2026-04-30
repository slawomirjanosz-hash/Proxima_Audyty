{{--
    Floating chat widget — shows on audit/questionnaire pages for clients.
    Props:
      $chatMessages – Collection of ClientChatMessage
      $companyId    – int (company_id)
--}}
@props(['chatMessages' => collect(), 'companyId' => null])

@php $isClient = auth()->user()?->isClient(); @endphp

@if($isClient && $companyId)
<style>
    /* ── Floating chat widget ─────────────────────────────── */
    .fcw-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1200;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0e89d8, #1ba84a);
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 18px rgba(14,137,216,.45);
        transition: transform .18s, box-shadow .18s;
        outline: none;
    }
    .fcw-btn:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(14,137,216,.55); }
    .fcw-unread-dot {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid #fff;
        animation: fcw-pulse 1.6s ease-in-out infinite;
    }
    @keyframes fcw-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.3)} }

    /* ── Panel ─────────────────────────────────────────────── */
    .fcw-panel {
        position: fixed;
        bottom: 88px;
        right: 24px;
        z-index: 1200;
        width: 330px;
        max-height: 480px;
        background: #fff;
        border: 1px solid #d5e0ea;
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(14,55,85,.22);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: fcw-slide-in .2s ease;
    }
    @keyframes fcw-slide-in { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
    .fcw-head {
        padding: 12px 16px;
        background: linear-gradient(135deg, #0e89d8, #0a5f9e);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-shrink: 0;
    }
    .fcw-head-title { font-size: 14px; font-weight: 800; letter-spacing: .2px; }
    .fcw-head-sub { font-size: 11px; opacity: .8; margin-top: 1px; }
    .fcw-close {
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
    .fcw-close:hover { background: rgba(255,255,255,.35); }
    .fcw-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #f8fbfd;
    }
    .fcw-bw { display: flex; flex-direction: column; }
    .fcw-bw.from-admin { align-items: flex-end; }
    .fcw-bw.from-client { align-items: flex-start; }
    .fcw-bubble {
        max-width: 82%;
        padding: 7px 11px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .fcw-bubble.from-admin { background: #0f2330; color: #fff; border-bottom-right-radius: 3px; }
    .fcw-bubble.from-client { background: #fff; border: 1px solid #d5e0ea; color: #1a2e3d; border-bottom-left-radius: 3px; }
    .fcw-meta { font-size: 10px; color: #8aa3b5; margin-top: 2px; }
    .fcw-empty { text-align: center; color: #9ab4c5; font-size: 13px; padding: 20px 10px; }
    .fcw-footer {
        padding: 10px 12px;
        background: #fff;
        border-top: 1px solid #e4edf3;
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-shrink: 0;
    }
    .fcw-textarea {
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
    .fcw-textarea:focus { border-color: #0e89d8; background: #fff; }
    .fcw-send {
        background: #0e89d8;
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
    .fcw-send:hover:not(:disabled) { background: #0a6faf; }
    .fcw-send:disabled { opacity: .5; cursor: not-allowed; }

    @media (max-width: 600px) {
        .fcw-panel { width: calc(100vw - 16px); right: 8px; bottom: 80px; }
    }
</style>

{{-- Toggle button --}}
<button class="fcw-btn" id="fcw-btn" onclick="fcwToggle()" title="Chat z audytorem" aria-label="Otwórz chat z audytorem">
    💬
    <span class="fcw-unread-dot" id="fcw-unread-dot" style="display:none;"></span>
</button>

{{-- Chat panel (hidden initially) --}}
<div class="fcw-panel" id="fcw-panel" style="display:none;" role="dialog" aria-label="Chat z zespołem ENESA">
    <div class="fcw-head">
        <div>
            <div class="fcw-head-title">💬 Chat z zespołem ENESA</div>
            <div class="fcw-head-sub">Masz pytanie? Napisz do nas.</div>
        </div>
        <button class="fcw-close" onclick="fcwToggle()" aria-label="Zamknij chat">✕</button>
    </div>
    <div class="fcw-messages" id="fcw-messages">
        @forelse($chatMessages as $msg)
            <div class="fcw-bw {{ $msg->is_from_admin ? 'from-admin' : 'from-client' }}" data-msg-id="{{ $msg->id }}">
                <div class="fcw-bubble {{ $msg->is_from_admin ? 'from-admin' : 'from-client' }}">{{ $msg->message }}</div>
                <div class="fcw-meta">{{ $msg->is_from_admin ? 'ENESA' : 'Ty' }} · {{ $msg->created_at->format('d.m.Y H:i') }}</div>
            </div>
        @empty
            <div class="fcw-empty" id="fcw-empty">Brak wiadomości. Napisz do nas!</div>
        @endforelse
    </div>
    <div class="fcw-footer">
        <textarea class="fcw-textarea" id="fcw-input" rows="2" placeholder="Napisz wiadomość..." aria-label="Treść wiadomości"></textarea>
        <button class="fcw-send" id="fcw-send-btn" type="button" onclick="fcwSend()">Wyślij</button>
    </div>
</div>

<script>
(function () {
    const csrf    = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const panel   = document.getElementById('fcw-panel');
    const messagesEl = document.getElementById('fcw-messages');
    const inputEl = document.getElementById('fcw-input');
    const sendBtn = document.getElementById('fcw-send-btn');
    const dot     = document.getElementById('fcw-unread-dot');

    let lastId    = {{ $chatMessages->last()?->id ?? 0 }};
    let isOpen    = false;
    let hasNew    = false;

    window.fcwToggle = function () {
        isOpen = !isOpen;
        panel.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
            dot.style.display = 'none';
            hasNew = false;
            inputEl.focus();
        }
    };

    function escHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function appendBubble(msg) {
        const empty = document.getElementById('fcw-empty');
        if (empty) empty.remove();

        const wrap = document.createElement('div');
        const side = msg.is_from_admin ? 'from-admin' : 'from-client';
        wrap.className = 'fcw-bw ' + side;
        wrap.dataset.msgId = msg.id;
        const label = msg.is_from_admin ? 'ENESA' : 'Ty';
        wrap.innerHTML =
            '<div class="fcw-bubble ' + side + '">' + escHtml(msg.message) + '</div>' +
            '<div class="fcw-meta">' + label + ' &middot; ' + escHtml(msg.created_at) + '</div>';
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    window.fcwSend = function () {
        const text = inputEl.value.trim();
        if (!text) return;
        sendBtn.disabled = true;

        fetch('{{ route('client.chat.send.ajax') }}', {
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
            if (data.id) {
                appendBubble(data);
                lastId = Math.max(lastId, data.id);
                inputEl.value = '';
            }
        })
        .catch(() => {})
        .finally(() => { sendBtn.disabled = false; inputEl.focus(); });
    };

    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); fcwSend(); }
    });

    // Poll for new messages every 5s
    function pollChat() {
        fetch('{{ route('client.chat.poll') }}?after=' + lastId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.messages || !data.messages.length) return;
            let hasAdmin = false;
            data.messages.forEach(function (msg) {
                if (msg.id > lastId) {
                    appendBubble(msg);
                    lastId = msg.id;
                    if (msg.is_from_admin) hasAdmin = true;
                }
            });
            if (hasAdmin && !isOpen) {
                dot.style.display = 'block';
                hasNew = true;
            }
        })
        .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', function () {
        messagesEl.scrollTop = messagesEl.scrollHeight;
        setInterval(pollChat, 5000);
    });
})();
</script>
@endif
