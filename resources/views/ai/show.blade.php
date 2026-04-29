<x-layouts.app>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .chat-layout { display: flex; flex-direction: column; height: calc(100vh - 80px); }
        .chat-header { background: #fff; border: 1px solid var(--line); border-radius: 14px 14px 0 0; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }
        .chat-header h2 { margin: 0; font-size: 16px; }
        .chat-header .meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .chat-body { flex: 1; overflow-y: auto; padding: 20px; background: #f8fbfe; border-left: 1px solid var(--line); border-right: 1px solid var(--line); display: flex; flex-direction: column; gap: 14px; }
        .message { display: flex; gap: 10px; max-width: 80%; }
        .message.user { align-self: flex-end; flex-direction: row-reverse; }
        .message.assistant { align-self: flex-start; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .avatar.ai { background: linear-gradient(130deg, #1ba84a, #0e89d8); }
        .avatar.user-av { background: #e2ecf5; }
        .bubble { padding: 12px 16px; border-radius: 14px; font-size: 14px; line-height: 1.55; white-space: pre-wrap; }
        .message.user .bubble { background: linear-gradient(130deg, #1ba84a, #0e89d8); color: #fff; border-bottom-right-radius: 4px; }
        .message.assistant .bubble { background: #fff; border: 1px solid var(--line); border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(14,55,85,.06); }
        .chat-footer { background: #fff; border: 1px solid var(--line); border-top: 0; border-radius: 0 0 14px 14px; padding: 14px 16px; display: flex; gap: 10px; }
        .chat-footer textarea { flex: 1; resize: none; border: 1px solid #c9d7e3; border-radius: 10px; padding: 10px 12px; font-size: 14px; font-family: inherit; min-height: 44px; max-height: 120px; overflow-y: auto; }
        .chat-footer textarea:focus { outline: none; border-color: #0e89d8; box-shadow: 0 0 0 3px rgba(14,137,216,.12); }
        .send-btn { background: linear-gradient(130deg, #1ba84a, #0e89d8); color: #fff; border: none; border-radius: 10px; width: 44px; height: 44px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .send-btn:disabled { opacity: .5; cursor: not-allowed; }
        .attach-btn { background: #f0f5fa; color: #2a5070; border: 1px solid #c9d7e3; border-radius: 10px; width: 44px; height: 44px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .12s; }
        .attach-btn:hover { background: #ddeeff; border-color: #0e89d8; }
        .file-preview-bar { display: none; align-items: center; gap: 10px; padding: 8px 16px; background: #edf6ff; border-left: 1px solid var(--line); border-right: 1px solid var(--line); border-top: 1px solid #c5d8ea; flex-shrink: 0; }
        .file-preview-bar.visible { display: flex; }
        .file-preview-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #b6d7f5; }
        .file-preview-name { font-size: 13px; color: #1d4f73; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .file-preview-remove { background: none; border: none; font-size: 18px; color: #c0392b; cursor: pointer; padding: 0 4px; line-height: 1; }
        .typing { display: none; align-self: flex-start; color: var(--muted); font-size: 13px; padding: 8px 14px; background: #fff; border: 1px solid var(--line); border-radius: 14px; }
        .typing.visible { display: block; }
        .suggestions { display: flex; flex-wrap: wrap; gap: 7px; padding: 10px 16px 0; background: #f8fbfe; border-left: 1px solid var(--line); border-right: 1px solid var(--line); }
        .suggestion-btn { background: #fff; border: 1px solid #c5d8ea; border-radius: 20px; padding: 6px 13px; font-size: 12px; color: #1d4f73; cursor: pointer; transition: all .12s; white-space: nowrap; }
        .suggestion-btn:hover { background: #e8f4ff; border-color: #0e89d8; color: #0e6db8; }
        .suggestions.hidden { display: none; }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 9px; font-size: 13px; font-weight: 600; text-decoration: none; background: rgba(255,255,255,.2); color: #fff; border: 1px solid rgba(255,255,255,.3); }
        .context-chip { font-size: 11px; padding: 2px 8px; border-radius: 20px; background: rgba(255,255,255,.2); color: #fff; }
    </style>

    <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0 10px">
        <a href="{{ route('ai.index') }}" class="btn-back">← {{ __('Conversations') }}</a>
        @php
            $contextLabel = match($conversation->context_type) {
                'energy_audit'            => '⚡ Audyt energetyczny',
                'iso50001'                => '🏭 ISO 50001',
                'offer'                   => '📄 Oferta',
                'compressor_room'         => '🔧 Sprężarkownia',
                'boiler_room'             => '🔥 Kotłownia',
                'drying_room'             => '🌡️ Suszarnia',
                'buildings'               => '🏢 Budynki',
                'technological_processes' => '⚙️ Procesy technologiczne',
                'bc_general'                 => '📋 BC Ogólnie',
                'bc_compressor_room'         => '🔧 BC Sprężarkownia',
                'bc_boiler_room'             => '🔥 BC Kotłownia',
                'bc_drying_room'             => '🌡️ BC Suszarnia',
                'bc_buildings'               => '🏢 BC Budynki',
                'bc_technological_processes' => '⚙️ BC Procesy technologiczne',
                default                   => '💬 Ogólny',
            };
        @endphp
        <span class="context-chip">{{ $contextLabel }}</span>
    </div>

    <div class="chat-layout">
        <div class="chat-header">
            <div>
                <h2>� {{ $conversation->title }}</h2>
                <div class="meta">{{ __('AI Assistant') }} Enesa &middot; Claude AI</div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                @if($conversation->protocol_data)
                    <a href="{{ route('ai.protocol', $conversation) }}"
                       style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;background:#e8f4fe;color:#0e6db8;text-decoration:none;border:1px solid #b6d7f5;">
                        📋 {{ __('Show protocol') }}
                    </a>
                @endif
                <form method="POST" action="{{ route('ai.protocol.generate', $conversation) }}" style="margin:0">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('{{ __('Generate protocol') }}?')"
                            style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;background:linear-gradient(130deg,#1ba84a,#0e89d8);color:#fff;border:none;cursor:pointer;">
                        ⚡ {{ __('Generate protocol') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('ai.force-delete', $conversation) }}" style="margin:0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('{{ __('Delete conversation') }}?')"
                            style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;cursor:pointer;">
                        🗑️ {{ __('Delete conversation') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="chat-body" id="chatBody">
            @foreach($messages as $msg)
                <div class="message {{ $msg->role === 'user' ? 'user' : 'assistant' }}">
                    <div class="avatar {{ $msg->role === 'user' ? 'user-av' : 'ai' }}">
                        {{ $msg->role === 'user' ? '👤' : '�' }}
                    </div>
                    <div class="bubble">{{ $msg->content }}</div>
                </div>
            @endforeach
            <div class="typing" id="typingIndicator">{{ __('AI Assistant') }} ...</div>
        </div>

        <div class="suggestions" id="suggestionsBar">
            @foreach($suggested as $s)
                <button class="suggestion-btn" onclick="useSuggestion(this)">{{ $s }}</button>
            @endforeach
        </div>

        {{-- Hidden file inputs --}}
        <input type="file" id="fileInput" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt,.csv" style="display:none">
        <input type="file" id="cameraInput" accept="image/*" capture="environment" style="display:none">

        {{-- File preview bar (shown when file is selected) --}}
        <div class="file-preview-bar" id="filePreviewBar">
            <img id="filePreviewThumb" class="file-preview-thumb" src="" alt="" style="display:none">
            <span id="filePreviewIcon" style="font-size:28px; display:none">📄</span>
            <span class="file-preview-name" id="filePreviewName"></span>
            <button class="file-preview-remove" onclick="clearFile()" title="Usuń plik">✕</button>
        </div>

        <div class="chat-footer">
            <button class="attach-btn" id="attachBtn" title="Załącz plik (PDF, obraz, CSV, TXT)" onclick="document.getElementById('fileInput').click()">📎</button>
            <button class="attach-btn" id="cameraBtn" title="Zrób zdjęcie aparatem" onclick="document.getElementById('cameraInput').click()">📷</button>
            <textarea
                id="messageInput"
                placeholder="Napisz wiadomość… (Enter = wyślij, Shift+Enter = nowa linia)"
                rows="1"
            ></textarea>
            <button class="send-btn" id="sendBtn" title="Wyślij">➤</button>
        </div>
    </div>

    <script>
        const chatBody        = document.getElementById('chatBody');
        const messageInput    = document.getElementById('messageInput');
        const sendBtn         = document.getElementById('sendBtn');
        const typingIndicator = document.getElementById('typingIndicator');
        const filePreviewBar  = document.getElementById('filePreviewBar');
        const conversationId  = {{ $conversation->id }};
        const csrfToken       = document.querySelector('meta[name="csrf-token"]').content;

        let selectedFile = null;

        function scrollBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
        scrollBottom();

        // File selection helpers
        function onFileSelected(file) {
            if (!file) return;
            selectedFile = file;
            document.getElementById('filePreviewName').textContent = file.name;
            const thumb = document.getElementById('filePreviewThumb');
            const icon  = document.getElementById('filePreviewIcon');
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) { thumb.src = e.target.result; thumb.style.display = ''; icon.style.display = 'none'; };
                reader.readAsDataURL(file);
            } else {
                thumb.style.display = 'none';
                icon.style.display  = '';
                icon.textContent    = file.name.toLowerCase().endsWith('.pdf') ? String.fromCodePoint(0x1F4D5) : String.fromCodePoint(0x1F4C4);
            }
            filePreviewBar.classList.add('visible');
            messageInput.placeholder = 'Opcjonalny komentarz do pliku...';
        }

        function clearFile() {
            selectedFile = null;
            document.getElementById('fileInput').value   = '';
            document.getElementById('cameraInput').value = '';
            filePreviewBar.classList.remove('visible');
            messageInput.placeholder = 'Napisz wiadomosc... (Enter = wyslij, Shift+Enter = nowa linia)';
        }

        document.getElementById('fileInput').addEventListener('change', function(e) {
            if (e.target.files[0]) onFileSelected(e.target.files[0]);
        });
        document.getElementById('cameraInput').addEventListener('change', function(e) {
            if (e.target.files[0]) onFileSelected(e.target.files[0]);
        });

        function addMessage(role, content) {
            const wrap = document.createElement('div');
            wrap.className = 'message ' + (role === 'user' ? 'user' : 'assistant');
            const avatar = role === 'user' ? String.fromCodePoint(0x1F464) : String.fromCodePoint(0x1F916);
            const div = document.createElement('div');
            div.className = 'avatar ' + (role === 'user' ? 'user-av' : 'ai');
            div.textContent = avatar;
            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            bubble.innerHTML = content.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
            wrap.appendChild(div);
            wrap.appendChild(bubble);
            chatBody.insertBefore(wrap, typingIndicator);
            scrollBottom();
        }

        var _rlPending = false;
        var _rlTimer   = null;

        function showRateLimitCountdown(seconds, retryMessage) {
            if (_rlTimer) clearInterval(_rlTimer);
            var rem = seconds;
            _rlPending = true;
            sendBtn.disabled = true;
            messageInput.disabled = true;
            typingIndicator.textContent = 'Limit API \u2014 ponawianie za ' + rem + 's\u2026';
            typingIndicator.classList.add('visible');
            _rlTimer = setInterval(async function () {
                rem--;
                if (rem > 0) {
                    typingIndicator.textContent = 'Limit API \u2014 ponawianie za ' + rem + 's\u2026';
                    return;
                }
                clearInterval(_rlTimer); _rlTimer = null;
                typingIndicator.textContent = 'Asystent AI \u2026';
                messageInput.disabled = false;
                try {
                    const res = await fetch('/ai/' + conversationId + '/wiadomosc', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ message: retryMessage }),
                    });
                    const d = await res.json();
                    typingIndicator.classList.remove('visible');
                    if (d.success) {
                        addMessage('assistant', d.response);
                    } else if (d.rate_limited) {
                        showRateLimitCountdown(d.retry_after || 30, retryMessage);
                        return;
                    } else {
                        addMessage('assistant', '\u26a0\ufe0f ' + (d.error || 'Wyst\u0105pi\u0142 b\u0142\u0105d.'));
                    }
                } catch (e2) {
                    typingIndicator.classList.remove('visible');
                    addMessage('assistant', '\u26a0\ufe0f B\u0142\u0105d po\u0142\u0105czenia. Spr\u00f3buj ponownie.');
                } finally {
                    _rlPending = false;
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                    messageInput.focus();
                }
            }, 1000);
        }

        async function sendMessage() {
            if (selectedFile) { await sendFile(); return; }

            const text = messageInput.value.trim();
            if (!text) return;

            messageInput.value = '';
            messageInput.style.height = 'auto';
            sendBtn.disabled = true;

            addMessage('user', text);
            typingIndicator.classList.add('visible');
            scrollBottom();

            try {
                const res = await fetch('/ai/' + conversationId + '/wiadomosc', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text }),
                });

                const data = await res.json();
                typingIndicator.classList.remove('visible');

                if (data.success) {
                    addMessage('assistant', data.response);
                } else if (data.rate_limited) {
                    showRateLimitCountdown(data.retry_after || 30, text);
                } else {
                    addMessage('assistant', '\u26a0\ufe0f ' + (data.error || 'Wyst\u0105pi\u0142 b\u0142\u0105d.'));
                }
            } catch (e) {
                typingIndicator.classList.remove('visible');
                addMessage('assistant', '\u26a0\ufe0f B\u0142\u0105d po\u0142\u0105czenia. Sprawd\u017a internet i spr\u00f3buj ponownie.');
            } finally {
                if (!_rlPending) {
                    sendBtn.disabled = false;
                    messageInput.focus();
                }
            }
        }

        async function sendFile() {
            const file    = selectedFile;
            const message = messageInput.value.trim();
            if (!file) return;

            const isImg = file.type.startsWith('image/');
            const displayText = message
                ? message + ' [' + file.name + ']'
                : (isImg ? String.fromCodePoint(0x1F4F7) + ' Zdjecie: ' + file.name : String.fromCodePoint(0x1F4CE) + ' Plik: ' + file.name);
            addMessage('user', displayText);

            messageInput.value = '';
            messageInput.style.height = 'auto';
            sendBtn.disabled   = true;
            clearFile();

            typingIndicator.classList.add('visible');
            typingIndicator.textContent = 'Analizuje plik... to moze chwile potrzac.';
            scrollBottom();

            try {
                const formData = new FormData();
                formData.append('file', file);
                if (message) formData.append('message', message);
                formData.append('_token', csrfToken);

                const res = await fetch('/ai/' + conversationId + '/plik', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                });

                const data = await res.json();
                typingIndicator.classList.remove('visible');
                typingIndicator.textContent = 'Asystent AI ...';

                if (data.success) {
                    addMessage('assistant', data.response);
                } else {
                    addMessage('assistant', String.fromCodePoint(0x26A0,0xFE0F) + ' ' + (data.error || 'Blad analizy pliku.'));
                }
            } catch (e) {
                typingIndicator.classList.remove('visible');
                typingIndicator.textContent = 'Asystent AI ...';
                addMessage('assistant', String.fromCodePoint(0x26A0,0xFE0F) + ' Blad polaczenia podczas przesylania pliku.');
            } finally {
                sendBtn.disabled = false;
                messageInput.focus();
            }
        }

        sendBtn.addEventListener('click', sendMessage);

        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        messageInput.addEventListener('input', function() {
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            const bar = document.getElementById('suggestionsBar');
            if (messageInput.value.trim() === '') {
                bar.classList.remove('hidden');
            }
        });

        function useSuggestion(btn) {
            messageInput.value = btn.textContent.trim();
            messageInput.style.height = 'auto';
            messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
            messageInput.focus();
            document.getElementById('suggestionsBar').classList.add('hidden');
        }
    </script>
</x-layouts.app>
