{{--
    Floating chat widget — shows on client-related pages for admins/auditors.
    Props:
      $chatMessages – Collection of ClientChatMessage
      $company      – Company model
--}}
@props(['chatMessages' => collect(), 'company' => null])

@php
    $user = auth()->user();
    $showWidget = $company && $user && !$user->isClient();
    $clientUser = $company?->client;
@endphp

@if($showWidget)
<style>
    /* ── Admin floating chat widget ───────────────────────────── */
    .acw-btn {
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
    .acw-btn:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(14,137,216,.55); }
    .acw-btn.new-msg {
        animation: acw-shake 0.5s ease-in-out 4, acw-glow 1.2s ease-in-out 4;
    }
    @keyframes acw-shake {
        0%,100%{ transform:translateX(0) scale(1); }
        20%    { transform:translateX(-5px) scale(1.05); }
        40%    { transform:translateX(5px) scale(1.08); }
        60%    { transform:translateX(-4px) scale(1.05); }
        80%    { transform:translateX(4px) scale(1.03); }
    }
    @keyframes acw-glow {
        0%,100%{ box-shadow: 0 4px 18px rgba(14,137,216,.45); }
        50%    { box-shadow: 0 0 0 8px rgba(239,68,68,.25), 0 4px 24px rgba(239,68,68,.5); }
    }
    .acw-unread-dot {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid #fff;
        animation: acw-pulse 1.6s ease-in-out infinite;
    }
    @keyframes acw-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.3)} }

    .acw-panel {
        position: fixed;
        bottom: 88px;
        right: 24px;
        z-index: 1200;
        width: 340px;
        max-height: 500px;
        background: #fff;
        border: 1px solid #d5e0ea;
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(14,55,85,.22);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: acw-slide-in .2s ease;
    }
    @keyframes acw-slide-in { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }

    .acw-head {
        padding: 12px 16px;
        background: linear-gradient(135deg, #0f2330, #1a3d55);
        color: #fff;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        flex-shrink: 0;
    }
    .acw-head-company { font-size: 14px; font-weight: 800; letter-spacing: .2px; line-height: 1.3; }
    .acw-head-user {
        font-size: 11px;
        opacity: .75;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .acw-head-user span { background: rgba(255,255,255,.15); border-radius: 4px; padding: 1px 6px; }
    .acw-close {
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
    .acw-close:hover { background: rgba(255,255,255,.35); }

    .acw-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #f8fbfd;
    }
    .acw-bw { display: flex; flex-direction: column; }
    .acw-bw.from-admin { align-items: flex-end; }
    .acw-bw.from-client { align-items: flex-start; }
    .acw-bubble {
        max-width: 82%;
        padding: 7px 11px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .acw-bubble.from-admin { background: #0f2330; color: #fff; border-bottom-right-radius: 3px; }
    .acw-bubble.from-client { background: #fff; border: 1px solid #d5e0ea; color: #1a2e3d; border-bottom-left-radius: 3px; }
    .acw-meta { font-size: 10px; color: #8aa3b5; margin-top: 2px; }
    .acw-empty { text-align: center; color: #9ab4c5; font-size: 13px; padding: 20px 10px; }

    .acw-footer {
        padding: 10px 12px;
        background: #fff;
        border-top: 1px solid #e4edf3;
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-shrink: 0;
    }
    .acw-textarea {
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
    .acw-textarea:focus { border-color: #0e89d8; background: #fff; }
    .acw-send {
        background: #0f2330;
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
    .acw-send:hover:not(:disabled) { background: #0e89d8; }
    .acw-send:disabled { opacity: .5; cursor: not-allowed; }

    @media (max-width: 600px) {
        .acw-panel { width: calc(100vw - 16px); right: 8px; bottom: 80px; }
    }
</style>

{{-- Toggle button --}}
<button class="acw-btn" id="acw-btn" onclick="acwToggle()" title="Chat z klientem" aria-label="Otwórz chat z klientem">
    💬
    <span class="acw-unread-dot" id="acw-unread-dot" style="display:none;"></span>
</button>

{{-- Chat panel --}}
<div class="acw-panel" id="acw-panel" style="display:none;" role="dialog" aria-label="Chat z klientem">
    <div class="acw-head">
        <div>
            <div class="acw-head-company">{{ $company->name }}</div>
            <div class="acw-head-user">
                👤 <span>{{ $clientUser?->name ?? 'Brak klienta' }}</span>
                @if($clientUser?->email)
                    &nbsp;· {{ $clientUser->email }}
                @endif
            </div>
        </div>
        <button class="acw-close" onclick="acwToggle()" aria-label="Zamknij chat">✕</button>
    </div>
    <div class="acw-messages" id="acw-messages">
        @forelse($chatMessages as $msg)
            <div class="acw-bw {{ $msg->is_from_admin ? 'from-admin' : 'from-client' }}" data-msg-id="{{ $msg->id }}">
                <div class="acw-bubble {{ $msg->is_from_admin ? 'from-admin' : 'from-client' }}">{{ $msg->message }}</div>
                <div class="acw-meta">
                    {{ $msg->is_from_admin ? ($msg->user?->name ?? 'Admin') : ($clientUser?->name ?? 'Klient') }}
                    · {{ $msg->created_at->format('d.m.Y H:i') }}
                </div>
            </div>
        @empty
            <div class="acw-empty" id="acw-empty">Brak wiadomości z tym klientem.</div>
        @endforelse
    </div>
    <div class="acw-footer">
        <textarea class="acw-textarea" id="acw-input" rows="2" placeholder="Napisz do klienta..." aria-label="Treść wiadomości"></textarea>
        <button class="acw-send" id="acw-send-btn" type="button" onclick="acwSend()">Wyślij</button>
    </div>
</div>

<script>
(function () {
    const csrf    = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const panel   = document.getElementById('acw-panel');
    const messagesEl = document.getElementById('acw-messages');
    const inputEl = document.getElementById('acw-input');
    const sendBtn = document.getElementById('acw-send-btn');
    const dot     = document.getElementById('acw-unread-dot');

    let lastId = {{ $chatMessages->last()?->id ?? 0 }};
    let isOpen = false;

    // Show dot if there are unread client messages on load
    @php
        $unreadCount = $chatMessages->where('is_from_admin', false)->whereNull('read_at')->count();
    @endphp
    @if($unreadCount > 0)
    dot.style.display = 'block';
    @endif

    window.acwToggle = function () {
        isOpen = !isOpen;
        panel.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
            dot.style.display = 'none';
            inputEl.focus();
        }
    };

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function appendBubble(msg) {
        const empty = document.getElementById('acw-empty');
        if (empty) empty.remove();

        const wrap = document.createElement('div');
        const side = msg.is_from_admin ? 'from-admin' : 'from-client';
        wrap.className = 'acw-bw ' + side;
        wrap.dataset.msgId = msg.id;
        const label = msg.is_from_admin
            ? escHtml(msg.user_name ?? 'Admin')
            : escHtml('{{ $clientUser?->name ?? 'Klient' }}');
        wrap.innerHTML =
            '<div class="acw-bubble ' + side + '">' + escHtml(msg.message) + '</div>' +
            '<div class="acw-meta">' + label + ' &middot; ' + escHtml(msg.created_at) + '</div>';
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    window.acwSend = function () {
        const text = inputEl.value.trim();
        if (!text) return;
        sendBtn.disabled = true;

        fetch('{{ route('chat.admin.send.ajax', $company) }}', {
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
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); acwSend(); }
    });

    // Poll every 5s for new messages from client
    function pollChat() {
        fetch('{{ route('chat.admin.poll', $company) }}?after=' + lastId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.messages || !data.messages.length) return;
            let hasClient = false;
            data.messages.forEach(function (msg) {
                if (msg.id > lastId) {
                    appendBubble(msg);
                    lastId = msg.id;
                    if (!msg.is_from_admin) hasClient = true;
                }
            });
            if (hasClient && !isOpen) {
                dot.style.display = 'block';
                // Shake + glow the button
                const btn = document.getElementById('acw-btn');
                if (btn) {
                    btn.classList.remove('new-msg');
                    void btn.offsetWidth; // force reflow
                    btn.classList.add('new-msg');
                    btn.addEventListener('animationend', function () {
                        btn.classList.remove('new-msg');
                    }, { once: true });
                }
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
